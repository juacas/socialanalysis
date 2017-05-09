<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'vendor/autoload.php';
require_once 'JPDijkstra.php';
use \Fhaculty\Graph\Graph as Graph;
use Graphp\GraphViz\Dot as Dot;
$graph = new Graph();


// create some cities
$rome = $graph->createVertex('Rome');
$madrid = $graph->createVertex('Madrid');
$cologne = $graph->createVertex('Cologne');
$paris = $graph->createVertex('Paris');

// build some roads
$cologne->createEdgeTo($madrid)->setWeight(1762);
$madrid->createEdgeTo($rome)->setWeight(1956);
$rome->createEdgeTo($madrid)->setWeight(1956);
$cologne->createEdgeTo($paris)->setWeight(497);
$rome->createEdgeTo($paris)->setWeight(1421);
// create loop
$rome->createEdgeTo($rome)->setWeight(25);
$gv = new Graphp\GraphViz\GraphViz();
$dot = new Dot($gv);

echo $dot->getOutput($graph);

$sp = new JPDijkstra($cologne);
$edges=$sp->getEdges();
$graph2 = $sp->createGraph();
echo $dot->getOutput($graph2);
$path = $sp->getWalkTo($rome);
echo "IDs:".json_encode($path->getVertices()->getIds())."\n";
$dmap=$sp->getDistanceMap();
echo json_encode($dmap);
//$ruta = $sp->getEdgesTo($madrid);
//var_dump($ruta);

/*
foreach ($rome->getVerticesEdgeFrom() as $vertex) {
    echo $vertex->getId().' leads to rome'.PHP_EOL;
    // result: Madrid and Rome itself
}
*/



