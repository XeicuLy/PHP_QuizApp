
const answersList = document.querySelectorAll("ol.answers li");

answersList.forEach((li) => li.addEventListener("click", checkClickedAnswer));

/**
 * クイズの解答をクリックしたときの処理
 *
 * @param {Event} event
 */
function checkClickedAnswer(event) {

  const clickedAnswerElement = event.currentTarget;

  const selectedAnswer = clickedAnswerElement.dataset.answer;

  const questionId = clickedAnswerElement.closest("ol.answers").dataset.id;


  const formData = new FormData();
  formData.append("id", questionId);
  formData.append("selectedAnswer", selectedAnswer);


  const xhr = new XMLHttpRequest();
  xhr.open("POST", "answer.php");
  xhr.send(formData);


  xhr.addEventListener("loadend", function (event) {

    const xhr = event.currentTarget;


    if (xhr.status === 200) {



      const response = JSON.parse(xhr.response);


      const result = response.result;
      const correctAnswer = response.correctAnswer;
      const correctAnswerValue = response.correctAnswerValue;
      const explanation = response.explanation;


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
 
  let message;
 
  let answerColorCode;


  if (result) {
  
    message = "正解！すごーい！";
    answerColorCode = "";
  } else {

    message = "ざんねーん！不正解です！おととい来やがれ！";
    answerColorCode = "#f05959";
  }


  alert(message);


  document.querySelector("span#correct-answer").innerHTML =
    correctAnswer + ". " + correctAnswerValue;
  document.querySelector("span#explanation").innerHTML = explanation;


  document.querySelector("span#correct-answer").style.color = answerColorCode;

  document.querySelector("div#section-correct-answer").style.display = "block";
}
