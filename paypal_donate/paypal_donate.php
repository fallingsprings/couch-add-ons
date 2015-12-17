<?php
    
    if ( !defined('K_COUCH_DIR') ) die(); // cannot be loaded directly
    
    class PayPalDonate{
        
        function paypal_donate( $params, $node ){
            global $CTX, $FUNCS, $PAGE;
            if( count($node->children) ) {die("ERROR: Tag \"".$node->name."\" is a self closing tag");}

            extract( $FUNCS->get_named_vars(
                        array(
                              'image'=>'',
                              'processor'=>'',
                              'return_url'=>'',
                              'cancel_url'=>'',
                              'custom'=>'',
                              'purpose'=>'',
                              'reference'=>'',
                              'amount'=>''
                             ),
                        $params)
                   );
            $image = trim( $image );
            $processor = trim( $processor );
            $show_shipping = ( $show_shipping==1 ) ? 1 : 0;
            $return_url = trim( $return_url );
            $cancel_url = trim( $cancel_url );

            $item_name = trim( $purpose );
            $item_number = trim( $reference );
            $amount = trim( $amount );

            $return_url = ( $return_url ) ? $return_url : K_SITE_URL . $PAGE->link;
            $cancel_url = ( $cancel_url ) ? $cancel_url : $return_url;
            $processor = ( $processor ) ? $processor : $return_url;
            $sep = ( strpos($processor, '?')===false ) ? '?' : '&';
            $notify_url = $processor . $sep . 'paypal_ipn=1';

                if( K_PAYPAL_USE_SANDBOX ){
                    $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                }
                else{
                    $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
                }

                if( !$image ) $image=0;
            if( $FUNCS->is_natural($image) ){
                $arr_btns = array( 'btn_donate_SM.gif', 'btn_donate_LG.gif', 'btn_donateCC_LG.gif',
                                  'x-click-but04.gif', 'x-click-butcc-donate.gif',
                                  'x-click-but11.gif', 'x-click-but21.gif' );
                $image_src = 'https://www.paypal.com/en_US/i/btn/'.$arr_btns[$image];
            }
            else{
                $css = explode(':', $image);
                if ( !$css[1] ){
                    $image_src = $image;
                }
            }

            $html .= '<form action="'.$paypal_url.'" method="post">';
            $html .= '<input type="hidden" name="cmd" value="_donations"/>';
            $html .= '<input type="hidden" name="business" value="'.K_PAYPAL_EMAIL.'"/>';
            $html .= '<input type="hidden" name="item_name" value="'.$item_name.'"/>';
            $html .= '<input type="hidden" name="item_number" value="'.$item_number.'"/>';
            $html .= '<input type="hidden" name="amount" value="'.$amount.'"/>';
            $html .= '<input type="hidden" name="no_note" value="1"/>';
            $html .= '<input type="hidden" name="currency_code" value="'.K_PAYPAL_CURRENCY.'"/>';
            $html .= '<input type="hidden" name="rm" value="2"/>';
            $html .= '<input type="hidden" name="custom" value="'.$custom.'">';
            $html .= '<input type="hidden" name="return" value="'.$return_url.'"/>';
            $html .= '<input type="hidden" name="cancel_return" value="'.$cancel_url.'"/>';
            $html .= '<input type="hidden" value="'.$notify_url.'" name="notify_url"/>';
            if( $css[1] ){
                $html .= '<input type="submit" class="pp_submit_btn" value="'.trim( $css[1] ).'"/>';
            }
            else{
                $html .= '<input type="image" border="0" alt="Make payments with PayPal - it\'s fast, free and secure!"';
                $html .= ' name="submit" src="'.$image_src.'"/>';
            }
            $html .= '<img width="1" height="1" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" alt=""/>';
            $html .= '</form>';


            return $html;
        }
    }
    
    $FUNCS->register_tag( 'paypal_donate', array('PayPalDonate', 'paypal_donate') );