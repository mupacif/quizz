<?php 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});





$app->get('/',function() use($app)
{
return $app->json(array('msg'=>'nothing to see here'));
});

// TEST

$app->get('/test',function() use($app)
{
return $app->json(array('yosh'=>'now it works'));
});


// Chapter

$app->get('/chapters',function() use($app)
{
return $app->json($app['db']->fetchAll('select  id, name from chapter'));
});


$app->post('/chapter', function(Request $request) use($app){
    $name = $request->get('name');
    $ok = $app['db']->insert('chapter', array('name'=>$name));
    return $app->json(array('id'=>$app['db']->lastInsertId()));

});


$app->delete('/chapter/{id}', function($id) use($app){
    $app['db']->executeQuery("PRAGMA foreign_keys = ON");
    $post = $app['db']->delete('chapter', array('id' => $id));
    return $app->json($post);

});


// question text 

$app->post('/addQuestion', function (Request $request) use($app) {
    $question= $request->get('name');
    $idChapter = $request->get('idChapter');
    $ok = $app['db']->insert('question', array('name'=>$question,'idChapter'=>$idChapter));

    return  $app->json(array('id'=>$app['db']->lastInsertId()));
});


$app->put('/setQuestion', function (Request $request) use($app) {
    $idQuestion = $request->get('idQuestion');
    $question = $request->get('question');
    $ok = $app['db']->executeUpdate('UPDATE question SET name = ? WHERE id = ?', array($question, $idQuestion));

    return new Response("back:"+$ok,200);
});
            /**
            get all question of a given chapter
            */
$app->get('/chapter/{id}',function($id) use($app)
{
    $sql = "select q.idChapter, q.name, q.id from question q join chapter c on c.id = q.idChapter and c.id= ?";
    $post = $app['db']->fetchAll($sql, array((int) $id));
return $app->json($post);
});
            /**
            delete a quesiton
            */
$app->delete('/question/{id}', function($id) use($app){
    $app['db']->executeQuery("PRAGMA foreign_keys = ON");
    $post = $app['db']->delete('question', array('id' => $id));
    return $app->json($post);

});


// answers

$app->post('/addAnswer', function (Request $request) use($app) {
    $answer= $request->get('name');
    $correct= $request->get('correct');
    $idQuestion = $request->get('idQuestion');
    $ok = $app['db']->insert('answer', array('name'=>$answer,'correct'=>$correct,'idQuestion'=>$idQuestion));

    return  $app->json(array('id'=>$app['db']->lastInsertId()));
});


$app->get('/answers/{id}',function($id) use($app)
{
    $sql = "select a.idQuestion, a.name, a.id, a.correct from answer a join question q on q.id = a.idQuestion and q.id= ?";
    $post = $app['db']->fetchAll($sql, array((int) $id));
return $app->json($post);
});


$app->delete('/answer/{id}', function($id) use($app){
    $app['db']->executeQuery("PRAGMA foreign_keys = ON");
    $post = $app['db']->delete('answer', array('id' => $id));
    return $app->json($post);

});


$app->put('/answer/{id}', function($id,Request $request) use($app){
    $answer = $request->get('answer');
    $correct = $request->get('correct');
    $ok = $app['db']->executeUpdate('UPDATE answer SET name = ?, correct = ? WHERE id = ?', array($answer,$correct, $id));

    return new Response("back:"+$ok,200);

});

