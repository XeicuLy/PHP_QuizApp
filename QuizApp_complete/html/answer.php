<?php

// 自作した関数を読み込み
require_once __DIR__.'/../lib/functions.php';

// 入力値を変数に入れる
$id = $_POST['id'] ?? '';
$selectedAnswer = $_POST['selectedAnswer'] ?? '';

// クイズの問題情報を取得
$data = fetchById($id);

// データが正しい状態か確認する
if (empty($data)) {
    // エラーのとき
    $response = [
        'message' => 'The specified id could not be found',
    ];
    error404Json($response);
}

// データを操作しやすい形に作り変える
$formattedData = generateFormattedData($data);

// 変数を利用しやすい変数に入れる
$correctAnswer = $formattedData['correctAnswer'];
$correctAnswerValue = $formattedData['answers'][$correctAnswer];
$explanation = $formattedData['explanation'];

// 選択した答えが正しいか判定して、結果を$resultに入れる
$result = $selectedAnswer == $correctAnswer;

// レスポンスする情報を変数にまとめる
$response = [
    'result' => $result,
    'correctAnswer' => $correctAnswer,
    'correctAnswerValue' => $correctAnswerValue,
    'explanation' => $explanation,
];

// レスポンスボディを出力（JSON）
echo json_encode($response);
