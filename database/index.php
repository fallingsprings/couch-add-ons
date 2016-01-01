<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );
echo $environment[0];
    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    exec(MYSQL_PATH.'mysql', $return, $test);
    if( $test == '126' ) echo 'Check permissions on the server. Cannot restore backup files.<br/>';
    if( $test == '127' ) echo 'Configure path name for mysql. Cannot restore backup files.<br/>';
    if( $test != '126' && $test != '127' && $test != '0' && $test != '1' ) echo 'Unknown error. Cannot restore backup files. Error Code: '.$test.'<br/>';
            
    exec( MYSQL_PATH.'mysqldump', $return, $test );
    if( $test == '126' ) echo 'Check permissions on the server. Cannot create backup files.<br/>';
    if( $test == '127' ) echo 'Configure path name for mysqldump. Cannot create backup files.<br/>';
    if( $test != '126' && $test != '127' && $test != '0' && $test != '1' ) echo 'Unknown error. Cannot create backup files. Error Code: '.$test.'<br/>';

php?>

    <center style="padding-top:10%;">
        <form method="post" action="backup.php">
            <input type="submit" name="submit" value="Create a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
        </form>
        
        <form method="post" action="restore.php">
            <input type="submit" name="submit" value="Restore a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
        </form>   

        <form method="post" action="download.php" onsubmit="if(!confirm('download file?')){return false;}else{success();}">
            <input type="submit" name="submit" value="Download a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
        </form>

        <form method="post" action="manage.php">
            <input type="submit" name="submit" value="Manage Files" style="font-size:1em; height:2em;"/>
        </form>
    </center>

<script type="text/javascript">
    function success(){
        var message = document.createElement('p');
        message.innerHTML = 'Download started. Check your downloads folder.';
        var button = document.getElementsByTagName('input');
        button[2].parentNode.replaceChild(message, button[2]);
    }       
</script>
