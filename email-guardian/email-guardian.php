<?php
if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
class EmailGuardian {   
  // Email Guardian
  // https://www.couchcms.com/forum/viewtopic.php?f=8&t=11001

  function init_guardian( $html, $secret_decoder_ring ){
    //inject decipher function
    $inject_decipher_function = "\n<script>" . file_get_contents(__DIR__.'/decipher.min.js');
    //inject array for emails and keys
    $inject_decipher_function .= 'var guardHouse=[],';
    $inject_decipher_function .= "decipheredLink = '',";
    $inject_decipher_function .= "characterSet = '" . $secret_decoder_ring . "';";
    $inject_decipher_function .= '</script>';
    $html = $inject_decipher_function . "\n" . $html;
    return $html;
  }
  
  function replace_with_span( $html, $link, $id ){
    $pos = strpos($html, $link);
    $span = "<span id='" . $id . "'></span>";
    if ($pos !== false) {
      $html = substr_replace($html, $span, $pos, strlen($link));
    }
    return $html;    
  }
  
  function encipher_email( $key, $str, $secret_decoder_ring ){
    $newStr='';
    $j=0;

    for ($i=0; $i < strlen($str); $i++){
      if(strpos($secret_decoder_ring, $str[$i]) === false ){
        //return the character if it's not on the decoder ring
        $newStr .= $str[$i];
        }else{
          //offset by randomly generated key, wrapping around at the end
          $j = (strpos($secret_decoder_ring, $str[$i]) + $key > strlen($secret_decoder_ring) - 1) ? strpos($secret_decoder_ring, $str[$i]) + $key - strlen($secret_decoder_ring) : strpos($secret_decoder_ring, $str[$i]) + $key;
          $newStr .= $secret_decoder_ring[$j];
      }
    }
    $tmp='';
    for($i = mb_strlen($newStr); $i >= 0; $i--){
      $tmp .= mb_substr($newStr, $i, 1);
    }
    //package it for the trip to the front end
    $newStr = addslashes($tmp);    
return $newStr;
  }
  
  static function email_guardian( $params, $node ){
    global $FUNCS;
    extract( $FUNCS->get_named_vars(
      array(
            'no_script_message'=>'Please enable JavaScript to see this email address.', 
            'create_links' =>'1'
            ),
      $params)
    );
    foreach( $node->children as $child ){
      $html .= $child->get_HTML();
    }
    $secret_decoder_ring = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ/<>!:.@#$%*abcdefghijklmnopqrstuvwxyz "; 
    $guardhouse = '';
      
    //discover mailto links
    preg_match_all('/<a\s+(href\s?=.*?)(mailto:.*?)a>/', $html, $mailto_links);
    
    //encipher mailto links
    if ( $mailto_links[0] ){ 
      //so multiple instances don't repeat this step
      if ( !defined('DECIPHER_FUNCTION_INJECTED')) {
        define( 'DECIPHER_FUNCTION_INJECTED', '1' );
        //injects the deciphering function on the front end
        $html = EmailGuardian::init_guardian( $html, $secret_decoder_ring );
      }
      
      // replace and encipher link
      foreach($mailto_links[0] as $link){
        //generate an id
        $id  = 'v' . $FUNCS->generate_key( 15 );
        
        //replace link with an empty span
        $html = EmailGuardian::replace_with_span($html, $link, $id);        
        
        //generate random key
        $key = rand(1, strlen($secret_decoder_ring) -1 );
        
        //encipher link
        $link = EmailGuardian::encipher_email($key, $link, $secret_decoder_ring);
        //build the JS array for the front end
        $guardhouse .= "guardHouse.push([document.getElementById('" . $id . "'), " . $key . ", '" . $link . "']);";
      }
    }
    
    //Do free floating emails separately after mailto links are processed. 
    //Otherwise they get tangled together.
    //Discover free floating email addresses
    preg_match_all('/[\w._%+-]+@[\w.-]+\w{2,4}/u', $html, $free_floating_emails);
    if($free_floating_emails[0]){
      $floaters = array();
      //if 'create_links' is on, build a mailto link
      foreach($free_floating_emails[0] as $email){
        if($create_links){
          $generated_link = '<a href="mailto:' . $email . '">' . $email . '</a>';
          $floaters[] = $generated_link;
        }else{
          $floaters[] = $email;
        }
      }
    }
    //encipher free floating addresses
    if ( $free_floating_emails[0] ){ 
      //so multiple instances don't repeat this step
      if ( !defined('DECIPHER_FUNCTION_INJECTED')) {
        define( 'DECIPHER_FUNCTION_INJECTED', '1' );
        //injects the deciphering function on the front end
        $html = EmailGuardian::init_guardian( $html, $secret_decoder_ring );
      }

      // replace and encipher free floating email
      foreach($floaters as $link){
        //generate an id
        $id  = 'v' . $FUNCS->generate_key( 15 );
        //replace original email address with an empty span
        //matches even if email was converted to mailto tag
        $html = EmailGuardian::replace_with_span($html, $free_floating_emails[0][array_search($link, $floaters)] , $id);        

        //generate random key
        $key = rand(1, strlen($secret_decoder_ring) -1 );

        //encipher link
        $link = EmailGuardian::encipher_email($key, $link, $secret_decoder_ring);

        //build the JS array for the front end
        $guardhouse .= "guardHouse.push([document.getElementById('" . $id . "'), " . $key . ", '" . $link . "']);";
      }
    }
    
    //script that deciphers obfuscated emails on the front end
    $decipher = "\n<script>";
    $decipher .= $guardhouse;
    $decipher .= "for(let cipher of guardHouse){";
    $decipher .= "decipheredLink = decipherEmail(cipher[1], cipher[2], characterSet);";
    $decipher .= "cipher[0].innerHTML = decipheredLink;";  
    $decipher .= "}";
    //empty guardHouse in case of multiple tags on the page
    $decipher .= "guardHouse = [];";  
    $decipher .= "</script>\n";
    $html .= $decipher;
    return $html;
  }
}

$FUNCS->register_tag( 'email_guardian', array('EmailGuardian', 'email_guardian') );
