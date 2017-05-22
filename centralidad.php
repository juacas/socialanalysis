<?php


/**
 * Calculo de la centralidad de grado (grado de salida y de entrada)
 * TODO: Documentar esto!!
 * TODO: ¿Esto se puede calcular involucrando sólo a los usuarios del array o en el bucle 
 * interno hay que considerar a TODOS los usuarios??? Aclarar y revisar.
 * 
 * @param array $members 
 * @param array $matriz_adyacencia
 */
    function centralidad_grado($members,$matriz_adyacencia){
        $vector_entrada=array();
        $vector_salida = array();
        
        for($i=0; $i<sizeof($members); $i++){

            $vector_salida[$members[$i]['name']] = $vector_entrada[$members[$i]['name']] = 0;

            for($j=0; $j<sizeof($members); $j++){

                $vector_entrada[$members[$i]['name']] += $matriz_adyacencia[$members[$i]['name']][$members[$j]['name']];
                $vector_salida[$members[$i]['name']] += $matriz_adyacencia[$members[$j]['name']][$members[$i]['name']];
            }
        }

    }
    
    

