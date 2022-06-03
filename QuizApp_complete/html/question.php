<?php

require_once __DIR__.'/../lib/functions.php';


$id = $_GET['id'] ?? '';


$data = fetchById($id);


if (empty($data)) {

    error404();
}


$formattedData = generateFormattedData($data);


$assignData = [
    'id' => escape($id),
    'question' => $formattedData['question'],
    'answers' => $formattedData['answers'],
];


loadTemplate('question', $assignData);
