<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'JPDijkstra.php';


use \Fhaculty\Graph\Graph as Graph;
use Graphp\GraphViz\Dot as Dot;

$graph = new Graph();
$gv = new Graphp\GraphViz\GraphViz();
$dot = new Dot($gv);


function create_graph($members, $matriz_adyacencia, $score){
    
    global $graph,$dot;
    
    
    for($i=0; $i<sizeof($members); $i++){
        
      $nodos[$members[$i]['name']]= $graph->createVertex($members[$i]['name']);
      //create loops for each node
      $nodos[$members[$i]['name']]->createEdgeTo($nodos[$members[$i]['name']])->setWeight($score[$members[$i]['name']]);
   
    }
    
    for($i=0; $i<sizeof($members); $i++){
        for($j=0; $j<sizeof($members); $j++){
            if(isset($matriz_adyacencia[$members[$i]['name']][$members[$j]['name']])){
                $nodos[$members[$j]['name']]->createEdgeTo($nodos[$members[$i]['name']])->setWeight($matriz_adyacencia[$members[$i]['name']][$members[$j]['name']]);
            }
        }
    }
     
    echo $dot->getOutput($graph);
}





