<?php

$app->get('/', function () use ($app) {
    $username = '';
    $role = '';
    $matkul = '';
    if (isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        $matkul = $_SESSION['matkul'];
    }
    $app->render('pages/_dashboard.twig', array(
        'username' => $username,
        'role' => $role,
        'matkuls' => $matkul
    ));
});
