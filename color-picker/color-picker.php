<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    // UDF for editable type="color"
    class Color extends KUserDefinedField{

        static function handle_params( $params ){
            global $FUNCS;

            $attr = $FUNCS->get_named_vars(
                        array(
                              'color'=>'#ffffff',
                             ),
                        $params);

            //sanitize color value.
            $attr['color'] = strtolower( trim($attr['color']) );
            $pattern = '/^#([a-f0-9]{6})$/';

            if ( !preg_match( $pattern, $attr['color'] ) ){
                 die( "ERROR: Tag \"editable\" type \"color\" - '".$attr['color']."' is not a valid hexadecimal color." );
            }
            return $attr;
        }

        function _render( $input_name, $input_id, $extra='', $dynamic_insertion=0 ){
            global $FUNCS, $CTX;

            $color = $this->get_data();

            return '<input type="color" name="'.$input_name.'"  id="'.$input_id.'" value="'.htmlspecialchars( $color, ENT_QUOTES, K_CHARSET ).'" style="width:100%;" '.$extra.'/>';
        }

        function get_data( $for_ctx=0 ){
            $data = strlen( $this->data ) ? $this->data : $this->color; 
            return $data;
        }

      function validate(){
            global $FUNCS;
            require('lang/EN.php');
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/color-picker/lang/' . K_ADMIN_LANG . '.php') ){
            require('lang/'.K_ADMIN_LANG.'.php');
            }

            if ( $this->validate != '0' ){
                $color = $this->data;
                $pattern = '/^#([A-Fa-f0-9]{6})$/';

                if ( !preg_match( $pattern, $color ) ){
                    $this->err_msg = $color_error;
                    return false;
                }
            }
            return parent::validate();
        }
    }

    $FUNCS->register_udf('color', 'Color', 1/*repeatable*/);
