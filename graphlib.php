<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once 'JPDijkstra.php';
require_once 'graphlib.php';
require_once 'centralidad.php';

use \Fhaculty\Graph\Graph as Graph;

class SocialMatrix {

    private $matriz_adyacencia = [];

    /**
     * @var Graph $graph 
     */
    private $graph;
    private $score = [];
    private $members_array = [];

    public function __construct($members) {
        foreach ($members as $value) {
            $username = $value['name'];
            $this->members_array[$username] = array();
            $this->score[$username] = 0;
            $this->matriz_adyacencia[$username] = array();
        }
        $this->graph = new Graph();
    }

    /**
     * Devuelve el grafico
     * @return Graph
     */
    function get_graph() {
        return $this->graph;
    }

    /**
     * Crea un grafico a partir de un array de miembros
     * @param array $members_array
     */
    function generateSubGraph($members_array) {

        $graph = new Graph();
        //Crea los vertices
        $vertex = create_vertex($graph, $members_array);

        //Creo los arcos entre los vertices con el peso que indica la matriz de adyacencia
        foreach ($members_array as $key1) {
            foreach ($members_array as $key2) {
                if (isset($this->matriz_adyacencia[$key1][$key2])) {
                    $vertex[$key2]->createEdgeTo($vertex[$key1])->setWeight($this->matriz_adyacencia[$key1][$key2]);
                }
            }
        }
    }

    /**
     * Calcula la suma de pesos por el camino más corto de un miembro al resto de miembros 
     * a los que esté conectado (cercania), y cuantas veces van apareciendo los "vertex" en el medio 
     * de esos caminos (proximidad).
     * @return \stdClass $results
     */
    function calculateCentralities() {

        //global $dot;
        $results = [];

        foreach ($this->get_graph()->getVertices() as $member) {

            //Obtiene el camino mas corto a cada uno de los vertex que esta conectado el miembro
            $sp = new JPDijkstra($member);
            //$graph2 = $sp->createGraph();
            //echo "\n" . $dot->getOutput($graph2);
            //Array que contiene como clave los "ids" y como valor el "peso" total (por el camino mas corto)
            //a cada uno de los "Vertex" que está conectado
            $dmap = $sp->getDistanceMap();
            //echo json_encode($dmap) . "\n";
            //Calculo de la suma de todos los caminos a todos los vertices que está conectado
            $ind_cercania = $this->centralidad_cercania( $dmap);
            if (!isset($results[$member->getId()])) {
                $results[$member->getId()] = new stdClass();
            }
            $results[$member->getId()]->cercania = $ind_cercania;

            //Calculo de todos los "vertex" que están entre medias de los caminos mas cortos 
            $intermediacion_parcial = centralidad_intermediacion($this->get_graph(), $dmap, $sp);
            // añade a global
            foreach ($intermediacion_parcial as $key => $value) {
                if (!isset($results[$key])) {
                    $results[$key] = new stdClass();
                    $results[$key]->intermediacion = $value;
                } else if (!isset($results[$key]->intermediacion)) {
                    $results[$key]->intermediacion = $value;
                } else {
                    $results[$key]->intermediacion += $value;
                }
            }
        }
        return $results;
    }

    /**
     * Crea los vertices del grafico
     * @param Graph $graph
     * @param array $members_array
     * @return array vertex
     */
    function create_vertex($graph, $members_array) {

        $vertex = [];
        foreach ($members_array as $key) {

            $vertex[$key] = $graph->createVertex($key);
        }
        return $vertex;
    }

    /**
     * Calcula el numero de interacciones hechas "de salida" o recibidas "de entrada" 
     * para cada miembro del grupo
     * @param array $members_array
     * @return array
     */
    function centralidad_grado($members_array) {

        $vector_entrada = [];
        $vector_salida = [];
        foreach ($members_array as $key1) {
            $userid1 = useridfor($key1);
            $vector_salida[$userid1] = $vector_entrada[$userid1] = 0;
        }
        foreach ($members_array as $key1) {
            $userid1 = useridfor($key1);

            foreach ($members_array as $key2) {
                $userid2 = useridfor($key2);
                if (isset($this->matriz_adyacencia[$userid1][$userid2])) {
                    $vector_entrada[$userid1] += $this->matriz_adyacencia[$userid1][$userid2];
                }
                if (isset($this->matriz_adyacencia[$userid2][$userid1])) {
                    $vector_salida[$userid1] += $this->matriz_adyacencia[$userid2][$userid1];
                }
            }
        }
        return [$vector_entrada, $vector_salida];
    }

    /**
     * Actualiza la puntuación individual o "score" de cada participante a través de los Posts
     * Repalys, o Rereplays que escribe y de las "Reactions" y/o "Likes" que pueda recibir en ellos. 
     * @param string $member
     * @param float $addscore
     */
    function addScore($member, $addscore) {
        $this->score[$member] += $addscore;
    }

    /**
     * Funcion que registra una interaccion entre dos miembros, creando los "Vertex" y/o los "Edges"
     * en caso de no exitir, 
     * @param string $from
     * @param string $to
     * @param type $type
     */
    function registerInteraction($from, $to, $type) {

        if (!isset($this->matriz_adyacencia[$to][$from])) {
            $this->matriz_adyacencia[$to][$from] = 1;
        } else {
            $this->matriz_adyacencia[$to][$from] ++;
        }


        if (!$this->graph->hasVertex($from)) {
            $fromVertex = $this->graph->createVertex($from);
        } else {
            $fromVertex = $this->graph->getVertex($from);
        }
        if (!$this->graph->hasVertex($to)) {
            $toVertex = $this->graph->createVertex($to);
        } else {
            $toVertex = $this->graph->getVertex($to);
        }

        $edge = $fromVertex->createEdgeTo($toVertex);
        $edge->setAttribute('type', $type);
    }
    /**
     * Calcula lo cercano que está un miembro del resto de miembros a los que está unido
     * @param \Fhaculty\Graph\Graph $graph
     * @param array $dmap
     * @return int $indice_cercania
     */
    function centralidad_cercania( $dmap) {
        
        // Obtengo la suma geodestica de cada nodo al resto de la red
        $suma_geodesica = 0;
        
        foreach ($dmap as $value) {
            $suma_geodesica += $value;
        }
        // Add distance to unaccesible nodes
        $total = count($this->graph->getVertices());
        $accesible = count($dmap);
        $suma_geodesica += ($total-$accesible)*($total*100); 
        if ($suma_geodesica == 0) {
            $indice_cercania = 0;
        } else {
            $indice_cercania = (($total - 1) / $suma_geodesica);
        }
        //echo "\n".$suma_geodestica."\n";
        //echo "\n".$indice_cercania."\n";
        return $indice_cercania;
    }
    
    /**
     * Devolver la lista de members que están en algún Path y actualizar su contador cada vez que ocurra
     * @param \Fhaculty\Graph\Graph $graph
     * @param array $dmap
     * @param JPDijkstra $sp
     * @return array $indice_proximidad
     */
    function centralidad_intermediacion($dmap, $sp) {
        
        // Obtengo los nodos por los que pasa en el camino mas corto a cada uno de los que está conectado
        $indice_proximidad = array();
        
        foreach ($dmap as $key => $value) {
            $vertex = $this->graph->getVertex($key);
            $path = $sp->getWalkTo($vertex);
            $ids = $path->getVertices()->getIds();
            for ($i = 1; $i < count($ids) - 1; $i++) {
                $indice_proximidad[$ids[$i]] = isset($indice_proximidad[$ids[$i]]) ? $indice_proximidad[$ids[$i]] + 1 : 1;
            }
        }
        return $indice_proximidad;
    }
    
}
