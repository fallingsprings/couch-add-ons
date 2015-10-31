<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    // UDF for HTML5 Input Types
    class HTML5InputTypes extends KUserDefinedFormField{

        function handle_params( $params ){
            global $FUNCS;
            $attr = $FUNCS->get_named_vars(
                        array(
                               'value'=>'',
                               'min'=>'',
                               'max'=>'',
                               'step'=>'',
                               'validate'=>'1',
                              ),
                        $params);
            $attr['value'] = trim($attr['value']);
            $attr['min'] = trim($attr['min']);
            $attr['max'] = trim($attr['max']);
            $attr['step'] = trim($attr['step']);
            $attr['validate'] = trim($attr['validate']);

            return $attr;
        }

       // Render input tag
        function _render( $input_name, $input_id, $extra='' ){
            global $FUNCS, $CTX;

            $value = $this->get_data();

            //Provides for HTML5 validation of required fields
            if( $this->required ){
                $extra .= ' required="required"';
            }

            //render min, max, and step attributes if present
            if ( $this->min != '' ) {
                $extra .= ' min="'.$this->min.'"';
            }
            if ( $this->max != '' ) {
                $extra .= ' max="'.$this->max.'"';
            }
            if ( $this->step != '' ) {
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

    class HTML5InputEmail extends HTML5InputTypes{
        function validate(){
            global $FUNCS;
            if ( $this->validate != '0' ){
                $this->validator = 'email'; 
            }
            return parent::validate();
        }
    }
        
    class HTML5InputUrl extends HTML5InputTypes{
        function validate(){
            global $FUNCS;
            if ( $this->validate != '0' ){
                $this->validator = 'url';
            }
            return parent::validate();
        }
    }
        
    class HTML5InputColor extends HTML5InputTypes{
        function handle_params( $params ){
            global $FUNCS;
            
            $attr = parent::handle_params( $params );

            extract( $FUNCS->get_named_vars(
                        array(
                              'value'=>'#000000' //Defaults to black.
                             ),
                        $params) );
            
            //sanitize color value.
            $value = strtolower( trim($value) );
            $pattern = '/^#([A-Fa-f0-9]{6})$/';
            
            if ( !preg_match( $pattern, $value ) ){
                 die( "ERROR: Tag \"input\" type \"color\" - '".$value."' is not a valid hexadecimal color (#000000)." );
            }
            
            $attr['value'] = $value;
            return $attr;  
         }
        
        function validate(){
            global $FUNCS;
            if ( $this->validate != '0' ){            
                $value = $this->get_data();
                $pattern = '/^#([A-Fa-f0-9]{6})$/';
                
                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = 'Not a valid hexadecimal color value.';
                    return false;
                }
            }
            return parent::validate();
        }
    }
        
       class HTML5InputNumber extends HTML5InputTypes{
        function handle_params( $params ){
            global $FUNCS;

            $attr = parent::handle_params( $params );

            extract( $FUNCS->get_named_vars(
                        array(
                              'type'=>''
                             ),
                        $params) );
            $type = strtolower( trim($type) );

            // sanitize parameters
            if( $attr['value'] != '' && !is_numeric($attr['value']) ){
                die( "ERROR: Tag \"input\" type \"".$type."\" - 'value' attribute must be numeric." );
            }
            if( $attr['min'] != '' && !is_numeric($attr['min']) ){
                die( "ERROR: Tag \"input\" type \"".$type."\" - 'min' attribute must be numeric." );
            }
            if( $attr['max'] != '' && !is_numeric($attr['max']) ){
                die( "ERROR: Tag \"input\" type \"".$type."\" - 'max' attribute must be numeric." );
            }
            if ( $attr['min'] != '' && $attr['max'] != '' && $attr['min'] >= $attr['max'] ){
                die( "ERROR: Tag \"input\" type \"".$type."\" - 'max' attribute must be greater than 'min' attribute." );
            }
            if( $attr['step'] == '' ){
                $attr['step'] = '1';
            }
            else{
                if( !is_numeric($attr['step']) || $attr['step'] <= 0 ){
                    die( "ERROR: Tag \"input\" type \"".$type."\" - 'step' attribute must be a positive number." );
                }
            }

            return $attr;
        }

        function validate(){
            global $FUNCS;

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();

// $value=100; // fudge values for testing invalid inputs

                //Is it a number?
                if( !is_numeric($value) ){
                    $this->err_msg = 'Not a number';
                    return false;
                }

                //Is it in range?
                if( $this->min != '' && $value < $this->min ){
                    $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.' ;
                    return false;
                }
                if( $this->max != '' && $value > $this->max ){
                    $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.' ;
                    return false;
                }

                //Is it a valid number?
                $step = $this->step;

                //Must be a multiple of step.
                $min = strlen( $this->min ) ? $this->min : 0;
                $val = ( $value < 0 ) ? ( $value + $min ) : ( $value - $min );
                $val = round( ($val / $step), 2 );

                if( $val != intval($val) ){ // not an exact multiple of step
                    $val = intval($val) * $step;    // discard the fractional part
                    if( $value < 0 ){
                        $higher = $val - $min;
                        $lower = $higher - $step;
                    }
                    else{
                        $lower = $val + $min;
                        $higher = $lower + $step;
                    }

                    if( $this->max != '' && $higher > $this->max ){
                        $higher = null;
                    }
                    if( $this->min != '' && $lower < $this->min ){
                        $lower = null;
                    }

                    if( !is_null($lower) && !is_null($higher) ){
                        $this->err_msg = 'Not a valid number. The two nearest valid numbers are  '.$lower.' and '.$higher.'.';
                    }
                    else{
                        $valid_value = ( !is_null($lower) ) ? $lower : $higher;
                        $this->err_msg = 'Not a valid number. The nearest valid number is '.$valid_value.'.';
                    }

                    return false;
                }
            }

            // Values are fine. Let parent handle custom validators, if any specified
            return parent::validate();
        }
    }

    class HTML5InputRange extends HTML5InputNumber{
        function handle_params( $params ){
            global $FUNCS;

            $attr = parent::handle_params( $params );

            // Unlike with the number (spinner) input type, the range (slider) input type has reasonable defaults for min, max, step, and value
            if( $attr['min'] == '' ){
                $attr['min'] = '0';
            }
            if( $attr['max'] == '' ){
                $attr['max'] = '100';
            }

            return $attr;
        }
    }

       class HTML5InputDate extends HTML5InputTypes{
        function handle_params( $params ){
            global $FUNCS;

            $attr = parent::handle_params( $params );
            $pattern = '/^(\d{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';


            // sanitize parameters
            if( $attr['value'] != '' && !preg_match($pattern, $attr['value']) ){
                die( "ERROR: Tag \"input\" type \"date\" - 'value' attribute must be a valid date string (YYYY-MM-DD)." );
            }
            if ( $attr['min'] != '' && !preg_match($pattern, $attr['min']) ){
                die("ERROR: Tag \"input\" type \"date\" - 'min' attribute not a valid date string (YYYY-MM-DD).");
            }
            if( $attr['max'] != '' && !preg_match($pattern, $attr['max']) ){
                die( "ERROR: Tag \"input\" type \"date\" - 'max' attribute not a valid date string (YYYY-MM-DD)." );
            }
            if ( $attr['min'] != '' && $attr['max'] != '' && $attr['min'] >= $attr['max'] ){
                die( "ERROR: Tag \"input\" type \"date\" - 'max' attribute must be greater than 'min' attribute." );
            }
            if( $attr['step'] == '' ){
                $attr['step'] = '1';
            }
            else{
                if( !is_numeric($attr['step']) || $attr['step'] <= 0 ){
                    die( "ERROR: Tag \"input\" type \"date\" - 'step' attribute must be a positive number." );
                }
            }
            return $attr;
        }

        function validate(){
            global $FUNCS;

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();
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
                if ( $this->min !='' && $value < $this->min ){
                    $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.' ;
                    return false;
                }
                if ( $this->max && $value > $this->max ){
                    $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.' ;
                    return false;
                }
            }

        // Values are fine. Let parent handle custom validators, if any specified
        return parent::validate();
        }
    }

    class HTML5InputTime extends HTML5InputTypes{
        function handle_params( $params ){
            global $FUNCS;

            $attr = parent::handle_params( $params );
            $pattern = '/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9])(.([0-9]?[0-9]?[0-9]))?)?$/';


            // sanitize parameters
            if( $attr['value'] != '' && !preg_match($pattern, $attr['value']) ){
                die( "ERROR: Tag \"input\" type \"time\" - 'value' attribute must be a valid time string (00:00{.00.000})." );
            }
            if ( $attr['min'] != '' && !preg_match($pattern, $attr['min']) ){
                die("ERROR: Tag \"input\" type \"time\" - 'min' attribute not a valid time string (00:00{.00.000}).");
            }
            if( $attr['max'] != '' && !preg_match($pattern, $attr['max']) ){
                die( "ERROR: Tag \"input\" type \"time\" - 'max' attribute not a valid time string (00:00{.00.000})." );
            }
            if ( $attr['min'] != '' && $attr['max'] != '' ) {
                // Normalize time values before comparing.
                $min = explode(':', $attr['min']);
                $hours = $min[0];
                $minutes = $min[1];
                $seconds = $min[2];
                if ( !$seconds ) $seconds = '00';
                $min = $hours.$minutes.$seconds;

                $max = explode(':', $attr['max']);
                $hours = $max[0];
                $minutes = $max[1];
                $seconds = $max[2];
                if ( !$seconds ) $seconds = '00';
                $max = $hours.$minutes.$seconds;

                if( $min > $max ){
                die( "ERROR: Tag \"input\" type \"time\" - 'max' attribute must be greater than 'min' attribute." );
                }
            }
            if( $attr['step'] == '' ){
                $attr['step'] = '1';
            }
            else{
                if( !is_numeric($attr['step']) || $attr['step'] <= 0 ){
                    die( "ERROR: Tag \"input\" type \"time\" - 'step' attribute must be a positive number." );
                }
            }
            return $attr;
        }

        function validate(){
            global $FUNCS;

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();
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
                if ( $this->min !='' && normalize_time($value) < normalize_time($this->min) ){
                    $this->err_msg = 'Out of range. Minimum value is '.$this->min.'.' ;
                    return false;
                }
                if ( $this->max !='' && normalize_time($value) > normalize_time($this->max) ){
                    $this->err_msg = 'Out of range. Maximum value is '.$this->max.'.' ;
                    return false;
                }
            }

        // Values are fine. Let parent handle custom validators, if any specified
        return parent::validate();
        }
    }

    $FUNCS->register_udform_field( 'search', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'tel', 'HTML5InputTypes' );
    $FUNCS->register_udform_field( 'email', 'HTML5InputEmail' );
    $FUNCS->register_udform_field( 'url', 'HTML5InputUrl' );
    $FUNCS->register_udform_field( 'color', 'HTML5InputColor' );
    $FUNCS->register_udform_field( 'number', 'HTML5InputNumber' );
    $FUNCS->register_udform_field( 'range', 'HTML5InputRange' );
    $FUNCS->register_udform_field( 'date', 'HTML5InputDate' );
    $FUNCS->register_udform_field( 'time', 'HTML5InputTime' );
