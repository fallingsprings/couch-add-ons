<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );
echo $environment[0];
    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    //System and Path Errors
    exec(MYSQL_PATH.'mysql', $return, $test);
    if( $test == '126' ) echo 'Check permissions on the server. Cannot restore backup files.<br/>';
    if( $test == '127' ) echo 'Configure path name for mysql. Cannot restore backup files.<br/>';
    if( $test != '126' && $test != '127' && $test != '0' && $test != '1' ) echo 'Unknown error. Cannot restore backup files. Error Code: '.$test.'<br/>';
            
    exec( MYSQL_PATH.'mysqldump', $return, $test );
    if( $test == '126' ) echo 'Check permissions on the server. Cannot create backup files.<br/>';
    if( $test == '127' ) echo 'Configure path name for mysqldump. Cannot create backup files.<br/>';
    if( $test != '126' && $test != '127' && $test != '0' && $test != '1' ) echo 'Unknown error. Cannot create backup files. Error Code: '.$test.'<br/>';

    //DOWNLOAD
    if( isset($_POST['download']) ){
        $FUNCS->validate_nonce( K_DB_NAME . $_POST['download'], $_POST['nonce'] );
        $command= MYSQL_PATH.'mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' |gzip --best';
            exec($command);

        //header 
        header( "Content-Type: application/octet-stream" );
        header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Pragma: no-cache" );
        header( "Content-Disposition: attachment; filename=".K_DB_NAME."_".$_POST['download'].".sql" );
        //create download
        passthru( $command );
        exit();
    }else{
        $reference = mt_rand();
        $nonce = $FUNCS->create_nonce( $reference );
    }

    //BACKUP
    if( isset($_POST['backup']) ){
        $FUNCS->validate_nonce( K_DB_NAME . $_POST['backup'], $_POST['nonce'] );
           
        if( !is_dir( $path ) ){
            //create folder
            mkdir( $path );
        }
        if( !file_exists($path.'/.htaccess') ){
            //create .htaccess file
            $handle = fopen($path.'/.htaccess', 'x+');
            fwrite($handle, 'deny from all');
            fclose($handle);
        }
        
        $path .= K_DB_NAME.'_'.$_POST['backup'].'.sql';
        $command= MYSQL_PATH. 'mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
        exec($command);
        $message = '<p>Database backup saved to '.$path.'</p>';
    }

    //HTML OUTPUT
    $now = date('Y-m-d_His');
    $nonce = $FUNCS->create_nonce( K_DB_NAME . $now );

$actions = <<< HERE
    <center style="padding-top:10%;">
        $message
        <form method="post" action="">
            <input type="submit" name="submit" value="Create a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
            <input type="hidden" name="backup" value="$now"/>   
            <input type="hidden" name="nonce" value="$nonce"/>   
        </form>
        
        <form method="post" action="" onsubmit="success();">
            <input type="submit" name="submit" value="Download a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
            <input type="hidden" name="download" value="$now"/>   
            <input type="hidden" name="nonce" value="$nonce"/>   
        </form>
        
        <form method="post" action="restore.php">
            <input type="submit" name="submit" value="Restore a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
        </form>   

        <form method="post" action="file_manager.php">
            <input type="submit" name="submit" value="Manage Files" style="font-size:1em; height:2em; margin-bottom:2em;"/>
        </form>
    </center>

<script type="text/javascript">
    function success(){
        var message = document.createElement('p');
        message.innerHTML = 'Download started. Check your downloads folder.';
        var page = document.getElementsByTagName('center');
        page[0].insertBefore(message, page[0].firstChild);
    }       
</script>
HERE;
echo $actions;