<?php
    
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
    
    function character_counter_handler( $params, $node ){
        $params = trim(preg_replace('/\s\s+/', ' ', $node->children[0]->text));

        $script = 'var my_counters=['.$params.'];';
        $script .= file_get_contents(K_ADMIN_URL . 'addons/character-counter/js/character-counter.min.js');
        
        return $script;      
    }        
    
$FUNCS->register_tag( 'character_counter', 'character_counter_handler' );