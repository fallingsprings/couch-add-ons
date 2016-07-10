<?php
    
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
    
    class CharacterCounter{
        
    function character_counter( $params, $node ){ // for use within cms:config_list_view/config_form_view
        global $CTX, $FUNCS, $AUTH;
        if( $AUTH->user->access_level < K_ACCESS_LEVEL_SUPER_ADMIN ){ return; }

        // get the 'config' object supplied by 'cms:config_list_view' tag or 'cms:config_form_view' tag
        $arr_config = &$CTX->get_object( '__config', 'config_list_view' );
        if( !is_array($arr_config) ){
            $arr_config = &$CTX->get_object( '__config', 'config_form_view' );
            if( !is_array($arr_config) ){ return; }
        }

        if( count($node->children) ){
            $code = $node->children;
            $counter_params = trim($code[0]->text);
            
            //TODO:repeatable 'remove row' breaks 'add row' listener
            //Maybe wait for expected update to repeatable regions
            $script = 'var my_counters=['.$counter_params.'];';
            $script .= file_get_contents(K_ADMIN_URL . 'addons/character_counter/js/characterCounter.min.js');
        }
        
        
        $code[0]->text = $script;
        
        $key = ( isset($node->__array_key__) ) ? $node->__array_key__ : 'js';
        $arr_config[$key] = $code;        
    }        
}
$FUNCS->register_tag( 'character_counter', array('CharacterCounter', 'character_counter') );
