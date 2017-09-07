<?php
function minify_css($css){
    //Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    //Remove tabs, spaces, and line breaks
    $css = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $css);
    //whitespace around punctuation
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    //final semicolon
    $css = preg_replace('/;}/', '}', $css);
    return $css;
}

class MinifyJsCss{
    static function minify_js_css( $params, $node ){
        global $FUNCS;
        extract( $FUNCS->get_named_vars(
            array(
                    'filetype'=>'',
                    'into' =>'',
                    ),
            $params)
        );
           
        // sanitize params
        $filetype = strtolower( trim($filetype) );
        if( $filetype !='css' && $filetype != 'js' ) {die("ERROR: Tag \"".$node->name."\" - Must specify either 'css' or 'js'.");}
        $files = array();
        $output_link = ($into) ? K_SITE_URL . trim( $into ) : '';
        $output_file = ($into) ? K_SITE_DIR . trim( $into ) : '';
        
        //load JShrink if needed
        if($filetype == 'js'){
            require_once( K_COUCH_DIR.'addons/minify-js-css/JShrink.php' );
        }

        //Add listed files to an array and sanitize
        //Split on whitespace and commas
        if($node->children[0]->text){
            foreach( $node->children as $child ){ $file_list .= $child->get_HTML(); }
            $files = preg_split('/[\s,+]/', $file_list, -1, PREG_SPLIT_NO_EMPTY);
            foreach( $files as &$file ){
                $file = K_SITE_DIR . trim($file, '/,');
            }
        }
        
        //Combine files
        foreach( $files as $code ){ $content .= file_get_contents( $code ); }

        //compare modification dates to output file
        if($output_file){
            foreach( $files as $item ){
                if( filemtime($item) > filemtime($output_file) ){
                    $modified = 1; break;
                }
            }
            //No new modifications. Render 'link' or 'script' tag. Done.
            if (!$modified && $filetype == 'css'){
                return '<link rel="stylesheet" href="'.$output_link.'?'.filemtime($output_file).'" />';
                }
            if (!$modified && $filetype == 'js'){
                return '<script type="text/javascript" src="'.$output_link.'?'.filemtime($output_file).'"></script>';
            }
        }
        
        //minify combined files
        if ($filetype == 'css'){
            $output = minify_css($content);
        }
        if ($filetype == 'js'){
            $output = \JShrink\Minifier::minify($content);
        }
        
        if( !$output_file ){ //No output file. Embed output on page. Done.
            if ($filetype == 'css'){
                return '<style>' . $output . '</style>';
            }
            if ($filetype == 'js'){
                return '<script type="text/javascript">' . $output . '</script>';
            }
        }
        
        //Create new output file and render tag. Done.
        file_put_contents($output_file, $output);
        if ($filetype == 'css'){
            return '<link rel="stylesheet" href="'.$output_link.'?'.filemtime($output_file).'" />';
        }
        if ($filetype == 'js'){
            return '<script type="text/javascript" src="'.$output_link.'?'.filemtime($output_file).'"></script>';
        }
    }
}
$FUNCS->register_tag( 'minify', array('MinifyJsCss', 'minify_js_css') );
