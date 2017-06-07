<?php

require_once 'vendor/autoload.php';
require_once 'graphlib.php';
require_once 'matrix.php';
require_once 'auxiliares.php';
require_once 'centralidad.php';
require_once 'JPDijkstra.php';
require_once './JSONExporter.php';

//require_once 'index.php';
use \Fhaculty\Graph\Graph as Graph;
use Graphp\GraphViz\Dot as Dot;

date_default_timezone_set('Europe/Madrid');
$fb = new Facebook\Facebook([
    'app_id' => '176559559452612',
    'app_secret' => '34a26342e8ff84802bfb80bb5b1e484f',
    'default_graph_version' => 'v2.7',
        ]);

try {

    //Returns a `Facebook\FacebookResponse` object
    $fb->setDefaultAccessToken('EAACglHnSp8QBAMaW9R45BSb27vhQPPZCVTewjRh7qxreRGoExLrAfnCVIhgQKsERsq6sUF6ZAi9JLtv6XgBW0Izj2U4ksAmg4E1KUvTDXawr1jguOICY9E0aBhlySqdcSP1Qm432FPZCTxxZBCvT62is5cZBfaZAEZD');
    //Realizo la peticion y la guardo en response
    $response = $fb->get('975539735923452?fields=feed{message,from,created_time,reactions,comments{message,from,created_time,likes,comments{message,from,created_time,likes}}},members');

    // Paso la peticion a un GraphNode y la guardo en la variable globalnode
    $globalnode = $response->getGraphNode();

    /* @var $membersnode Facebook\GraphNodes\GraphEdge */
    // Obtengo los miembros del grupo
    $membersnode = $globalnode->getField('members');

    /* @var $members Facebook\GraphNodes\GraphEdge */
    //Obtengo el nodo de los miembros del grupo como un array
    $members = $membersnode->asArray();

    /* @var $feednode Facebook\GraphNodes\GraphEdge */
    // Obtengo el feed del grupo
    $feednode = $globalnode->getField('feed');

    /* @var $posts Facebook\GraphNodes\GraphEdge */
    // Obtengo todos los posts del grupo iterando 
    $posts = $feednode->getIterator();

    //Creo una objeto de tipo SocialMatrix
    //$matrix = new SocialMatrix($members);
    $matrix = new DataBaseMatrix(); 
    // Comienza el bucle que recorrera todos los post del grupo
    while ($posts->valid()) {

        /* @var $post Facebook\GraphNodes\GraphNode */
        $post = $posts->current();

        /* @var $comments Facebook\GraphNodes\GraphEdge */
        $comments = $post->getField('comments');

        get_post($matrix, $post);

        $i = 0;

        while ($i < sizeof($comments)) {

            get_main_comment($comments[$i], $post, $matrix);

            /* @var $comments_2 Facebook\GraphNodes\GraphEdge */
            $comments_2 = $comments[$i]->getField('comments');

            $j = 0;

            while ($j < sizeof($comments_2)) {

                get_second_comment($comments_2[$j], $comments[$i], $post, $matrix);
                $j++;
            }
            $i++;
        }

        //Obtengo el siguiente post
        $posts->next();
    }
} catch (Facebook\Exceptions\FacebookResponseException $e) {
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
} catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
}

// Fase de análisis

// TODO: Lectura de interactions de la base datos
$interactions=[];
$matrix = new SocialMatrix($members);
foreach ($interactions as $interaction){
    $matrix->registerInteraction($interaction->from, $interaction->to, $interaction->type);
}

    list($vector_entrada, $vector_salida) = $matrix->centralidad_grado($members);

    $dot = new Dot();
    echo $dot->getOutput($matrix->get_graph());
    $results = $matrix->calculateCentralities();

    $exporter = new es\uva\eduvalab\JSONExporter();
    echo $exporter->getOutput($matrix->get_graph());

       
        
        
       
        
       
        
   
