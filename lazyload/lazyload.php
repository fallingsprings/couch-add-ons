<?php
if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly

class LazyLoad{            
    static function lazy_load( $params, $node ){        
        foreach( $node->children as $child ){
            $html .= $child->get_HTML();
        }
        //check params
        $default = ($params[0]['rhs'])?0:1;
        if(!$default){
            foreach($params as $resource_type){
                if($resource_type['rhs'] == 'image' || $resource_type['rhs'] == 'images'){
                    $lazy_load_images=1;
                }
                if($resource_type['rhs'] == 'iframe' || $resource_type['rhs'] == 'iframes'){
                    $lazy_load_iframes=1;
                }
                if($resource_type['rhs'] == 'av' || $resource_type['rhs'] == 'audio' || $resource_type['rhs'] == 'video'){
                    $lazy_load_av=1;
                }
            }
        } 
        //Instantiate a new DOM document to manipulate    
        $dom = new DOMDocument();
        if (!@$dom->loadHTML('<?xml encoding="UTF-8">' . $html)) { // trick to set charset
            return $html;
        }
        // Gather lazy-loadable resources
        $images = array($dom->getElementsByTagName('img'));
        $iframes = array($dom->getElementsByTagName('iframe'));
        $videos = $dom->getElementsByTagName('video');
        $audio = $dom->getElementsByTagName('audio');
        $av = array($videos, $audio);
        $blankImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

        //Lazy Load iframes
        if($default || $lazy_load_iframes){    
            foreach ( $iframes as $resource ) {
                for ($i = $resource->length - 1; $i >= 0; $i--) {
                    if ( strpos($resource->item($i)->getAttribute('class'), 'eager') === false ) {
                        $node = $resource->item($i);
                        $clone = $node->cloneNode();
                    
                        //create noscript tag
                        $noscript = $dom->createElement('noscript');
                        $noscript->appendChild($clone);
                        $node->parentNode->insertBefore($noscript, $node);
                
                        //set up lazy load
                        if ( $node->getAttribute('src') ) {
                            $node->setAttribute('data-src', $node->getAttribute('src'));
                            if (!is_file(K_SITE_DIR . '/iframe-dummy.html')){
                                file_put_contents(K_SITE_DIR . '/iframe-dummy.html', '');
                            }
                            $node->setAttribute('src', '/iframe-dummy.html');
                        }
                        $node->setAttribute('class', trim($node->getAttribute('class') . ' lazyload'));   
                    }
                }
            }
        }
        //Lazy Load images
        if($default || $lazy_load_images){    
            foreach ( $images as $resource ) {
                for ($i = $resource->length - 1; $i >= 0; $i--) {
                    if ( strpos($resource->item($i)->getAttribute('class'), 'eager') === false ) {
                        $node = $resource->item($i);
                        $clone = $node->cloneNode();
                    
                        //create noscript tag
                        $noscript = $dom->createElement('noscript');
                        $noscript->appendChild($clone);
                        $node->parentNode->insertBefore($noscript, $node);
                
                        //set up lazy load
                        if ( $node->getAttribute('src') ) {
                            $node->setAttribute('data-src', $node->getAttribute('src'));
                            $node->setAttribute('src', $blankImage);
                        }
                        $node->setAttribute('class', trim($node->getAttribute('class') . ' lazyload'));   
                    }
                }
            }
        }
        //Lazy Load video and audio
        if($default || $lazy_load_av){    
            foreach ( $av as $resource ) {
                for ($i = $resource->length - 1; $i >= 0; $i--) {
                    if ( strpos($resource->item($i)->getAttribute('class'), 'eager') === false ) {
                        $node = $resource->item($i);
                        $clone = $node->cloneNode();
                    
                        //create noscript tag
                        $noscript = $dom->createElement('noscript');
                        $noscript->appendChild($clone);
                        $node->parentNode->insertBefore($noscript, $node);
                
                        //set up lazy load
                        if ( $node->getAttribute('poster') ) {
                            $node->setAttribute('data-poster', $node->getAttribute('poster'));
                            $node->setAttribute('poster', $blankImage);
                        }
                        $node->setAttribute('preload', 'none');
                        $node->setAttribute('class', trim($node->getAttribute('class') . ' lazyload'));   
                    }
                }
            }
        }
        $newHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|body))[^>]*>\s*~i', '', $dom->saveHTML()); //regex to remove doctype, body, and html tags
                
        if (!$newHtml) {
            return $html;
        }else{
            return $newHtml;
        }
    }
}
$FUNCS->register_tag( 'lazy_load', array('LazyLoad', 'lazy_load') );
