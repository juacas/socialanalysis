<?php

/**
 * Analiza el texto de un comentario y devuelve "true" si es corto o "false" si no lo es 
 * @param GraphEdge $comment
 * @return boolean $ok
 */
function is_short_comment($comment) {

    $ok = false;
    //obtengo el mensaje del comentario
    $message = $comment->getField('message');
    //Obtengo el numero de palabras del mensaje
    $num_words = str_word_count($message, 0);

    //Si el mensaje es corto devuelve true
    if ($num_words <= 2) {
        $ok = true;
    }
    return $ok;
}

/**
 * Devuelve el nombre del autor de un comment o un post, o bien de un array de miembros
 * ¿¿¿¿¿¿¿¿ Si $in no es ninguno de los anteriores devuelve $in ???????
 * @param  array member / GraphNode $in
 * @return string $in['name'] / $name
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
        throw new Exception('Objeto de tipo desconocido: ' . var_dump($in));
    }
}
