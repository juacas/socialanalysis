<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace es\uva\eduvalab;

/**
 * Description of JSONExporter
 *
 * @author juacas
 */

use Fhaculty\Graph\Exporter\ExporterInterface;
use Fhaculty\Graph\Graph;
use SimpleXMLElement;
use Fhaculty\Graph\Edge\Directed;
class JSONExporter implements ExporterInterface
{
   
    public function getOutput(Graph $graph)
    {
        $graphjson = new \stdClass();
        $graphjson->directed = true;
        $graphjson->graph=[];
        $graphjson->nodes=[];
        $graphjson->links=[];
        $nodeindexes=array();
        $index=0;
        foreach ($graph->getVertices()->getMap() as $id => $vertex) {
            /* @var $vertex Vertex */
            $nodeElem = new \stdClass();
            $nodeElem->id=$id;
            $attrs =$vertex->getAttributeBag()->getAttributes();
             foreach ($attrs as $name=>$value){
                $nodeElem->$name = $value;
            }
            $graphjson->nodes[] = $nodeElem;
            $nodeindexes[$id]=$index++;
        }
        foreach ($graph->getEdges() as $edge) {
            /* @var $edge Edge */
            $edgeElem = new \stdClass();
            $edgeElem->source = $nodeindexes[$edge->getVertices()->getVertexFirst()->getId()];
            $edgeElem->target = $nodeindexes[$edge->getVertices()->getVertexLast()->getId()];
            if ($edge instanceof Directed) {
                $edgeElem->directed = true;
            }
            foreach ($edge->getAttributeBag()->getAttributes() as $name=>$value){
                $edgeElem->$name = $value;
            }
            $edgeElem->weight= $edge->getWeight();
            $graphjson->links[]=$edgeElem;   
        }
        return json_encode($graphjson);
    }
}
