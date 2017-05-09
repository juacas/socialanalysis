<?php

//Funcion para eliminar los comentarios de menores o iguales a dos palabras
    function is_short_comment($comment){

        $ok = false;
        //obtengo el mensaje del comentario
        $message = $comment->getField('message');
        //Cuento el numero de palabras del mensaje
        $num_words = str_word_count($message,0);

        //Si el mensaje tiene dos o menos palabras ignoramos dicho mensaje
        if($num_words <= 2){

            $ok = true;
        }              
        return $ok;
    }

//Funcion que devuelve el nombre de lo que le pasen    
    function name($in){
              
        $author = $in->getField('from');
        $name = $author->getField('name');  

        return $name;
    }


 