<?php
require_once 'vendor/autoload.php';
require_once './JPDijkstra.php';
require_once './JSONExporter.php';
use \Fhaculty\Graph\Graph as Graph;
use Graphp\GraphViz\Dot as Dot;
$graph = new Graph();

// create some cities
$rome = $graph->createVertex('Rome');
$rome->setAttribute('pop', 2323233);
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

$exporter =new es\uva\eduvalab\JSONExporter();

echo $exporter->getOutput($graph);