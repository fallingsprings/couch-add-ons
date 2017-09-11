<?php
function minify_css($css){
    //Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    //Remove tabs, spaces, and line breaks
    $css = preg_replace(array('/\s{2,}/', '/[\t\n]/'), '', $css);
    //whitespace around punctuation
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    //final semicolon
    $css = preg_replace('/;}/', '}', $css);
    return $css;
}

class MinifyJsCss{
    static function minify_js_css( $params, $node ){
           
        // sanitize params
        $filetype = strtolower(trim($params[0]['rhs']));
        if($filetype != 'css' && $filetype != 'js'){die("ERROR: Tag \"".$node->name."\" - Must specify either 'css' or 'js'.");}
        $output_file = ($params[1]['rhs']) ? K_SITE_DIR . trim($params[1]['rhs']) : '';
        $output_link = ($output_file) ? K_SITE_URL . trim($params[1]['rhs']) : '';
        
        //Add listed files to an array and sanitize
        if(trim($node->children[0]->text)){
            foreach( $node->children as $child ){ 
                $file_list .= $child->get_HTML(); 
            }
            //Split on whitespace and commas
            $files = preg_split('/[\s,+]/', $file_list, -1, PREG_SPLIT_NO_EMPTY);
            //full path
            foreach( $files as &$file ){
                $file = K_SITE_DIR . ltrim($file, '/');
            }
        }else{
            die("ERROR: Tag \"".$node->name."\" - No files were listed.");
        }
        
        //compare modification dates to output file
        if($output_file){
            foreach($files as $item){
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
        
        //Combine files
        foreach($files as $code){ $content .= file_get_contents($code); }
        //load JShrink if needed
        if($filetype == 'js'){ 
            require_once( K_COUCH_DIR.'addons/minify-js-css/JShrink.php' ); 
        }
        //minify combined files
        if ($filetype == 'css'){ $output = minify_css($content); }
        if ($filetype == 'js'){ $output = \JShrink\Minifier::minify($content); }
        
        //No output file. Embed output on page. Done.
        if(!$output_file){ 
            if ($filetype == 'css'){
                return '<style>' . $output . '</style>';
            }
            if ($filetype == 'js'){
                return '<script type="text/javascript">' . $output . '</script>';
            }
        }
        
        //Create new output file. Render tag. Done.
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
