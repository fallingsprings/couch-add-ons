<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    // UDF for editable type="color"
    class Color extends KUserDefinedField{

        static function handle_params( $params ){
            global $FUNCS;

            $attr = $FUNCS->get_named_vars(
                        array(
                              'color'=>'#ffffff',
                              'field_width'=>'100%',
                              'field_height'=>'',
                             ),
                        $params);

            //sanitize parameters.
            $attr['color'] = strtolower( trim($attr['color']) );
            $attr['field_width'] = strtolower( trim($attr['field_width']) );
            $attr['field_height'] = strtolower( trim($attr['field_height']) );
            $pattern = '/^#([a-f0-9]{6})$/';

            if ( !preg_match( $pattern, $attr['color'] ) ){
                 die( "ERROR: Tag \"editable\" type \"color\" - '".$attr['color']."' is not a valid hexadecimal color." );
            }
            return $attr;
        }

        function _render( $input_name, $input_id, $extra='', $dynamic_insertion=0 ){
            global $FUNCS, $CTX;

            $color = $this->get_data();

            $html = '<input type="color" name="' . $input_name . '"  id="' . $input_id . '" value="' . htmlspecialchars( $color, ENT_QUOTES, K_CHARSET ) . '" style="width:' . $this->field_width . ';';
            $html .= strlen( $this->field_height ) ? 'height:' . $this->field_height .';' : '';
            $html .= '" ' . $extra . '/>';
          
            return $html;
        }

        function get_data( $for_ctx=0 ){
            $data = strlen( $this->data ) ? $this->data : $this->color;
            return $data;
        }

      function validate(){
            global $FUNCS;
            
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/color-picker/lang/' . K_ADMIN_LANG . '.php') ){
                require('lang/'.K_ADMIN_LANG.'.php');
            }else{
                require('lang/EN.php');
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
