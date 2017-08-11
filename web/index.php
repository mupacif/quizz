<?php 

require_once __DIR__.'/../vendor/autoload.php';

$app= new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider());
$app['db.options'] = array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/Quizz.db',
    );


require __DIR__.'/route.php';

/*    $app->get('/addQuestion/{nom}', function ($nom) use ($app) {
    
    $app['db']->insert('interro', array('nom'=>$nom);

    return  "ok";
});*/

$app->run();