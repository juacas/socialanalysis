
<?php

require_once 'graphlib.php';


/**
 * 
 * @param \Fhaculty\Graph\Graph $graph
 * @param type $dmap
 * @param type $username
 * @return type
 */
function centralidad_cercania($graph,$dmap, $username){

    // Obtengo la suma geodestica de cada nodo al resto de la red
    $suma_geodestica = array();
    $indice_cercania = array();
    $suma_geodestica = 0;

    foreach($dmap as $value){

        $suma_geodestica += $value;    
    }

    if($suma_geodestica==0){
        $indice_cercania = 0;
    }else{
        $indice_cercania = (($graph->getVertices()->count()) / $suma_geodestica[$username]) * 100;
    }

    echo "\n".$suma_geodestica."\n";
    echo "\n".$indice_cercania."\n";
    return [$suma_geodestica,$indice_cercania];
}
/**
 * Devolver la lista de members que están en ealgún ´PAth
 * @param \Fhaculty\Graph\Graph $graph
 * @param type $dmap
 * @param JPDijkstra $sp
 * @param type $memberid
 */
function centralidad_intermediacion($graph, $dmap, $sp , $memberid){
    
    // Obtengo los nodos por los que pasa en el camino mas corto a cada uno de los que está conectado 
    $indice_proximidad = array();
    
    foreach ($graph->getVertices() as $vertex){
        $path = $sp->getWalkTo($vertex);
        $ids=$path->getVertices()->getIds();
        for ($i=1;$i<count($ids)-1;$i++){
            $indice_proximidad[$ids[$i]]=isset($indice_proximidad[$ids[$i]])?$indice_proximidad[$ids[$i]]+1:1;
        }
    }
    return $indice_proximidad;
    /*
    foreach ($dmap as $key => $value) {
        echo $key."   ".$value."\n";
        if($value > 2){
            // esto de mas de 2 no vale tengo que poner que no se repita los iDs
            $path = $sp->getWalkTo($graph->get);
            
            echo "IDs:".json_encode($path->getVertices()->getIds())."\n";
        }
        
    }*/
    
}