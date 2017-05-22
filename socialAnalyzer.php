<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace es\uva\eduvalab;
require_once 'centralidad.php';
/**
 * Description of socialAnalyzer
 *
 * @author juacas
 */
class socialAnalyzer {
    public function addInteraction($fromUser,$toUser,$weight,$type,$info) {
        
    }
    /**
     * @return \Fhaculty\Graph\Graph graph representation of the interactions
     */
    public function getGraph() {
        
    }
    /**
     * @return \Graphp\GraphViz\Do
     */
    public function getAdjacencyMatrix() {    
    }
    /**
     * @param array(string) $members List of userids for the calculation of the centrality
     * @return array List with users and their centrality data
     */
    public function getCentrality($members) {
        $matrix = $this->getAdjacencyMatrix();
        
        list($inputcentrality, $outputcentrality) = centralidad_grado($members, $matriz_adyacencia);
        return [$inputcentrality,$outputcentrality];
    }
    /**
     * @return array List with users and their XXXXX data
     */
    public function getXXXXXX($param) {
        
    }
}
