<?php
        require_once 'vendor/autoload.php';
        require_once 'graphlib.php';
        require_once 'matrix.php';
        require_once 'auxiliares.php';
        require_once 'centralidad.php';
        require_once 'JPDijkstra.php';
        //require_once 'index.php';
        
        
        $fb = new Facebook\Facebook([  
        'app_id' => '176559559452612',
        'app_secret' => '34a26342e8ff84802bfb80bb5b1e484f',
        'default_graph_version' => 'v2.7',
        ]);
       
        try {
          
          //Returns a `Facebook\FacebookResponse` object
          $fb->setDefaultAccessToken('EAACglHnSp8QBAMaW9R45BSb27vhQPPZCVTewjRh7qxreRGoExLrAfnCVIhgQKsERsq6sUF6ZAi9JLtv6XgBW0Izj2U4ksAmg4E1KUvTDXawr1jguOICY9E0aBhlySqdcSP1Qm432FPZCTxxZBCvT62is5cZBfaZAEZD');
          //Realizo la peticion y la guardo en response
          $response = $fb->get('975539735923452?fields=feed{message,from,created_time,reactions,comments{message,from,created_time,likes,comments{message,from,created_time,likes}}},members');
          
          // Paso la peticion a un GraphNode y la guardo en la variable globalnode
          $globalnode = $response->getGraphNode();
          
          /* @var $membersnode Facebook\GraphNodes\GraphEdge */
          // Obtengo los miembros del grupo
          $membersnode = $globalnode->getField('members');
          
          /* @var $members Facebook\GraphNodes\GraphEdge */
          //Obtengo el nodo de los miembros del grupo como un array
          $members = $membersnode->asArray();

          /* @var $feednode Facebook\GraphNodes\GraphEdge */
          // Obtengo el feed del grupo
          $feednode = $globalnode->getField('feed');
          
          /* @var $posts Facebook\GraphNodes\GraphEdge */
          // Obtengo todos los posts del grupo iterando 
          $posts = $feednode->getIterator();
          
          //Inicializo la matriz de adyacencia y el vector de score a "0"
          // Preguntar como puedo no inicializar a 0 sin que me de error
          $score = array();
        
          foreach ($members as $value) {
              
              $username = $value['name'];
              $members_array[$username] = array();
              $score[$username] = 0;
              $matriz_adyacencia[$username] = array();
          }
          
         
          // Comienza el bucle que recorrera todos los post del grupo
          while($posts->valid()){
            
            /* @var $post Facebook\GraphNodes\GraphNode */            
            $post = $posts->current();
            
            /* @var $comments Facebook\GraphNodes\GraphEdge */
            $comments = $post->getField('comments');
            
            actualize_data_post($post,$score);
  
            $i = 0;
            
            while($i < sizeof($comments)){
                
                actualize_data_main_comment($comments[$i],$post,$matriz_adyacencia,$score);
                
                /* @var $comments_2 Facebook\GraphNodes\GraphEdge */
                $comments_2 = $comments[$i]->getField('comments');
                
                $j = 0;
                
                while($j < sizeof($comments_2)){
                    
                    actualize_data_second_comment($comments_2[$j],$comments[$i],$post,$matriz_adyacencia,$score,$members_array);
                    $j++;
                }
                $i++;
            }
            
            //centralidad_grado($members_array,$matriz_adyacencia,$vector_entrada, $vector_salida);
            create_graph($members,$matriz_adyacencia,$score);
           
            //Obtengo el siguiente post
            $posts->next();
          }
          
          //create_graph($members,$matriz_adyacencia);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }
       
       
        
        
       
        
       
        
   
