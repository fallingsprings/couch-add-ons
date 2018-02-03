<?php
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

    // UDF for HTML5 Input Types
    class HTML5InputTypes extends KUserDefinedFormField{

        static function handle_params( $params, $node ){
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
            $attr['step'] = strtolower( trim($attr['step']) );
            $attr['validate'] = trim($attr['validate']);

            $attr['preset'] = strlen( $attr['value'] ) ? $attr['value'] : '';

            return $attr;
        }

       // Render input tag
        function _render( $input_name, $input_id, $extra='', $dynamic_insertion=0 ){
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
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = parent::handle_params( $params, $node );

            extract( $FUNCS->get_named_vars(
                        array(
                              'value'=>'#000000' //Defaults to black.
                             ),
                        $params) );

            //sanitize color value.
            $value = strtolower( trim($value) );
            $pattern = '/^#([A-Fa-f0-9]{6})$/';

            if ( !preg_match( $pattern, $value ) ){
                 die( "ERROR: Tag \"input\" type \"color\" - '".$value."' is not a valid hexadecimal color." );
            }

            $attr['value'] = $value;
            return $attr;
         }

        function validate(){
            global $FUNCS;
            require('lang/EN.php');
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/html5-input-types/lang/' . K_ADMIN_LANG . '.php') ){
            require('lang/'.K_ADMIN_LANG.'.php');
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();
                $pattern = '/^#([A-Fa-f0-9]{6})$/';

                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = $color_error;
                    return false;
                }
            }
            return parent::validate();
        }
    }

       class HTML5InputNumber extends HTML5InputTypes{
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = parent::handle_params( $params, $node );

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
            if ( $attr['min'] != '' && $attr['max'] != '' && $attr['min'] > $attr['max'] ){
                die( "ERROR: Tag \"input\" type \"".$type."\" - 'max' attribute cannot be less than 'min' attribute." );
            }
            if ( $attr['step'] != 'any' ){
                if( $attr['step'] == '' ){
                    $attr['step'] = '1';
                }
                else{
                    if( !is_numeric($attr['step']) || $attr['step'] <= 0 ){
                        die( "ERROR: Tag \"input\" type \"".$type."\" - 'step' attribute must be a positive number." );
                    }
                }
            }
            return $attr;
        }

        function validate(){
            global $FUNCS;
            require('lang/EN.php');
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/html5-input-types/lang/' . K_ADMIN_LANG . '.php') ){
            require('lang/'.K_ADMIN_LANG.'.php');
            }

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();

// $value=100; // fudge values for testing invalid inputs

                //Is it a number?
                if( !is_numeric($value) ){
                    $this->err_msg = $number_error;
                    return false;
                }

                //Is it in range?
                if( $this->min != '' && $value < $this->min ){
                    $this->err_msg = $out_of_range_min_error . $this->min . '.' ;
                    return false;
                }
                if( $this->max != '' && $value > $this->max ){
                    $this->err_msg = $out_of_range_min_error . $this->max . '.' ;
                    return false;
                }

                //Is it in step?
                if ( $this->step != 'any' ) {
                    $basis = strlen( $this->min ) ? $this->min : 0;
                    $basis = !strlen( $this->min ) && strlen( $this->preset ) ? $this->preset : $basis;
                    $diff = abs( $basis - $value );
                    $number_of_steps = round( ($diff / $this->step), 12 );

                    if ( $number_of_steps != intval($number_of_steps) ){ //Not a multiple of step
                        $number_of_steps = intval($number_of_steps) * $this->step; //Discard the remainder
                        if( $value < $basis ){
                            $higher = $basis - $number_of_steps;
                            $lower = $higher - $this->step;
                        }
                        if( $value > $basis ){
                            $lower = $basis + $number_of_steps;
                            $higher = $lower + $this->step;
                        }

                        if( $this->max != '' && $higher > $this->max ){
                            $higher = null;
                        }
                        if( !is_null($higher) ){
                            $this->err_msg = $number_step_error . $lower . $and . $higher . '.';
                        }
                        else{
                            $this->err_msg =  $number_step_edge_error . $lower . '.';
                        }
                        return false;
                    }
                }
            }
            // Values are fine. Let parent handle custom validators, if any specified
            return parent::validate();
        }
    }

    class HTML5InputRange extends HTML5InputNumber{
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = parent::handle_params( $params, $node );

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
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = parent::handle_params( $params, $node );
            $pattern = '/^(\d{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';

            // sanitize parameters
            $date = explode('-', $attr['value']);
            if( $attr['value'] != '' && (!preg_match( $pattern, $attr['value'] ) || !checkdate($date[1], $date[2], $date[0])) ){
                die( "ERROR: Tag \"input\" type \"date\" - 'value' attribute must be a valid date string." );
            }
            $date = explode('-', $attr['min']);
            if ( $attr['min'] != '' && (!preg_match( $pattern, $attr['min'] ) || !checkdate($date[1], $date[2], $date[0])) ){
                die("ERROR: Tag \"input\" type \"date\" - 'min' attribute not a valid date string.");
            }
            $date = explode('-', $attr['max']);
            if( $attr['max'] != '' && (!preg_match( $pattern, $attr['max'] ) || !checkdate($date[1], $date[2], $date[0])) ){
                die( "ERROR: Tag \"input\" type \"date\" - 'max' attribute not a valid date string." );
            }
            if ( $attr['min'] != '' && $attr['max'] != '' && $attr['min'] > $attr['max'] ){
                die( "ERROR: Tag \"input\" type \"date\" - 'max' attribute cannot be less than 'min' attribute." );
            }
            if ( $attr['step'] != 'any' ){
                if( $attr['step'] == '' ){
                    $attr['step'] = '1';
                }
                else{
                    if( !is_numeric($attr['step']) || $attr['step'] <= 0 || $attr['step'] != intval($attr['step']) ){
                        die( "ERROR: Tag \"input\" type \"time\" - 'step' attribute must be a positive number." );
                    }
                }
            }
            return $attr;
        }

        function validate(){
            global $FUNCS;
            require('lang/EN.php');
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/html5-input-types/lang/' . K_ADMIN_LANG . '.php') ){
            require('lang/'.K_ADMIN_LANG.'.php');
            }

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                //Is it a valid date?
                $value = $this->get_data();
                $pattern = '/^(\d{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/';
                $date = explode('-', $value);
                if ( !preg_match( $pattern, $value ) || !checkdate($date[1], $date[2], $date[0]) ){
                    $this->err_msg = $date_error;
                    return false;
                }

                //Is it in range?
                if ( $this->min !='' && $value < $this->min ){
                    $this->err_msg = $out_of_range_min_error . $this->min . '.' ;
                    return false;
                }
                if ( $this->max && $value > $this->max ){
                    $this->err_msg = $out_of_range_max_error . $this->max . '.' ;
                    return false;
                }

                //Is it in step?
                if ( $this->step != 'any' ) {
                    $date = date_create($value);
                    $basis = strlen( $this->min ) ? date_create($this->min) : date_create('0001-01-00'); //lowest valid date minus 1. Kinda funky, but it works.
                    $basis = !strlen( $this->min ) && strlen( $this->preset ) ? date_create($this->preset) : $basis;
                    $diff = $basis->diff($date)->format('%a');
                    $number_of_steps = round($diff/$this->step, 2);

                    if ( $number_of_steps != intval($number_of_steps) ){ //Not a multiple of step
                        $number_of_steps = intval($number_of_steps) * $this->step; //Discard the remainder

                        if( $date < $basis ){
                            $higher = $basis->sub(new DateInterval('P'.$number_of_steps.'D'))->format('Y-m-d');
                            $lower = date_create($higher)->sub(new DateInterval('P'.$this->step.'D'))->format('Y-m-d');
                        }
                        if( $date > $basis ){
                            $lower = $basis->add(new DateInterval('P'.$number_of_steps.'D'))->format('Y-m-d');
                            $higher = date_create($lower)->add(new DateInterval('P'.$this->step.'D'))->format('Y-m-d');
                        }

                        if ( $basis == date_create('0001-01-00') && $diff < $this->step ){
                            $lower = null;
                        }
                        if ( $this->max != '' && $higher > $this->max){
                            $higher = null;
                        }

                        if( !is_null($lower) && !is_null($higher) ){
                            $this->err_msg = $date_step_error . $lower . $and . $higher . '.';
                        }
                        else{
                            $valid_value = ( !is_null($lower) ) ? $lower : $higher;
                            $this->err_msg = $date_step_edge_error . $valid_value . '.';
                        }
                        return false;
                    }
                }
            }

        // Values are fine. Let parent handle custom validators, if any specified
        return parent::validate();
        }
    }

    class HTML5InputTime extends HTML5InputTypes{
        static function handle_params( $params, $node ){
            global $FUNCS;

            $attr = parent::handle_params( $params, $node );
            $pattern = '/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9])(.([0-9]?[0-9]?[0-9]))?)?$/';

            // sanitize parameters
            if( $attr['value'] != '' && !preg_match($pattern, $attr['value']) ){
                die( "ERROR: Tag \"input\" type \"time\" - 'value' attribute must be a valid time string." );
            }
            if ( $attr['min'] != '' && !preg_match($pattern, $attr['min']) ){
                die("ERROR: Tag \"input\" type \"time\" - 'min' attribute not a valid time string.");
            }
            if( $attr['max'] != '' && !preg_match($pattern, $attr['max']) ){
                die( "ERROR: Tag \"input\" type \"time\" - 'max' attribute not a valid time string." );
            }
            if ( $attr['min'] != '' && $attr['max'] != '' ) {
                // Normalize time values before comparing.
                // Too many digits in strtotimeto, so subtract 1000000000 to make room for milliseconds
                $min = strtotime($attr['min']) - 1000000000;
                $max = strtotime($attr['max']) - 1000000000;
                //preserve milliseconds
                $min_ms = explode('.', $attr['min']);
                $max_ms = explode('.', $attr['max']);

                if ( $min_ms[1] ) $min = $min.'.'.$min_ms[1];
                if ( $max_ms[1] ) $max = $max.'.'.$max_ms[1];
                if( $min > $max ){
                die( "ERROR: Tag \"input\" type \"time\" - 'max' attribute cannot be less than 'min' attribute." );
                }
            }

            //The HTML5 Standard (http://www.w3.org/TR/html5/) specifies a default step of 60.
            //However, the Chrome browser uses a default step of 1.
            //Given this mismatch, I've chosen to follow the behavior of the only major browser to implement type="time". This decision may need to be revisited in the future.
            if ( $attr['step'] != 'any' ){
                if( $attr['step'] == '' ){
                    $attr['step'] = '1';
                }
                else{
                    if( !is_numeric($attr['step']) || $attr['step'] <= 0 ){
                        die( "ERROR: Tag \"input\" type \"time\" - 'step' attribute must be a positive number." );
                    }
                }
            }
            return $attr;
        }

        function validate(){
            global $FUNCS;
            require('lang/EN.php');
            if( K_ADMIN_LANG != 'EN' && file_exists(K_COUCH_DIR . 'addons/html5-input-types/lang/' . K_ADMIN_LANG . '.php') ){
            require('lang/'.K_ADMIN_LANG.'.php');
            }

            if( $this->is_empty() ){
                return parent::validate(); // will handle 'required'
            }

            if ( $this->validate != '0' ){
                $value = $this->get_data();
                $pattern = '/^(([0-1][0-9])|([2][0-3])):([0-5][0-9])(:([0-5][0-9])(.([0-9]?[0-9]?[0-9]))?)?$/';
                if ( !preg_match( $pattern, $value ) ){
                    $this->err_msg = $time_error;
                    return false;
                }

                //Is it in range?
                //Too many digits in strtotime, so subtract 1000000000 to make room for milliseconds
                $time = strtotime($value) - 1000000000;
                $min = strlen( $this->min ) ? $min = (strtotime($this->min) - 1000000000) : '';
                $max = strlen( $this->max ) ? $max = (strtotime($this->max) - 1000000000) : '';
                //preserve milliseconds
                $time_ms = explode('.', $value);
                $min_ms = explode('.', $this->min);
                $max_ms = explode('.', $this->max);

                if ( $time_ms[1] ) $time = $time.'.'.$time_ms[1];
                if ( $min_ms[1] ) $min = $min.'.'.$min_ms[1];
                if ( $max_ms[1] ) $max = $max.'.'.$max_ms[1];

                if ( $min !='' && $time < $min ){
                    $this->err_msg = $out_of_range_min_error . $this->min . '.';
                    return false;
                }
                if ( $max !='' && $time > $max ){
                    $this->err_msg = $out_of_range_max_error . $this->max . '.';
                    return false;
                }

                //Is it in step?
                if ( $this->step != 'any' ) {
                    if ( !strlen( $this->min ) && strlen( $this->preset ) ){
                        $preset = strtotime($this->preset) - 1000000000;
                        $preset_ms = explode('.', $this->preset);
                        if ( $preset_ms[1] ) $preset = $preset.'.'.$preset_ms[1];
                    }
                    $basis = !strlen( $this->min ) && strlen( $this->preset ) ? $preset : $min;
                    $diff = abs($basis - $time);
                    $number_of_steps = round($diff/$this->step, 3);

                    if ( $number_of_steps != intval($number_of_steps) ){ //Not a multiple of step
                        $number_of_steps = intval($number_of_steps) * $this->step; //Discard the remainder

                        if ( $time > $basis ){
                        $lower = $basis + $number_of_steps;
                        $higher = $lower + $this->step;
                        }
                        if ( $time < $basis ){
                        $higher = $basis - $number_of_steps;
                        $lower = $higher - $this->step;
                        }

                       if ( $lower < 448514000 ){ //Can't be less than 00:00
                            $lower = null;
                        }
                        else {
                            $lower_ms = explode('.', $lower); //preserve milliseconds
                            if( strlen($lower_ms[1]) ){
                                $lower_ms[1] = '.'.$lower_ms[1];
                                $lower_ms[1] = $lower_ms[1] * 1000;
                            }
                            $lower = date('H:i:s', $lower + 1000000000);
                            $lower =  strlen( $lower_ms[1] ) ? $lower.'.'.$lower_ms[1] : $lower;
                        }

                        if ( $this->max != '' && $higher > $max){
                            $higher = null;
                        }
                        else {
                            $higher_ms = explode('.', $higher); //preserve milliseconds
                            if( strlen($higher_ms[1]) ){
                                $higher_ms[1] = '.'.$higher_ms[1];
                                $higher_ms[1] = $higher_ms[1] * 1000;
                            }
                            $higher = date('H:i:s', $higher + 1000000000);
                            $higher =  strlen( $higher_ms[1] ) ? $higher.'.'.$higher_ms[1] : $higher;
                        }

                        if( !is_null($lower) && !is_null($higher) ){
                            $this->err_msg = $time_step_error . $lower . $and . $higher . '.';
                        }
                        else{
                            $valid_value = ( !is_null($lower) ) ? $lower : $higher;
                            $this->err_msg = $time_step_edge_error . $valid_value . '.';
                        }
                        return false;
                    }
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
