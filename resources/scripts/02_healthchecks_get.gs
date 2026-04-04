/**
 * Supabase DB healthcheck status API を実行する。
 *
 * Script Properties に `PMAPP_API_BASE_URL` を設定すること。
 * 例: https://example.com
 */
function getHealthcheckStatus() {
  var baseUrl = getPmappApiBaseUrl_();
  var endpoint = baseUrl.replace(/\/$/, '') + '/api/v2/healthchecks/status';

  var response = UrlFetchApp.fetch(endpoint, {
    method: 'get',
    contentType: 'application/json',
    muteHttpExceptions: true,
  });

  var bodyText = JSON.parse(response.getContentText());

  if (!bodyText.is_healthy) {
    return postToSlackApp('DBヘルスチェックに失敗しました。: ' + bodyText.message)
  }

  return postToSlackApp(bodyText.message);
}

function getPmappApiBaseUrl_() {
  var baseUrl = PropertiesService.getScriptProperties().getProperty('PMAPP_API_BASE_URL');

  if (!baseUrl) {
    throw new Error('Script Properties に PMAPP_API_BASE_URL を設定してください。');
  }

  return baseUrl;
}

function postToSlackApp(content){
  let status = false;
  const token = PropertiesService.getScriptProperties().getProperty('SLACK_TOKEN');
  const slackApp = SlackApp.create(token);
  const channelId = "#alert";
  if (slackApp.chatPostMessage(channelId, '【dev環境】 ' + content)){
    status = true;
  }
  return status;
}
