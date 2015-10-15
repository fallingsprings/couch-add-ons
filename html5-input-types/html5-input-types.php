<?php
    /*
    The contents of this file are subject to the Common Public Attribution License
    Version 1.0 (the "License"); you may not use this file except in compliance with
    the License. You may obtain a copy of the License at
    http://www.couchcms.com/cpal.html. The License is based on the Mozilla
    Public License Version 1.1 but Sections 14 and 15 have been added to cover use
    of software over a computer network and provide for limited attribution for the
    Original Developer. In addition, Exhibit A has been modified to be consistent with
    Exhibit B.

    Software distributed under the License is distributed on an "AS IS" basis, WITHOUT
    WARRANTY OF ANY KIND, either express or implied. See the License for the
    specific language governing rights and limitations under the License.

    The Original Code is the CouchCMS project.

    The Original Developer is the Initial Developer.

    The Initial Developer of the Original Code is Kamran Kashif (kksidd@couchcms.com).
    All portions of the code written by Initial Developer are Copyright (c) 2009, 2010
    the Initial Developer. All Rights Reserved.

    Contributor(s):

    Alternatively, the contents of this file may be used under the terms of the
    CouchCMS Commercial License (the CCCL), in which case the provisions of
    the CCCL are applicable instead of those above.

    If you wish to allow use of your version of this file only under the terms of the
    CCCL and not to allow others to use your version of this file under the CPAL, indicate
    your decision by deleting the provisions above and replace them with the notice
    and other provisions required by the CCCL. If you do not delete the provisions
    above, a recipient may use your version of this file under either the CPAL or the
    CCCL.
    */

    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

   // UDF for HTML5 Input Types
    class HTML5InputTypes extends KUserDefinedFormField{
        
        function handle_params( $params ){    
            global $FUNCS;  
            $attr = $FUNCS->get_named_vars(
                        array( 'min'=>'',
                               'max'=>'',
                               'step'=>'',
                              ),
                        $params);
            $attr['min'] = trim($attr['min']);
            $attr['max'] = trim($attr['max']);
            $attr['step'] = trim($attr['step']);
            
            return $attr;  
        }
       
       // Render input tag
        function _render( $input_name, $input_id, $extra='' ){
            global $FUNCS, $CTX;
            $value = $this->get_data();
            $date_pattern = '/^(\d{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';
            $time_pattern = '/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9])(.([0-9]?[0-9]?[0-9]))?)?$/';
                                    
        //Sanitize parameters based on input type
             //MIXED Types
            if ( $this->step && $this->step<=0 ) die("ERROR: Tag \"input\" type \"".$this->k_type."\" - 'step' attribute must be a positive number.");
            if ( $this->min && $this->max && $this->min >= $this->max ) die("ERROR: Tag \"input\" type \"".$this->k_type."\" - 'max' attribute must be greater than 'min' attribute.");
            
           //TIME
            if ( $this->k_type == 'time' && $this->step && ($this->step<=0 || $this->step>60) ) die("ERROR: Tag \"input\" type \"time\" - 'step' attribute must be a positive number less than 60.");
            if ( $this->k_type == 'time' && $this->min && !preg_match($time_pattern, $this->min) ) die("ERROR: Tag \"input\" type \"time\" - 'min' attribute not a valid time string.");
            if ( $this->k_type == 'time' && $this->max && !preg_match($time_pattern, $this->max) ) die("ERROR: Tag \"input\" type \"time\" - 'max' attribute not a valid time string.");
            
            //DATE
            if ( $this->k_type == 'date' && $this->step && $this->step != intval($this->step) ) die("ERROR: Tag \"input\" type \"date\" - 'step' attribute must be an integer.");
            if ( $this->k_type == 'date' && $this->min && !preg_match($date_pattern, $this->min) ) die("ERROR: Tag \"input\" type \"date\" - 'min' attribute not a valid date string.");
            if ( $this->k_type == 'date' && $this->max && !preg_match($date_pattern, $this->max) ) die("ERROR: Tag \"input\" type \"date\" - 'max' attribute not a valid date string.");
            

            
            //Provides for HTML5 validation of required fields
            if( $this->required ){
                $extra .= ' required="required"';
            }
            
            //render min, max, and step attributes if present
            if ( $this->min ) {
                $extra .= ' min="'.$this->min.'"';
            }
            if ( $this->max ) {
                $extra .= ' max="'.$this->max.'"';
            }
            if ( $this->step ) {
                $extra .= ' step="'.$this->step.'"';
            }
            
            $html .= '<input type="'.$this->k_type.'" name="'.$input_name.'"  id="'.$input_id.'" value="'.htmlspecialchars( $value, ENT_QUOTES, K_CHARSET ).'" '.$extra.'/>'; 
            return $this->wrap_fieldset( $html );
        }
        
        function validate(){
            global $FUNCS;
            $value = $this->get_data();
               
            //Validate required fields. If not required, don't validate empty fields.
            if( $this->is_empty() ){
                if( $this->required ){
                    return parent::validate();
                }
                else{
                    return true;
                }
            }

            //Provide Couch validation for "email" and "url" types
            if( $this->k_type == 'email' ){
                $this->validator = 'email';
            }
            if( $this->k_type == 'url' ){
                $this->validator = 'url';
            }
            
           //Validate number and range input
            if ( $this->k_type == 'number' || $this->k_type == 'range' ){
                //Is it a number?
                if ( !is_numeric($value) ) {
                     $this->err_msg = 'Not a number';
                    return false;
                }
                
                //Is it in range?
                if ( $this->min != '' && $value < $this->min ){
                    $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.' ;
                    return false;
                }
                if ( $this->max != '' && $value > $this->max ){
                    $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.' ;
                    return false;
                }
                
                //Is it a valid number?
                if ( $this->step ){
                    $step = $this->step;
                }
                else{
                    $step = '1';
                }
                        
                //Must be a multiple of step.
                $val = ($value - $this->min) / $step;
                        
                if( $val != intval($val) ){
                    $lower = intval($val) * $step + $this->min;
                    $higher = intval($val + 1) * $step + $this->min;
                    if( $value < 0 && $this->min == '') $higher = intval($val - 1) * $step;
                    $this->err_msg = 'Not a valid number. The two nearest valid numbers are  '.$lower.' and '.$higher.'.';
                    return false;
                }
            }
            
            //Validate time input
            //TODO: determine valid times considering step
            if ($this->k_type == 'time' ){
                $pattern = '/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9])(.([0-9]?[0-9]?[0-9]))?)?$/';
                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = 'Not a valid time.';
                    return false;
                }
                
                //Is it in range?
                // First normalize values, then compare.
                function normalize_time($value){
                    $time = explode(':', $value);
                    $hours = $time[0];
                    $minutes = $time[1];
                    $seconds = $time[2];
                    if ( !$seconds ) $seconds = '00';
                    $time = $hours.$minutes.$seconds;
                    
                    return $time;
                }
                    
                if ( $this->min ){
                    $time = normalize_time($value);
                    $min = normalize_time($this->min);
                        
                    if ( $time < $min ){
                        $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.';
                        return false;
                    }                                              
                }

                if ( $this->max ){
                    $max = normalize_time($this->max);
                        
                    if ( $time > $max ){
                        $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.';
                        return false;
                    }
                }
            }

            //Validate date input
            //TODO: determine valid dates considering step
            if ($this->k_type == 'date' ){
                $pattern = '/^(\d{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';
                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = 'Not a valid date.';
                    return false;
                }
                        
                $date = explode('-', $value);
                $month = $date[1];
                $day = $date[2];
                
                //Shorter months
                if ( $day == '31' && ($month == '02' || $month == '04' || $month == '06' || $month == '09' || $month == '11') ){
                    $this->err_msg = 'Not a valid date.';
                    return false;
                }
                //February
                if ( $month == '02' && $day == '30' ){
                    $this->err_msg = 'Not a valid date.';
                    return false;
                }
                //Leap Year
                if ( $month == '02' && !date('L', $value) && $day == '29' ){
                    $this->err_msg = 'Not a valid date.';
                    return false;
                }
                
                //Is it in range?
                if ( $this->min && $value < $this->min ){
                    $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.' ;
                    return false;
                }
                if ( $this->max && $value > $this->max ){
                    $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.' ;
                    return false;
                }
           }
            
            //Validate color input
            if ($this->k_type == 'color' ){
                $pattern = '/^#([A-Fa-f0-9]{6})$/';
                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = 'Not a valid hexadecimal color value.';
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


    //Unobtrusive Spam Protection
    //<cms:too_many_urls in='frm_my_field' allowed='1' />
    class KTooManyUrls{
        
        function too_many_urls_handler( $params, $node ){
            global $FUNCS, $CTX;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}
            
            extract( $FUNCS->get_named_vars(
                array(
                       'in'=>'', /* field to test */
                       'allowed' =>'1', /* URLs allowed. Default is 1 */
                      ),
                $params)
            );
           
            // sanitize params    
            $allowed = trim( $allowed );            
            $in = trim( $in );
            
            $target = $CTX->get( $in );             
            $url_patterns = '/((\bhttps{0,1}:\/\/|<\s*a\s+href=["'."'".']{0,1})[-a-z0-9.]+\b)|(^|[^-a-z_.0-9]+)(?<!@)([-a-z0-9]+\.)+(com|net|org|edu|gov|mil|aero|asia|biz|cat|coop|info|int|jobs|mobi|museum|name|post|pro|tel|travel|xxx|ac|ad|ae|af|ag|ai|al|am|an|ao|aq|ar|as|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|cr|cs|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gg|gh|gi|gl|gm|gn|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|im|in|io|iq|ir|is|it|ja|je|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mg|mh|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|mv|mw|mx|my|mz|na|nc|ne|nf|ng|ni|nl|no|np|nr|nu|nz|om|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|ps|pt|pw|py|qa|re|ro|rs|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|ss|st|su|sv|sx|sy|sz|tc|td|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)\b|(^|[^-a-z_.0-9]+)(?<!@)([-a-z0-9]+\.)\b/msi';

            $url_count = preg_match_all($url_patterns, $target, $each_url);
            if( $url_count > $allowed ){return true;} else{return false;}
        }
    }
    
    $FUNCS->register_tag( 'too_many_urls', array('KTooManyUrls', 'too_many_urls_handler') );
        
    $FUNCS->register_udform_field( 'search', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'email', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'url', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'tel', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'number', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'range', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'date', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'time', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'color', 'HTML5InputTypes' );  