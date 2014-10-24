<?php

$app->get('/', function () use ($app) {
    $username = '';
    $role = '';
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
    }
    $app->render('pages/_index.twig', array(
        'username' => $username,
        'role' => $role
    ));
});
