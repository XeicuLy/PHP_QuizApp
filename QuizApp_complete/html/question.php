<?php

// 自作した関数を読み込み
require_once __DIR__.'/../lib/functions.php';

// 入力値を変数に入れる
$id = $_GET['id'] ?? '';

// クイズの問題情報を取得
$data = fetchById($id);

// データが正しい状態か確認する
if (empty($data)) {
    // エラーのときの出力
    error404();
}

// データを操作しやすい形に作り変える
$formattedData = generateFormattedData($data);

// HTML内に埋め込む情報を変数にまとめる
$assignData = [
    'id' => escape($id),
    'question' => $formattedData['question'],
    'answers' => $formattedData['answers'],
];

// 出力
loadTemplate('question', $assignData);
