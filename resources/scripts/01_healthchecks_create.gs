/**
 * Supabase DB healthcheck create API を実行する。
 *
 * Script Properties に `PMAPP_API_BASE_URL` を設定すること。
 * 例: https://example.com
 */
function createHealthcheck() {
  var baseUrl = getPmappApiBaseUrl_();
  var endpoint = baseUrl.replace(/\/$/, '') + '/api/v2/healthchecks';

  var response = UrlFetchApp.fetch(endpoint, {
    method: 'post',
    contentType: 'application/json',
    muteHttpExceptions: true,
  });

  var bodyText = response.getContentText();

  return JSON.parse(bodyText);
}

function getPmappApiBaseUrl_() {
  var baseUrl = PropertiesService.getScriptProperties().getProperty('PMAPP_API_BASE_URL');

  if (!baseUrl) {
    throw new Error('Script Properties に PMAPP_API_BASE_URL を設定してください。');
  }

  return baseUrl;
}
