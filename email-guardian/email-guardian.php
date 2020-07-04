<?php
if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
class EmailGuardian {   
  // Email Guardian
  // https://www.couchcms.com/forum/viewtopic.php?f=8&t=11001

  static function replace_with_span( $html, $email, $id, $no_script_message ){
    $span = "<span id='" . $id . "'>" . $no_script_message . "</span>";
    return preg_replace('/'.preg_quote($email, '/') .'/', $span, $html, 1);
  }
  
  static function obfuscate_email( $email, $key, $secret_decoder_ring ){
    $obfuscatedEmail='';
    $j=0;

    for ($i=0; $i < strlen($email); $i++){
      if(strpos($secret_decoder_ring, $email[$i]) === false ){
        //return the character if it's not on the decoder ring
        $obfuscatedEmail .= $email[$i];
        }else{
          //offset by randomly generated key, wrapping around at the end
          $j = (strpos($secret_decoder_ring, $email[$i]) + $key > strlen($secret_decoder_ring) - 1) ? strpos($secret_decoder_ring, $email[$i]) + $key - strlen($secret_decoder_ring) : strpos($secret_decoder_ring, $email[$i]) + $key;
          $obfuscatedEmail .= $secret_decoder_ring[$j];
      }
    }
    //reverse the order
    $tmp='';
    for($i = mb_strlen($obfuscatedEmail); $i >= 0; $i--){
      $tmp .= mb_substr($obfuscatedEmail, $i, 1);
    }
    //package it for the trip to the front end
    $obfuscatedEmail = addslashes($tmp);    
return $obfuscatedEmail;
  }
  
  static function init_functions( ){
    //inject decipher function
    return "\n<script>" . file_get_contents(__DIR__.'/decipher.min.js');
  }
  
  static function init_variables( $guardhouse , $secret_decoder_ring ){
    $script .= "var decipheredLink = '',";
    $script .= "characterSet = '" . $secret_decoder_ring . "';";
    $script .= "var guardHouse = [";
    foreach($guardhouse as $item){
      $script .= "[document.getElementById('" . $item['id'] . "'), ".$item['key'].", '".$item['cipher']."'],";
    }
    $script .= "];";
    return $script;
  }
  
  static function refresh_guardhouse( $guardhouse ){
    //empty guardHouse of already injected emails and push new
    $script = "\n<script>";
    $script .= "guardHouse = [];";
    foreach($guardhouse as $item){
      $script .= "guardHouse.push([document.getElementById('" . $item['id'] . "'), " . $item['key'] . ", '" . $item['cipher'] . "']);";
    }
    return $script;
  }
  
  static function email_guardian_handler( $params, $node ){
    global $FUNCS;
    extract( $FUNCS->get_named_vars(
      array(
            'no_script_message'=>'(Please enable JavaScript to view this email address)', 
            'create_links' =>'1'
            ),
      $params)
    );
    foreach( $node->children as $child ){
      $html .= $child->get_HTML();
    }
    $secret_decoder_ring = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ/<>!:.@#$%*abcdefghijklmnopqrstuvwxyz "; 
    $guardhouse = [];
      
    //discover mailto links
    preg_match_all('/<a\s+(href\s?=.*?)(mailto:.*?)a>/', $html, $mailto_links);
    
    if ( $mailto_links[0] ){ 
      // replace and encipher each link
      foreach($mailto_links[0] as $email){
        //generate an id
        $id  = 'v' . $FUNCS->generate_key( 15 );
        
        //replace link with an empty span
        $html = EmailGuardian::replace_with_span($html, $email, $id, $no_script_message);        
        
        //generate random key
        $key = rand(1, strlen($secret_decoder_ring) -1 );
        
        //encipher link
        $cipher = EmailGuardian::obfuscate_email($email, $key, $secret_decoder_ring);
        
        //store everything in the guardhouse
        $guardhouse[] = ["id"=>$id, "key"=>$key, "cipher"=>$cipher];
      }
    }
       
    //Discover free floating email addresses
    preg_match_all('/([\S]|[\"].* .*[\"])+@[\w.&;%-]+\w{2,4}/u', strip_tags($html), $free_floating_emails);
    if($free_floating_emails[0]){
      $floaters = [];
      //if 'create_links' is on, build a mailto link
      foreach($free_floating_emails[0] as $email){
        if($create_links){
          $generated_link = '<a href="mailto:' . $email . '">' . $email . '</a>';
          $floaters[] = $generated_link;
        }else{
          $floaters[] = $email;
        }
      }
      // replace and encipher free floating email addresses
      foreach($floaters as $email){
        //generate an id
        $id  = 'v' . $FUNCS->generate_key( 15 );
        //replace original email address with an empty span
        //matching the original string in case email was converted to mailto tag
        $html = EmailGuardian::replace_with_span($html, $free_floating_emails[0][array_search($email, $floaters)] , $id, $no_script_message);        
        //generate random key
        $key = rand(1, strlen($secret_decoder_ring) -1 );

        //encipher link
        $cipher = EmailGuardian::obfuscate_email($email, $key, $secret_decoder_ring);
        //store everything in the guardhouse
        $guardhouse[] = ["id"=>$id, "key"=>$key, "cipher"=>$cipher];
      }
    }
    
    //create and inject script
    if ( $guardhouse ){
      if ( !defined('DECIPHER_FUNCTION_INJECTED')) {
        define( 'DECIPHER_FUNCTION_INJECTED', '1' );
        //inject the deciphering functions and variables on the front end
        $script = EmailGuardian::init_functions();
        $script .= EmailGuardian::init_variables( $guardhouse, $secret_decoder_ring );
      }else{
        //functions and declarations already injected in previous script
        //so empty array and push new items
        $script .= EmailGuardian::refresh_guardhouse( $guardhouse );
      }
      $script .= "injectEmail(guardHouse, characterSet);";
      $script .= "</script>\n";
      $html .= $script;
      return $html;
    }
  }
}

$FUNCS->register_tag( 'email_guardian', array('EmailGuardian', 'email_guardian_handler') );
