<?php

// 自作した関数を読み込み
require_once __DIR__.'/../lib/functions.php';

// クイズの問題情報一覧を取得
$dataList = fetchAll();

// 一問も問題がなかったら
if (empty($dataList)) {
    // エラーのときの出力
    error404();
}

// クイズの問題一覧を整形してエスケープする
$questions = [];
foreach ($dataList as $data) {
    // クイズの問題1問ずつ整形して、表示する一覧に入れる
    $questions[] = generateFormattedData($data);
}

// HTML内に埋め込む情報を変数にまとめる
$assignData = [
    'questions' => $questions,
];

// 出力
loadTemplate('index', $assignData);
