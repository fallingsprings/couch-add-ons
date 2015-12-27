<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
php?>

    <center style="padding-top:10%;">
        <form method="post" action="backup.php">
            <input type="submit" name="submit" value="Create a Backup" style="font-size:1em; height:2em;margin-bottom:2em;"/>
        </form>
        
        <form method="post" action="restore.php">
            <input type="submit" name="submit" value="Restore a Backup" style="font-size:1em; height:2em;margin-bottom:2em;"/>
        </form>   

        <form method="post" action="download.php" onsubmit="if(!confirm('download file?')){return false;}else{success();}">
            <input type="submit" name="download" value="Download a Backup" style="font-size:1em; height:2em;"/>
        </form>
    </center>

<script type="text/javascript">
    function success(){
        var message = document.createElement('p');
        message.innerHTML = 'Backup Downloaded. Check your downloads folder.';
        var button = document.getElementsByTagName('input');
        button[2].parentNode.replaceChild(message, button[2]);
    }       
</script>
