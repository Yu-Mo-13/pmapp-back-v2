function main() {
  // パスワード変更促進通知
  let lineText = '';
  const res = UrlFetchApp.fetch(`${PropertiesService.getScriptProperties().getProperty('ROOT')}`);
  const data = JSON.parse(res.getContentText());

  for (i = 0; i < data.length; i++) {
    const rec = data[i];
    // LINE文面作成
    lineText = `アプリ名:${rec.application.name}`
    if (rec.account) {
      lineText += `\nアカウント名:${rec.account.name}`
    }
    // Mobile パスワード仮登録ページURL
    lineText += `\nURL: ${PropertiesService.getScriptProperties().getProperty('MOBILEURL')}%2Ftemp-passwords%2Fcreate%3Fapplication_id%3D${rec.application.id}${rec.account ? `%26account_id%3D${rec.account.id}` : ''}`
    SendLine(`パスワードを設定してから${PropertiesService.getScriptProperties().getProperty('UPDATELIMITDAYS')}日が経過しました。\n${lineText}\nパスワードを変更してください。`)
  }
  // destroyAutoRegistPassword();
  // SendLine('本登録されていないパスワードを削除しました。パスワードの変更を登録する場合は、再度パスワードの仮登録を行ってください。')
  SendLine('[dev環境]パスワード変更促進通知終了');
}

function convertDate(registered_date) {
  return Utilities.parseDate(registered_date, 'JST', 'yyyy-MM-dd')
}

// 作成済みの仮登録パスワードを全て削除
function destroyAutoRegistPassword() {
  const url = `${PropertiesService.getScriptProperties().getProperty('ROOT')}${PropertiesService.getScriptProperties().getProperty('AUTOREGISTENDPOINT')}`;
  const options = {
    "method": "delete",
    "headers": {
      "Content-Type": "application/json",
    }
  }
  UrlFetchApp.fetch(url, options);
}

// LINE Messaging APIでの通知
function SendLine(content) {
  const token = PropertiesService.getScriptProperties().getProperty('LINETOKEN');
  const url = PropertiesService.getScriptProperties().getProperty('LINEURL');
  const userId = PropertiesService.getScriptProperties().getProperty('LINEUSERID');
  const payload = {
    to: userId,
    messages: [{
      type: 'text',
      text: content
    }]
  }
  const options = {
    "method" : "post",
    "headers" : {
      "Content-Type" : "application/json",
      "Authorization" : "Bearer " + token
    },
    "payload": JSON.stringify(payload)
  };
  UrlFetchApp.fetch(url, options);
}
