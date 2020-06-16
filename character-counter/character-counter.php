<?php
    
  if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
    
  function character_counter_handler( $params, $node ){
    $counter_object = trim(preg_replace('/\s\s+/', ' ', $node->children[0]->text));
    global $FUNCS;
    extract( $FUNCS->get_named_vars(
              array(
                'lang_warning'=>'Maximum Reached',
                'lang_max' =>'Max: ',
                'lang_min' =>'Min: ',
                ),
              $params)
           );

    $script = '"use strict";';
    $script .= 'function localizeCounter(){';
      $script .= 'const LANG = {warning: "'.$lang_warning.'", max: "'.$lang_max.'", min: "'.$lang_min.'"};';
      $script .= 'return LANG;';
    $script .= '}';

    $script .= 'const characterCounters=['.$counter_object.'];';
    $script .= file_get_contents(K_ADMIN_URL . 'addons/character-counter/js/character-counter.min.js');
        
        return $script;      
    }        
    
$FUNCS->register_tag( 'character_counter', 'character_counter_handler' );