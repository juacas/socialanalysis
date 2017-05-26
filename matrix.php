<?php

require_once 'auxiliares.php';
require_once 'matrix.php';

// Funcion que contea y almacena la informacion de un post
/**
 * 
 * @param SocialMatrix $matrix
 * @param GraphNode $post
 */
function actualize_data_post($matrix, $post) {

    $postname = useridfor($post);

    $reactions = $post->getField('reactions');

    //Por cada post que escribe una persona aumento su score +1, más 0.1 por cada reacción recibida
    $matrix->addScore($postname, (0.1 * sizeof($reactions)) + 1);
}

// Funcion que almacena la informacion de un comentario que recibe un post
/**
 * 
 * @param type $comment
 * @param GraphNode $post
 * @param SocialMatrix $matrix
 */
function actualize_data_main_comment($comment, $post, $matrix) {
    
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

// Funcion que contea y almacena la informacion de un comentario que recibe un comentario
/**
 * 
 * @param string $comment_2
 * @param string $comment_1
 * @param GraphNode  $post
 * @param SocialMatrix $matrix
 */
function actualize_data_second_comment($comment_2, $comment_1, $post, $matrix) {

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
