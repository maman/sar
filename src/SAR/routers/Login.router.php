<?php

$app->get('/login', function () use ($app) {
    $app->render('pages/_login.twig');
});
