/**
 * Google Form Presensi Mustawa 1 -> aplikasi Presensi Wali Santri.
 *
 * Cara pakai singkat:
 * 1. Buat project Apps Script dari script.google.com.
 * 2. Isi APP_URL dan WEBHOOK_SECRET di CONFIG.
 * 3. Jalankan createOrConfigureForm() sekali dan izinkan akses.
 * 4. Bagikan URL yang tercetak pada log Google Apps Script.
 */
const CONFIG = {
  APP_URL: 'https://kajian.griyaquran.web.id',
  WEBHOOK_SECRET: 'GANTI_DENGAN_SECRET_YANG_SAMA_DI_COOLIFY',
  FORM_ID: '',
};

const QUESTIONS = {
  eventDate: 'Tanggal kajian',
  status: 'Status pengajuan',
  student: 'Nama ananda Mustawa 1',
  parentType: 'Jenis wali',
  parentName: 'Nama Bapak/Ibu',
  parentPhone: 'Nomor HP',
  notes: 'Catatan atau alasan',
  confirmation: 'Konfirmasi data',
};

function createOrConfigureForm() {
  let formId = PropertiesService.getScriptProperties().getProperty('FORM_ID') || CONFIG.FORM_ID;
  const form = formId
    ? FormApp.openById(formId)
    : FormApp.create('Presensi Kajian Wali Santri - Mustawa 1');

  PropertiesService.getScriptProperties().setProperty('FORM_ID', form.getId());
  ensureQuestions_(form);
  syncMustawa1Students();
  installSubmitTrigger_(form);

  Logger.log('FORM_ID: ' + form.getId());
  Logger.log('LINK WALI: ' + form.getPublishedUrl());
  Logger.log('LINK EDITOR: ' + form.getEditUrl());
}

function ensureQuestions_(form) {
  getOrAddDateItem_(form, QUESTIONS.eventDate).setRequired(true);
  getOrAddMultipleChoiceItem_(form, QUESTIONS.status)
    .setChoiceValues(['Menyimak online', 'Berhalangan tidak hadir'])
    .setRequired(true);
  getOrAddListItem_(form, QUESTIONS.student).setRequired(true);
  getOrAddMultipleChoiceItem_(form, QUESTIONS.parentType)
    .setChoiceValues(['Bapak', 'Ibu'])
    .setRequired(true);
  getOrAddTextItem_(form, QUESTIONS.parentName).setRequired(true);
  getOrAddTextItem_(form, QUESTIONS.parentPhone).setRequired(true);
  getOrAddParagraphItem_(form, QUESTIONS.notes)
    .setHelpText('Wajib diisi jika berhalangan. Untuk menyimak online boleh dikosongkan.')
    .setRequired(false);
  getOrAddCheckboxItem_(form, QUESTIONS.confirmation)
    .setChoiceValues(['Saya memastikan data yang diisi benar.'])
    .setRequired(true);
}

function syncMustawa1Students() {
  const form = getForm_();
  const response = fetchApp_('/api/integrations/google-forms/mustawa-1/options', '');
  const item = getOrAddListItem_(form, QUESTIONS.student);
  const choices = (response.students || []).map((student) => student.label);

  if (choices.length === 0) {
    throw new Error('Aplikasi belum memiliki santri Mustawa 1 aktif.');
  }

  item.setChoiceValues(choices).setRequired(true);
  Logger.log('Pilihan santri diperbarui: ' + choices.length);
}

function installSubmitTrigger_(form) {
  ScriptApp.getProjectTriggers()
    .filter((trigger) => trigger.getHandlerFunction() === 'onFormSubmit')
    .forEach((trigger) => ScriptApp.deleteTrigger(trigger));

  ScriptApp.newTrigger('onFormSubmit')
    .forForm(form)
    .onFormSubmit()
    .create();
}

function onFormSubmit(event) {
  const response = event.response;
  const answers = {};

  response.getItemResponses().forEach((itemResponse) => {
    answers[itemResponse.getItem().getTitle()] = responseValue_(itemResponse.getResponse());
  });

  const studentLabel = answers[QUESTIONS.student] || '';
  const studentReference = studentLabel.split('—').pop().trim();
  const studentName = studentLabel.replace(/\s*—\s*[^—]+$/, '').trim();
  const statusLabel = answers[QUESTIONS.status] || '';

  const payload = {
    response_id: response.getId(),
    form_id: getForm_().getId(),
    submitted_at: response.getTimestamp().toISOString(),
    event_date: normalizeDate_(answers[QUESTIONS.eventDate]),
    status: statusLabel === 'Berhalangan tidak hadir' ? 'izin' : 'hadir_online',
    student_reference: studentReference,
    student_name: studentName,
    parent_type: answers[QUESTIONS.parentType] === 'Bapak' ? 'father' : 'mother',
    parent_name: answers[QUESTIONS.parentName],
    parent_phone: answers[QUESTIONS.parentPhone],
    notes: answers[QUESTIONS.notes] || '',
  };

  const body = JSON.stringify(payload);
  const result = UrlFetchApp.fetch(CONFIG.APP_URL + '/api/integrations/google-forms/mustawa-1', {
    method: 'post',
    contentType: 'application/json',
    payload: body,
    headers: { 'X-Google-Form-Signature': hmacHex_(body) },
    muteHttpExceptions: true,
  });

  Logger.log('Submit response ' + result.getResponseCode() + ': ' + result.getContentText());
}

function fetchApp_(path, body) {
  const result = UrlFetchApp.fetch(CONFIG.APP_URL + path, {
    method: 'get',
    headers: { 'X-Google-Form-Signature': hmacHex_(body) },
    muteHttpExceptions: true,
  });

  const code = result.getResponseCode();
  if (code < 200 || code >= 300) {
    throw new Error('Aplikasi mengembalikan HTTP ' + code + ': ' + result.getContentText());
  }

  return JSON.parse(result.getContentText());
}

function getForm_() {
  const formId = PropertiesService.getScriptProperties().getProperty('FORM_ID') || CONFIG.FORM_ID;
  if (!formId) {
    throw new Error('FORM_ID belum tersedia. Jalankan createOrConfigureForm() terlebih dahulu.');
  }

  return FormApp.openById(formId);
}

function hmacHex_(value) {
  const bytes = Utilities.computeHmacSha256Signature(value, CONFIG.WEBHOOK_SECRET);
  return bytes.map((byte) => {
    const unsigned = byte < 0 ? byte + 256 : byte;
    return ('0' + unsigned.toString(16)).slice(-2);
  }).join('');
}

function responseValue_(value) {
  return Array.isArray(value) ? value.join(', ') : String(value || '').trim();
}

function normalizeDate_(value) {
  const date = new Date(value);
  if (isNaN(date.getTime())) {
    return value;
  }

  return Utilities.formatDate(date, Session.getScriptTimeZone(), 'yyyy-MM-dd');
}

function findItem_(form, title) {
  return form.getItems().find((item) => item.getTitle() === title) || null;
}

function getOrAddDateItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asDateItem() : form.addDateItem().setTitle(title);
}

function getOrAddMultipleChoiceItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asMultipleChoiceItem() : form.addMultipleChoiceItem().setTitle(title);
}

function getOrAddListItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asListItem() : form.addListItem().setTitle(title);
}

function getOrAddTextItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asTextItem() : form.addTextItem().setTitle(title);
}

function getOrAddParagraphItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asParagraphTextItem() : form.addParagraphTextItem().setTitle(title);
}

function getOrAddCheckboxItem_(form, title) {
  const existing = findItem_(form, title);
  return existing ? existing.asCheckboxItem() : form.addCheckboxItem().setTitle(title);
}
