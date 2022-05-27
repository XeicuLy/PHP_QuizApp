// 解答の選択肢一覧を取得
const answersList = document.querySelectorAll("ol.answers li");
// クリックされたときの処理を仕込む
answersList.forEach((li) => li.addEventListener("click", checkClickedAnswer));

/**
 * クイズの解答をクリックしたときの処理
 *
 * @param {Event} event
 */
function checkClickedAnswer(event) {
  // addEventListenerによってイベント検知した対象を取得(この実装ではli要素)
  const clickedAnswerElement = event.currentTarget;
  // 選択した答え(A,B,C,D)
  const selectedAnswer = clickedAnswerElement.dataset.answer;
  // 親要素のolから、data-idの値を取得
  const questionId = clickedAnswerElement.closest("ol.answers").dataset.id;

  // 送信するデータを作成
  const formData = new FormData();
  formData.append("id", questionId);
  formData.append("selectedAnswer", selectedAnswer);

  // リクエスト
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "answer.php");
  xhr.send(formData);

  // 読み込みが終わったときのイベントを追加
  xhr.addEventListener("loadend", function (event) {
    // addEventListenerによってイベント検知した対象を取得(この実装ではXMLHttpRequest)
    /** @type {XMLHttpRequest} */
    const xhr = event.currentTarget;

    // リクエストが成功したかステータスコードで確認(200は成功)
    if (xhr.status === 200) {
      // リクエストが成功したとき

      // レスポンスの値をJavaScriptで利用できるように準備
      const response = JSON.parse(xhr.response);

      // レスポンスの値をわかりやすい変数に代入
      const result = response.result;
      const correctAnswer = response.correctAnswer;
      const correctAnswerValue = response.correctAnswerValue;
      const explanation = response.explanation;

      // 表示処理
      displayResult(result, correctAnswer, correctAnswerValue, explanation);
    } else {
      alert("Error: 解答データの取得に失敗しました");
    }
  });
}

/**
 * 結果の表示
 *
 * @param {string} result
 * @param {string} correctAnswer
 * @param {string} correctAnswerValue
 * @param {string} explanation
 */
function displayResult(result, correctAnswer, correctAnswerValue, explanation) {
  // メッセージを入れる変数を用意
  let message;
  // カラーコードを入れる変数を用意
  let answerColorCode;

  // 答えが正しいか判定
  if (result) {
    // 正しい答えだったとき
    message = "正解！すごーい！";
    answerColorCode = "";
  } else {
    // 間違えた答えだったとき
    message = "ざんねーん！不正解です！おととい来やがれ！";
    answerColorCode = "#f05959";
  }

  // アラートで正解・不正解を出力
  alert(message);

  // 正解の内容をHTMLに埋め込む
  document.querySelector("span#correct-answer").innerHTML =
    correctAnswer + ". " + correctAnswerValue;
  document.querySelector("span#explanation").innerHTML = explanation;

  // 色を変更(間違っていたときだけ色が変わる)
  document.querySelector("span#correct-answer").style.color = answerColorCode;
  // 答え全体を表示
  document.querySelector("div#section-correct-answer").style.display = "block";
}
