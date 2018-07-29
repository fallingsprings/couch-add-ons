<?php
if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

//Uncomment only one of these to timestamp links for cache-busting.
//define('MINIFY_TIMESTAMP_QUERYSTRING', 1);
//define('MINIFY_TIMESTAMP_FILENAME', 1);// Requires a rewrite rule in .htaccess
    //RewriteRule ^(.+)\^([\d-]+)\^\.(js|css)$ $1 [L]

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

function minify_timestamp( $link, $last_mod ){ 
    //versioning technique by @trendoman
    //https://www.couchcms.com/forum/viewtopic.php?f=8&t=10644
    $fileDetails = pathinfo( $link );
    $dirname = strtolower( $fileDetails["dirname"] );
    $filename = strtolower( $fileDetails["filename"] );
    $extension = strtolower( $fileDetails["extension"] );
    $output = $dirname . "/" . $filename . "." . $extension . "^" . $last_mod . "^." . $extension;
    return $output;
}

class MinifyJsCss{
    static function minify_js_css( $params, $node ){
        global $FUNCS;
        
        // sanitize params
        $filetype = strtolower(trim($params[0]['rhs']));
        if($filetype != 'css' && $filetype != 'js'){die("ERROR: Tag \"".$node->name."\" - Must specify either 'css' or 'js'.");}
        $filepath = trim(str_replace( K_SITE_URL, "", $params[1]['rhs'] ));
        $output_file = ($filepath) ? K_SITE_DIR . $filepath : '';
        $output_link = ($filepath) ? K_SITE_URL . $filepath : '';
        $last_mod = (is_file($output_file)) ? filemtime($output_file) : 0;
        
        //save tag attributes
        $i=0;
        foreach($params as $attribs){
            if($i > 1){
                $tag_attributes .= ' ' . $attribs['lhs'] . '="' . $attribs['rhs'].'"';
            }
            $i++;
        }
        
        //Add listed files to an array and sanitize
        foreach( $node->children as $child ){
            $file_list .= $child->get_HTML();
        }
            //Split on /*CODE*/ chunks
            $chunks = explode('/*CODE*/', $file_list);
            //separate into files and code chunks
            if(count($chunks) > 1){
                $code_chunk = array();
                $count = 0;
                foreach( $chunks as &$chunk ){
                    $count += 1;
                    if ($count % 2 == 0){
                        //store code chunk and insert a placeholder
                        $code_chunk[] = $chunk;
                        $chunk=' _|*KCODECHUNK*|_ ';
                    }
                }
                $file_list = implode('', $chunks);
            }

            //Split on whitespace and commas
            $files = preg_split('/[\s,+]/', $file_list, -1, PREG_SPLIT_NO_EMPTY);
            //sanitize file path
            foreach( $files as &$file ){
                    $file = str_replace(K_SITE_URL, '', $file);
                    $file = ltrim($file, '/');
            }
        if(!$files){return false;}
                
        if(defined('MINIFY_TIMESTAMP_FILENAME')){$output_link = minify_timestamp($output_link, $last_mod);}
        $css_tag = '<link rel="stylesheet" href="'.$output_link;
        if(defined('MINIFY_TIMESTAMP_QUERYSTRING')){$css_tag .= '?'.$last_mod;}
        $css_tag .= '"'.$tag_attributes.' />';
        $js_tag = '<script type="text/javascript" src="'.$output_link;
        if(defined('MINIFY_TIMESTAMP_QUERYSTRING')){$js_tag .= '?'.$last_mod;}
        $js_tag .= '"'.$tag_attributes.'></script>';
        
        //compare modification dates to output file
        if($output_file){
            foreach($files as $item){
                if(is_file(K_SITE_DIR . $item)){
                    if( filemtime(K_SITE_DIR . $item) > $last_mod ){
                        if(defined('MINIFY_TIMESTAMP_FILENAME') || defined('MINIFY_TIMESTAMP_QUERYSTRING')){
                            $FUNCS->invalidate_cache();
                        }
                        $modified = 1;
                        break;
                    }
                }
                if ($item === '_|*KCODECHUNK*|_' && ( getlastmod() > $last_mod )){
                    if(defined('MINIFY_TIMESTAMP_FILENAME') || defined('MINIFY_TIMESTAMP_QUERYSTRING')){
                        $FUNCS->invalidate_cache();
                    }
                    $modified = 1;
                    break;
                }
            }
            //No new modifications. Render 'link' or 'script' tag. Done.
            if (!$modified && $filetype == 'css'){
                return $css_tag;
                }
            if (!$modified && $filetype == 'js'){
                return $js_tag;
            }
        }
        
        //Combine files
        $count=0;
        foreach($files as $item){
            if($item == '_|*KCODECHUNK*|_'){$content .= $code_chunk[$count]; $count+=1;}
            elseif(is_file(K_SITE_DIR . $item)){$content .= file_get_contents(K_SITE_DIR . $item);}
        }
        //minify combined files
        if ($filetype == 'css'){ $output = minify_css($content); }
        if ($filetype == 'js'){ 
            require_once( K_COUCH_DIR.'addons/minify-js-css/JShrink.php' ); 
            $output = \JShrink\Minifier::minify($content); 
        }
        
        //No output file. Embed output on page. Done.
        if(!$output_file){ 
            if ($filetype == 'css'){ return '<style' . $tag_attributes . '>' . $output . '</style>'; }
            if ($filetype == 'js'){ return '<script type="text/javascript"' . $tag_attributes . '>' . $output . '</script>'; }
        }
        
        //Create new output file. Render tag. Done.
        file_put_contents($output_file, $output);
        if ($filetype == 'css'){
                return $css_tag;
        }
        if ($filetype == 'js'){
                return $js_tag;
        }
    }
}
$FUNCS->register_tag( 'minify', array('MinifyJsCss', 'minify_js_css') );
