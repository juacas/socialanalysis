<?php

require_once 'auxiliares.php';
require_once 'matrix.php';


// Funcion que contea y almacena la informacion de un post
    function actualize_data_post($post,&$score){

        $postname = name($post);

        $reactions = $post->getField('reactions');

        //Por cada post que escribe una persona aumento su score +1, más 0.1 por cada reacción recibida
        $score[$postname] += (0.1*sizeof($reactions)) + 1;

    }
    
    
    
 // Funcion que contea y almacena la informacion de un comentario que recibe un post
    function actualize_data_main_comment($comment,$post,&$matriz_adyacencia,&$score){

        $postname = name($post);
        $commentname = name($comment);
        $comment_reactions = $comment->getField('likes');

        $ok = is_short_comment($comment);

        if(!$ok){

            // Si la persona que escribe el comentario es la misma que escribio el post NO se actualiza la matriz
            if(strcmp($commentname, $postname) != 0){
                
                if (!isset($matriz_adyacencia[$postname][$commentname])){
                   $matriz_adyacencia[$postname][$commentname]=1;
                }else{
                    $matriz_adyacencia[$postname][$commentname]++; 
                }
            }

            $score[$commentname] += 1 + (sizeof($comment_reactions)*0.1);

        }
    }

    
    
 // Funcion que contea y almacena la informacion de un comentario que recibe un comentario
    function actualize_data_second_comment($comment_2,$comment_1,$post,&$matriz_adyacencia,&$score,$members_array){

        $ok = is_short_comment($comment_2);

        //Si es un comentario largo
        if(!$ok){

            $postname = name($post);
            $commentname_1 = name($comment_1);
            $commentname_2 = name ($comment_2);
            $comment_reactions = $comment_2->getField('likes');

            if(strcmp($commentname_1, $commentname_2) != 0){
                
                if (!isset($matriz_adyacencia[$commentname_1][$commentname_2])){
                   $matriz_adyacencia[$commentname_1][$commentname_2]=1;
                }else{
                    $matriz_adyacencia[$commentname_1][$commentname_2]++;
                }
            }
            
            if(strcmp( $postname, $commentname_2) != 0){
                
                if (!isset($matriz_adyacencia[$postname][$commentname_2])){
                   $matriz_adyacencia[$postname][$commentname_2]=1;
                }else{
                    $matriz_adyacencia[$postname][$commentname_2]++; 
                }
            }
            
            $score[$commentname_2] += 1 + (sizeof($comment_reactions)*0.1);
            
            //is_someone_tagged($comment_2,$matriz_adyacencia,$members_array, $commentname_2);
        }

    }
    
    
    
// Funcion que obtiene si hay alguien etiquedado y actualiza la matriz
    function is_someone_tagged($comment, &$matriz_adyacencia, $members, $commentname){

        //Saco el mensaje escrito en el comentario
        $message = $comment->getField('message');
        
        //Busco si en el mensaje está el nombre de alguno de los miembros del grupo
        //$i=0;
        //$encontrado = false;
        
        foreach ($members as $value) {
            
            
            $pos = strpos($message,$key);
            if($pos !== FALSE){

                $matriz_adyacencia[$key][$commentname]++;
                break;
            }
        }
        
        /*
        while(($i < sizeof($members)) && ($encontrado == false) ){

            //$pos tendra la posicion de inicio del nombre, si no existe tendra FALSE
            $pos = strpos($message,$members['name']);

            //Si $pos contiene una posición será porque el miembro en el que nos encontramos esta nombrado
            if($pos !== FALSE){

                $matriz_adyacencia[$members['name']][$commentname]++;
                $encontrado = true;
            }
            $i++;
        }*/

    }