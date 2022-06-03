<?php


function loadTemplate($filename, array $assignData = [])
{
    if ($assignData) {
        extract($assignData);
    }

    include __DIR__ . '/../template/'.$filename.'.tpl.php';
}

/**
 * 404のテンプレートを出力して、終了する
 *
 * @return void
 */
function error404()
{
    header('HTTP/1.1 404 Not Found');

    header('Content-Type: text/html; charset=UTF-8');

    loadTemplate('404');

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
    header('HTTP/1.1 404 Not Found');

    header('Content-Type: application/json; charset=UTF-8');

    echo json_encode($response);

    exit(0);
}

/**
 * クイズのすべての問題を取得
 *
 * @return array すべての問題の配列
 */
function fetchAll()
{
    $questions = [];

    $handle = fopen(__DIR__.'/data.csv', 'r');

    if ($handle === false) {
        return $questions;
    }

    while ($row = fgetcsv($handle)) {
        if (isDataRow($row)) {
            $questions[] = $row;
        }
    }

    fclose($handle);

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
    foreach (fetchAll() as $row) {
        if ($row[0] === $id) {
            return $row;
        }
    }

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
    if (count($row) !== 8) {
        return false;
    }

    foreach ($row as $value) {
        if (empty($value)) {
            return false;
        }
    }

    if (!is_numeric($row[0])) {
        return false;
    }

    $correctAnswer = strtoupper($row[6]);
    $availableAnswers = ['A', 'B', 'C', 'D'];
    if (!in_array($correctAnswer, $availableAnswers)) {
        return false;
    }

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
    $convertedData = htmlspecialchars($data, ENT_HTML5);

    if ($nl2br) {
        return nl2br($convertedData);
    }

    return $convertedData;
}
