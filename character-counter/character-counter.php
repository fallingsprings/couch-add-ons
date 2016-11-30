<?php
    
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
    
    class CharacterCounter{
        static function character_counter( $params, $node ){
            $params = trim(preg_replace('/\s\s+/', ' ', $node->children[0]->text));

            $script = 'var my_counters=['.$params.'];';
            $script .= file_get_contents(K_ADMIN_URL . 'addons/character-counter/js/characterCounter.min.js');
        
            return $script;      
        }        
    }
$FUNCS->register_tag( 'character_counter', array('CharacterCounter', 'character_counter') );
//TODO: repeatable 'remove row' breaks 'add row' listener
//Maybe wait for expected update to repeatable regions, though