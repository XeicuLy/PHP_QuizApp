<?php

require_once __DIR__.'/../lib/functions.php';


$id = $_POST['id'] ?? '';
$selectedAnswer = $_POST['selectedAnswer'] ?? '';


$data = fetchById($id);


if (empty($data)) {

    $response = [
        'message' => 'The specified id could not be found',
    ];
    error404Json($response);
}

$formattedData = generateFormattedData($data);


$correctAnswer = $formattedData['correctAnswer'];
$correctAnswerValue = $formattedData['answers'][$correctAnswer];
$explanation = $formattedData['explanation'];


$result = $selectedAnswer == $correctAnswer;


$response = [
    'result' => $result,
    'correctAnswer' => $correctAnswer,
    'correctAnswerValue' => $correctAnswerValue,
    'explanation' => $explanation,
];


echo json_encode($response);
