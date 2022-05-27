<?php

/**
 * テンプレートを読み込み
 *
 * @param string $filename ファイル名
 * @param array $assignData 出力したい値の配列
 *
 * @return void
 */
function loadTemplate($filename, array $assignData = [])
{
    // 出力したい値が渡されている場合のみ
    if ($assignData) {
        // 配列のキーの変数名を用意して、変数の中には配列を値を設定する
        // 例: extract(['apple' => 'りんご']); // $apple = 'りんご'; の意味になる
        extract($assignData);
    }

    // テンプレートのファイルを読み込む
    include __DIR__ . '/../template/'.$filename.'.tpl.php';
}

/**
 * 404のテンプレートを出力して、終了する
 *
 * @return void
 */
function error404()
{
    // HTTPレスポンスのヘッダを404にする
    header('HTTP/1.1 404 Not Found');

    // レスポンスの種類を指定する
    header('Content-Type: text/html; charset=UTF-8');

    // 404ページを出力
    loadTemplate('404');

    // PHPスクリプトを終了(0は正常に終了)
    exit(0);
}

/**
 * 404のJsonを出力して、終了する
 *
 * @param mixed $response 出力したいデータ
 *
 * @return void
 */
function error404Json($response)
{
    // HTTPレスポンスのヘッダを404にする
    header('HTTP/1.1 404 Not Found');

    // レスポンスの種類を指定する
    header('Content-Type: application/json; charset=UTF-8');

    // jsonを出力
    echo json_encode($response);

    // PHPスクリプトを終了(0は正常に終了)
    exit(0);
}

/**
 * クイズのすべての問題を取得
 *
 * @return array すべての問題の配列
 */
function fetchAll()
{
    // クイズの問題の情報一覧をを保存する入れ物を用意
    $questions = [];

    // ファイル操作の準備をする(r: 読み込み専用)
    $handle = fopen(__DIR__.'/data.csv', 'r');

    // ファイルが操作できるか判定
    if ($handle === false) {
        // 操作できないときは空を返す
        return $questions;
    }

    // ファイルの中身を1行ずつ取得する
    while ($row = fgetcsv($handle)) {
        // クイズの問題データ以外は無視する
        if (isDataRow($row)) {
            // クイズの問題だけを配列に追加する
            $questions[] = $row;
        }
    }

    // ファイルの操作を終了する
    fclose($handle);

    // 取得できた値を返す
    return $questions;
}

/**
 * 指定されたIDのクイズの問題を取得
 *
 * @param string $id クイズのID
 *
 * @return array クイズの問題
 */
function fetchById($id)
{
    // (毎回全部のデータを取得するのでベストな実装ではない)
    foreach (fetchAll() as $row) {
        // 指定されたIDと一致するか確認
        if ($row[0] === $id) {
            // 一致した行を返す
            return $row;
        }
    }

    // IDがヒットしなかったら空を返す
    return [];
}

/**
 * クイズの問題データの行か判定
 *
 * @param array $row csvファイルの1行分のデータ
 *
 * @return bool クイズのデータの場合はtrue/クイズのデータでなければfalse
 */
function isDataRow(array $row)
{
    // データの項目数が足りているか判定
    if (count($row) !== 8) {
        return false;
    }

    // データの項目の中身がすべて埋まっているか確認する
    foreach ($row as $value) {
        // 項目の値が空か判定
        if (empty($value)) {
            return false;
        }
    }

    // idの項目が数字ではない場合は無視する
    if (!is_numeric($row[0])) {
        return false;
    }

    // 正しい答えはa,b,c,dのどれか
    $correctAnswer = strtoupper($row[6]);
    $availableAnswers = ['A', 'B', 'C', 'D'];
    if (!in_array($correctAnswer, $availableAnswers)) {
        return false;
    }

    // すべてチェックが問題なければtrue
    return true;
}

/**
 * 取得できたクイズのデータ1行を利用しやすいように連想配列に変換
 * 値をHTMLに組み込めるようにエスケープも行う
 *
 * @param array $data クイズ情報(1問分)
 *
 * @return array 整形したクイズの情報
 */
function generateFormattedData($data)
{
    // 構造化した配列を作成する
    $formattedData = [
        'id' => escape($data[0]),
        'question' => escape($data[1], true),
        'answers' => [
            'A' => escape($data[2]),
            'B' => escape($data[3]),
            'C' => escape($data[4]),
            'D' => escape($data[5]),
        ],
        'correctAnswer' => escape(strtoupper($data[6])),
        'explanation' => escape($data[7], true),
    ];

    return $formattedData;
}

/**
 * HTMLに組み込むために必要なエスケープ処理を行う
 *
 * @param string $data エスケープしたい情報
 * @param bool $nl2br 改行を<br>に変換する場合はtrue
 *
 * @return string エスケープ済の文字列
 */
function escape($data, $nl2br = false)
{
    // HTMLに埋め込んでも大丈夫な文字に変換する
    $convertedData = htmlspecialchars($data, ENT_HTML5);

    // 改行コードを<br>タグに変換するか判定
    if ($nl2br) {
        /// 改行コードを<br>タグに変換したものをを返却
        return nl2br($convertedData);
    }

    return $convertedData;
}
