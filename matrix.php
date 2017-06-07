<?php

require_once 'auxiliares.php';
require_once 'matrix.php';

/**
 * Obtiene el numero de reacciones recibidas en el Post, y actaliza el "score" de
 * la persona que escribio el Post
 * @param SocialMatrix $matrix
 * @param GraphNode $post
 */
function get_post($matrix, $post) {

    $postname = useridfor($post);
    $matrix->registerInteraction(null, $postname, 'Post');
    
    $reactions = $post->getField('reactions');

    //Por cada post que escribe una persona aumento su score +1, más 0.1 por cada reacción recibida
    foreach ($reactions as $reaction){
        $matrix->registerInteraction($reaction->from, $postname, 'Reaction-'.$reaction->type);
    }
    $matrix->addScore($postname, (0.1 * sizeof($reactions)) + 1);
}


/**
 * Actualiza el "score" de la persona que escribio un Comment a un Post con el valor asigando
 * por escribir el Comment más los "Likes" que obtuvo su Comment. Registra la interacción con la 
 * persona a la que contesta si no son la misma persona.
 * El Comment no se registrará como interacción ni se actualizará el "score" de la persona si este es demasiado corto.
 * @param GraphNode $comment
 * @param GraphNode $post
 * @param SocialMatrix $matrix
 */
function get_main_comment($comment, $post, $matrix) {

    $postname = useridfor($post);
    $commentname = useridfor($comment);
    $comment_reactions = $comment->getField('likes');
    $ok = is_short_comment($comment);

    //Si el comentario es mayor de dos palabras
    if (!$ok) {
        // Si la persona que escribe el comentario es la misma que escribio el post NO se actualiza la matriz
        if (strcmp($commentname, $postname) != 0) {
            $matrix->registerInteraction($commentname, $postname, 'Reply');
        }
        $matrix->addScore($commentname, 1 + (sizeof($comment_reactions) * 0.1));
    }
}


/**
 * Actualiza el "score" de la persona que respondió a un Comment con el valor asigando
 * por escribir el Comment más los "Likes" que obtuvo su Comment.
 * Registra la interacción con la persona a la que responde y con la persona que escribio el Post raíz.
 * No se registrará ninguna interacción ni se actualizará su "score" si el comentario es demasiado corto.
 * @param string $comment_2
 * @param string $comment_1
 * @param GraphNode  $post
 * @param SocialMatrix $matrix
 */
function get_second_comment($comment_2, $comment_1, $post, $matrix) {

    $ok = is_short_comment($comment_2);

    //Si es un comentario largo
    if (!$ok) {

        $postname = useridfor($post);
        $commentname_1 = useridfor($comment_1);
        $commentname_2 = useridfor($comment_2);
        $comment_reactions = $comment_2->getField('likes');

        if (strcmp($commentname_1, $commentname_2) != 0) {
            $matrix->registerInteraction($commentname_2, $commentname_1, 'Rereplay');
        }

        if (strcmp($postname, $commentname_2) != 0) {
            $matrix->registerInteraction($commentname_2, $postname, 'Rereplay');
        }
        $matrix->addScore($commentname_2, 1 + (sizeof($comment_reactions) * 0.1));
    }
}
