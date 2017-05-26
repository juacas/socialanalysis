<?php

//Funcion que descarta los comentarios cortos menores o iguales a dos palabras
/**
 * 
 * @param GraphEdge $comment
 * @return boolean $ok
 */
function is_short_comment($comment) {

    $ok = false;
    //obtengo el mensaje del comentario
    $message = $comment->getField('message');
    //Cuento el numero de palabras del mensaje
    $num_words = str_word_count($message, 0);

    //Si el mensaje tiene dos o menos palabras ignoramos dicho mensaje
    if ($num_words <= 2) {

        $ok = true;
    }
    return $ok;
}

//Funcion que devuelve el nombre de lo que le pasen    
/**
 * TODO: Documentar bien y aclarar.
 * @param type $in
 * @return type
 */
function useridfor($in) {
    // $in is a member array
    if (isset($in['name'])) {
        return $in['name'];
    } else if ($in instanceof Facebook\GraphNodes\GraphNode) {
        $author = $in->getField('from');
        $name = $author->getField('name');
        return $name;
    } else {
        throw new Exception('Objeto de tipo desconocido: '.var_dump($in));
    }
}
