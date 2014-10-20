<?php

$app->get('/', function () use ($app) {
    $app->render('pages/_index.twig');
});
