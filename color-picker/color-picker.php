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
                              'alpha'=>'',
                             ),
                        $params);

            //sanitize parameters.
            $attr['color'] = strtolower( trim($attr['color']) );
            $attr['field_width'] = strtolower( trim($attr['field_width']) );
            $attr['field_height'] = strtolower( trim($attr['field_height']) );
            $attr['alpha'] = ( trim($attr['alpha']) );

            $pattern = '/^#([a-f0-9]{6})$|^#([a-f0-9]{8})$/';
            if ( !preg_match( $pattern, $attr['color'] ) ){
                 die( "ERROR: Tag \"editable\" type \"color\" - '".$attr['color']."' is not a valid hexadecimal color." );
            }
          
            //if color parameter is 8-digit hex, then enable alpha
            if ( preg_match( '/^#([a-f0-9]{8})$/', $attr['color'] ) ){
                $attr['alpha'] = '1';
            }
            return $attr;
        }

        function _render( $input_name, $input_id, $extra='', $dynamic_insertion=0 ){
            global $FUNCS, $CTX;

            $color = $this->get_data();
          
            if( strlen( $this->alpha ) ){
                //Split alpha value from hex color value
                //if no alpha value then make it 255 (100%)
                $alpha = strlen( str_split($color, 7)[1] ) ? hexdec( str_split( $color, 7 )[1] ) : 255;
                $color = str_split( $color, 7 )[0];
            }
          
            $html = '<input type="color" name="' . $input_name . '[color]"  id="' . $input_id . '" value="' . htmlspecialchars( $color, ENT_QUOTES, K_CHARSET ) . '" style="width:' . $this->field_width . ';';
            $html .= strlen( $this->field_height ) ? 'height:' . $this->field_height .';' : '';
            $html .= strlen( $alpha ) ? 'opacity:' . $alpha/255 .';' : '';
            $html .= '" ' . $extra . '/>';
          
           if ( strlen( $alpha ) ){
                $html .= '<br />';
                $html .= 'Opacity (<span id="' . $input_id . '_alpha_percent">' . round( ($alpha / 2.55), 1 ) . '</span>%):<br />';

                $script = "let opacity = Math.round(getElementById('" . $input_id . "_alpha').value / .255)/10; getElementById('" . $input_id . "_alpha_percent').innerHTML = opacity; getElementById('" . $input_id . "').style.opacity = opacity/100;";

                $html .= '<input type="range" name="' . $input_name . '[alpha]"  id="' . $input_id . '_alpha" value="' . $alpha . '" min="0" max="255" style="width:' . $this->field_width . '" oninput="' . $script . '" />';
           }
          
            return $html;
        }

        function get_data( $for_ctx=0 ){
            $data = strlen( $this->data ) ? $this->data : $this->color;
            return $data;
        }

        function store_posted_changes( $post_val ){
            global $FUNCS;
            if( $this->deleted ) return; // no need to store
            if( is_null($this->orig_data) ) $this->orig_data = $this->data;
          
            if( strlen($this->alpha) ){
                //convert alpha value to hex
                //if 100% opacity, we can just leave it off
                if( $post_val['alpha'] == 255 ){
                    $post_val['alpha'] = '';
                }else{
                    $post_val['alpha'] = dechex( $post_val['alpha'] );
                }
                // create 8-digit hexadecimal color with alpha value
				if strlen($post_val['alpha']) == 1{		//single digit hex value
					$post_val['color'] .= '0';   		//insert a 0
				}
                $post_val['color'] .= $post_val['alpha'];
            }
            
            $this->data = $post_val['color'];

            // modified?
            $this->modified = ( strcmp( $this->orig_data, $this->data )==0 ) ? false : true; // values unchanged
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
                $pattern = '/^#([A-Fa-f0-9]{6})$|^#([a-f0-9]{8})$/';

                if ( !preg_match( $pattern, $color ) ){
                    $this->err_msg = $color_error;
                    return false;
                }
            }
            return parent::validate();
        }      
        //Override default is_empty function
        function is_empty(){
            if( strlen($this->data) ){
                return false;
            }
            return true;
        }
    }


    $FUNCS->register_udf('color', 'Color', 1/*repeatable*/);
