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

/*// INTERRO

$app->get('/interros/{idMatiere}',function($idMatiere) use($app)
{

return $app->json($app['db']->fetchAll('select  i.id, nom,  avg(note)*100 as "note" from interro i left join session s on i.id = s.idInterro   where i.idMatiere = ? group by nom order by i.id , s.idInterro, s.id ',array((int)$idMatiere)));
});

$app->get('/interro/{id}',function($id) use($app)
{
	$sql = "select q.idInterro, q.question, q.answer, q.id from interro i join question q on i.id = q.idInterro and i.id= ?";
    $post = $app['db']->fetchAll($sql, array((int) $id));
return $app->json($post);
});

$app->post('/addInterro', function (Request $request) use($app) {
    $nom = $request->get('nom');
    $idMatiere = $request->get('idMatiere');
    $app['db']->insert('interro', array('nom'=>$nom,'idMatiere'=>$idMatiere));

    return $app->json(array('id'=>$app['db']->lastInsertId()));
});

$app->delete('/interro/{id}',function($id) use($app)
{
    $app['db']->executeQuery("PRAGMA foreign_keys = ON");
    $post = $app['db']->delete('interro', array('id' => $id));
    return $app->json($post);
});


// QUESTIONS


$app->post('/addQuestion', function (Request $request) use($app) {
    $question = $request->get('question');
    $answer = $request->get('answer');
    $interro = $request->get('idInterro');
    $ok = $app['db']->insert('question', array('question'=>$question,'answer'=>$answer,'idInterro'=>$interro));

    return  $app->json(array('id'=>$app['db']->lastInsertId()));
});


$app->post('/setQuestion', function (Request $request) use($app) {
    $idQuestion = $request->get('idQuestion');
    $answer = $request->get('answer');
    $ok = $app['db']->executeUpdate('UPDATE question SET answer = ? WHERE id = ?', array($answer, $idQuestion));

    return new Response("back:"+$ok,200);
});



// SESSION 
$app->get('/session/{id}',function($id) use($app)
{
	$sql = "select note, date from session where idInterro= ? order by id desc limit 3";
    $post = $app['db']->fetchAll($sql, array((int) $id));
return $app->json($post);
});

$app->post('/addSession', function (Request $request) use($app) {
    $idInterro = $request->get('idInterro');
    $score = $request->get('score');
    $app['db']->insert('session', array('idInterro'=>$idInterro,'note'=>$score,'date'=>date("Y-n-j")));

    return $app->json(array('id'=>$app['db']->lastInsertId()));
});
*/


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


$app->post('/setQuestion', function (Request $request) use($app) {
    $idQuestion = $request->get('idQuestion');
    $question = $request->get('question');
    $ok = $app['db']->executeUpdate('UPDATE question SET name = ? WHERE id = ?', array($answer, $idQuestion));

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
