<?php

    //Calculo de la centralidad de grado (grado de salida y de entrada)
    function centralidad_grado($members,$matriz_adyacencia,&$vector_entrada, &$vector_salida){

        for($i=0; $i<sizeof($members); $i++){

            $vector_salida[$members[$i]['name']] = $vector_entrada[$members[$i]['name']] = 0;

            for($j=0; $j<sizeof($members); $j++){

                $vector_entrada[$members[$i]['name']] += $matriz_adyacencia[$members[$i]['name']][$members[$j]['name']];
                $vector_salida[$members[$i]['name']] += $matriz_adyacencia[$members[$j]['name']][$members[$i]['name']];
            }
        }

    }
    
    

