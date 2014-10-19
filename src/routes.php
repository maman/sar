<?php

$app->get('/', function () use ($app, $c) {
        $app->view()->setData(array(
            'title' => 'Hello World',
            'body' => 'Lorem ipsum dolor sit amet, consectetur adispicing elit.',
            'env' => $app->getMode()
        ));
        $app->render('index.html');
    }
);

$app->get('/test', function () use ($app, $c) {
        $app->view()->setData(array(
            'title' => 'Hello Test',
            'body' => 'Lorem ipsum dolor sit amet, consectetur adispicing elit.',
            'env' => $app->getMode()
        ));
        $app->render('index.html');
    }
);