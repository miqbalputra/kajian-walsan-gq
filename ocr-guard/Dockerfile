FROM python:3.11-slim

ENV PYTHONDONTWRITEBYTECODE=1 \
    PYTHONUNBUFFERED=1 \
    OMP_NUM_THREADS=1 \
    OPENBLAS_NUM_THREADS=1 \
    MKL_NUM_THREADS=1 \
    NUMEXPR_NUM_THREADS=1

WORKDIR /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends ca-certificates curl libglib2.0-0 libgomp1 \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt ./
RUN pip install --no-cache-dir --disable-pip-version-check -r requirements.txt

# RapidOCR tidak membundel ONNX di wheel. Pin model PP-OCRv6 small dan
# classifier pada build image, lalu verifikasi checksum agar runtime offline.
ENV OCR_GUARD_MODEL_ROOT=/opt/ocr-models
RUN mkdir -p "${OCR_GUARD_MODEL_ROOT}" \
    && curl -fsSL --retry 3 --retry-all-errors \
        https://www.modelscope.cn/models/RapidAI/RapidOCR/resolve/v3.9.2/onnx/PP-OCRv6/det/PP-OCRv6_det_small.onnx \
        -o "${OCR_GUARD_MODEL_ROOT}/PP-OCRv6_det_small.onnx" \
    && echo "090f04abcd9d9a7498bc4ebf677e4cb9bdce1fe4197ddb7e529f1ef44e1ff94f  ${OCR_GUARD_MODEL_ROOT}/PP-OCRv6_det_small.onnx" | sha256sum -c - \
    && curl -fsSL --retry 3 --retry-all-errors \
        https://www.modelscope.cn/models/RapidAI/RapidOCR/resolve/v3.9.2/onnx/PP-OCRv6/rec/PP-OCRv6_rec_small.onnx \
        -o "${OCR_GUARD_MODEL_ROOT}/PP-OCRv6_rec_small.onnx" \
    && echo "6f327246b50388f3c176ae304bd95767ea6dc0c9ae92153ef8cbe210b3c14884  ${OCR_GUARD_MODEL_ROOT}/PP-OCRv6_rec_small.onnx" | sha256sum -c - \
    && curl -fsSL --retry 3 --retry-all-errors \
        https://www.modelscope.cn/models/RapidAI/RapidOCR/resolve/v3.9.2/onnx/PP-OCRv4/cls/ch_ppocr_mobile_v2.0_cls_mobile.onnx \
        -o "${OCR_GUARD_MODEL_ROOT}/ch_ppocr_mobile_v2.0_cls_mobile.onnx" \
    && echo "e47acedf663230f8863ff1ab0e64dd2d82b838fceb5957146dab185a89d6215c  ${OCR_GUARD_MODEL_ROOT}/ch_ppocr_mobile_v2.0_cls_mobile.onnx" | sha256sum -c -

COPY app.py ./

EXPOSE 9005

CMD ["uvicorn", "app:app", "--host", "0.0.0.0", "--port", "9005", "--workers", "1"]
