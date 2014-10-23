<?php

use SAR\models\Login;

$app->get('/login', function () use ($app) {
    $app->render('pages/_login.twig');
});

$app->post('/login', function () use ($app) {
    var_dump($app->request->post());
    $userId = $app->request->post('user');
    $password = $app->request->post('password');
    echo $userId . ":" . $password;
    $auth = new Login();
    echo $auth->authenticate($userId, $password);
});
