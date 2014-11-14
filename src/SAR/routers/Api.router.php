<?php

use SAR\models\Silabus;
use SAR\models\Kategori;

$app->get('/api/:idMatkul/silabus/', function ($idMatkul) use ($app) {
    $silabus = new Silabus();
    $result = $silabus->init($idMatkul);
    $app->response()->header('Content-Type', 'application/json');
    $response = array(
        'id' => $silabus->silabusID,
        'tujuan' => $silabus->tujuan,
        'kompetensi' => $silabus->kompetensi,
        'pokokBahasan' => $silabus->pokokBahasan,
        'pustaka' => $silabus->pustaka
    );
    echo(json_encode($response));
});

$app->post('/api/:idMatkul/silabus/', function ($idMatkul) use ($app) {
    $app->response()->header('Content-Type', 'application/json');
    $body = $app->request()->post();
    echo(json_encode($body));
});
