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
     * 
     * @return Graph
     */
    function get_graph() {
        return $this->graph;
    }

    //Funcion que crea el grafico a partir de un array de miembros
    /**
     * 
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

    function calculateCentralities() {

        global $dot;
        $results = [];
        //Grafico que muestra el camino mas corto desde un nodo al resto de la red
        foreach ($this->get_graph()->getVertices() as $member) {

            $sp = new JPDijkstra($member);
            $graph2 = $sp->createGraph();
            echo "\n" . $dot->getOutput($graph2);

            // array que contiene la suma del camino mas corto de un nodo con cada nodo de la red que esta conectado
            $dmap = $sp->getDistanceMap();
            echo json_encode($dmap) . "\n";
            //Funcion que calcula los indices de lejanía y cercania
            list($dist_geod, $ind_cercania) = centralidad_cercania($this->get_graph(), $dmap, $member->getId());
            if (!isset($results[$member->getId()])) {
                $results[$member->getId()] = new stdClass();
            }
            $results[$member->getId()]->cercania = $ind_cercania;

            $intermediacion_parcial = centralidad_intermediacion($this->get_graph(), $dmap, $sp);
            // añade a global
            foreach ($intermediacion_parcial as $key => $value) {
                if (!isset($results[$key])) {
                    $results[$key] = new stdClass();
                }
                $results[$key]->intermediacion += $value;
            }
        }
        return $results;
    }

    //Funcion que crea los vertices del grafico
    /**
     * 
     * @param Graph $graph
     * @param type $members_array
     * @return array vertex
     */
    function create_vertex($graph, $members_array) {

        $vertex = [];
        foreach ($members_array as $key) {

            $vertex[$key] = $graph->createVertex($key);
        }
        return $vertex;
    }

    //Calculo de la centralidad de grado (grado de salida y de entrada)
    /**
     * 
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
                    $vector_entrada[$userid2] += $this->matriz_adyacencia[$userid1][$userid2];
                }
                if (isset($this->matriz_adyacencia[$userid2][$userid1])) {
                    $vector_salida[$userid1] += $this->matriz_adyacencia[$userid2][$userid1];
                }
            }
        }
        return [$vector_entrada, $vector_salida];
    }

    //Funcion que actualiza el score de un determinado participante
    /**
     * 
     * @param string $member
     * @param float $addscore
     */
    function addScore($member, $addscore) {
        $this->score[$member] += $addscore;
    }

    //Funcion que registra una interaccion entre dos miembros
    /**
     * 
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

        $edges = $fromVertex->getEdgesTo($toVertex);
        if (count($edges) > 0) {
            $edge = $edges->getEdgeFirst();
        } else {
            $edge = $fromVertex->createEdgeTo($toVertex);
        }
        $edge->setWeight($edge->getWeight() + 1);
    }

}
