import hashlib
import hmac
import io
import os
from pathlib import Path
from typing import Any

import numpy as np
from fastapi import FastAPI, File, Header, HTTPException, UploadFile
from PIL import Image, ImageOps, UnidentifiedImageError
from rapidocr import EngineType, LangCls, LangDet, ModelType, OCRVersion, RapidOCR


MODEL_NAME = os.getenv("OCR_GUARD_MODEL", "PP-OCRv6-small-id")
TOKEN = os.getenv("OCR_GUARD_TOKEN", "")
MAX_BYTES = int(os.getenv("OCR_GUARD_MAX_BYTES", str(4 * 1024 * 1024)))
MAX_SIDE = int(os.getenv("OCR_GUARD_MAX_SIDE", "1600"))
MODEL_ROOT = Path(os.getenv("OCR_GUARD_MODEL_ROOT", "/opt/ocr-models"))
DET_MODEL_PATH = MODEL_ROOT / "PP-OCRv6_det_small.onnx"
REC_MODEL_PATH = MODEL_ROOT / "PP-OCRv6_rec_small.onnx"
CLS_MODEL_PATH = MODEL_ROOT / "ch_ppocr_mobile_v2.0_cls_mobile.onnx"

app = FastAPI(title="Kajian Walsan OCR Guard", version="1.0.0")


def authenticate(authorization: str | None, x_token: str | None) -> None:
    if not TOKEN:
        return

    bearer = ""
    if authorization and authorization.lower().startswith("bearer "):
        bearer = authorization[7:].strip()

    supplied = x_token or bearer
    if not supplied or not hmac.compare_digest(supplied, TOKEN):
        raise HTTPException(status_code=401, detail="Unauthorized")


def create_engine() -> RapidOCR:
    missing_models = [
        str(path) for path in (DET_MODEL_PATH, REC_MODEL_PATH, CLS_MODEL_PATH)
        if not path.is_file()
    ]
    if missing_models:
        raise RuntimeError(f"OCR model files are missing: {', '.join(missing_models)}")

    # PP-OCRv6 small memakai model multilingual untuk deteksi dan recognition.
    # Bahasa id dipilih melalui route model, sedangkan classifier orientasi
    # tetap menggunakan classifier PP-OCRv4 yang kompatibel.
    return RapidOCR(
        params={
            "Global.log_level": "critical",
            "Global.text_score": 0.50,
            "Global.max_side_len": MAX_SIDE,
            "EngineConfig.onnxruntime.intra_op_num_threads": 1,
            "EngineConfig.onnxruntime.inter_op_num_threads": 1,
            "EngineConfig.onnxruntime.enable_cpu_mem_arena": False,
            "Det.engine_type": EngineType.ONNXRUNTIME,
            "Det.lang_type": LangDet.MULTI,
            "Det.model_type": ModelType.SMALL,
            "Det.ocr_version": OCRVersion.PPOCRV6,
            "Det.model_path": str(DET_MODEL_PATH),
            "Cls.engine_type": EngineType.ONNXRUNTIME,
            "Cls.lang_type": LangCls.CH,
            "Cls.model_type": ModelType.MOBILE,
            "Cls.ocr_version": OCRVersion.PPOCRV4,
            "Cls.model_path": str(CLS_MODEL_PATH),
            "Rec.engine_type": EngineType.ONNXRUNTIME,
            "Rec.lang_type": "id",
            "Rec.model_type": ModelType.SMALL,
            "Rec.ocr_version": OCRVersion.PPOCRV6,
            "Rec.model_path": str(REC_MODEL_PATH),
        }
    )


engine = create_engine()


def image_precheck(data: bytes) -> tuple[Image.Image, dict[str, Any] | None]:
    if len(data) == 0:
        raise HTTPException(status_code=400, detail="Empty image")
    if len(data) > MAX_BYTES:
        raise HTTPException(status_code=413, detail="Image too large")

    try:
        image = Image.open(io.BytesIO(data))
        image.load()
    except (UnidentifiedImageError, OSError) as exc:
        raise HTTPException(status_code=400, detail="Invalid image") from exc

    width, height = image.size
    if min(width, height) < 200:
        return image, {
            "document_signal": "none",
            "text_chars": 0,
            "text_boxes": 0,
            "ocr_confidence": 100,
            "language": "id",
            "reason_code": "low_resolution",
            "raw_text": "",
            "model": MODEL_NAME,
            "image_width": width,
            "image_height": height,
        }

    grayscale = np.asarray(image.convert("L"), dtype=np.float32)
    if grayscale.size and float(grayscale.std()) < 3.0:
        return image, {
            "document_signal": "none",
            "text_chars": 0,
            "text_boxes": 0,
            "ocr_confidence": 100,
            "language": "id",
            "reason_code": "blank_image",
            "raw_text": "",
            "model": MODEL_NAME,
            "image_width": width,
            "image_height": height,
        }

    return image, None


def run_ocr(data: bytes) -> dict[str, Any]:
    image, early = image_precheck(data)
    if early:
        return early

    # PIL normalizes EXIF orientation before handing bytes to RapidOCR.
    image = ImageOps.exif_transpose(image).convert("RGB")
    output = engine(np.asarray(image))

    boxes = getattr(output, "boxes", None)
    texts = getattr(output, "txts", None)
    scores = getattr(output, "scores", None)
    if boxes is None or texts is None or scores is None:
        return {
            "document_signal": "none",
            "text_chars": 0,
            "text_boxes": 0,
            "ocr_confidence": 0,
            "language": "id",
            "reason_code": "no_text",
            "raw_text": "",
            "model": MODEL_NAME,
            "image_width": image.width,
            "image_height": image.height,
        }

    pairs = [
        (str(text).strip(), float(score))
        for text, score in zip(texts, scores)
        if str(text).strip()
    ]
    text_values = [text for text, _score in pairs]
    score_values = [score for _text, score in pairs]
    text_chars = len(" ".join(text_values))
    text_boxes = len(text_values)
    confidence = int(round((sum(score_values) / len(score_values)) * 100)) if score_values else 0

    if text_boxes >= 2 and text_chars >= 20 and confidence >= 70:
        signal = "strong"
        reason_code = "text_document_detected"
    elif text_boxes > 0:
        signal = "weak"
        reason_code = "low_ocr_confidence"
    else:
        signal = "none"
        reason_code = "no_text"

    return {
        "document_signal": signal,
        "text_chars": text_chars,
        "text_boxes": text_boxes,
        "ocr_confidence": max(0, min(100, confidence)),
        "language": "id",
        "reason_code": reason_code,
        "raw_text": " ".join(text_values)[:1000],
        "model": MODEL_NAME,
        "image_width": image.width,
        "image_height": image.height,
    }


@app.get("/health")
def health() -> dict[str, str]:
    return {"status": "ok", "model": MODEL_NAME}


@app.post("/v1/check-document")
async def check_document(
    image_file: UploadFile = File(...),
    expected_document: str = "kajian_note",
    language: str = "id",
    authorization: str | None = Header(default=None),
    x_ocr_guard_token: str | None = Header(default=None),
) -> dict[str, Any]:
    authenticate(authorization, x_ocr_guard_token)

    if not (image_file.content_type or "").startswith("image/"):
        raise HTTPException(status_code=415, detail="Only image uploads are supported")

    data = await image_file.read()
    try:
        result = run_ocr(data)
    except HTTPException as exc:
        if exc.status_code in (400, 413):
            result = {
                "document_signal": "none",
                "text_chars": 0,
                "text_boxes": 0,
                "ocr_confidence": 100,
                "language": "id",
                "reason_code": "invalid_image",
                "raw_text": "",
                "model": MODEL_NAME,
                "image_width": 0,
                "image_height": 0,
            }
        else:
            raise
    result["expected_document"] = expected_document
    result["requested_language"] = language
    result["sha256"] = hashlib.sha256(data).hexdigest()
    return result
