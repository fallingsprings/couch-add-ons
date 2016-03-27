<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    // test if system commands available
    exec( K_MYSQL_PATH . 'mysql', $output, $result );
    if( $result!='0' && $result!='1' ){
        if( $result == '126' ){ echo 'Check permissions on the server. Cannot restore backup files.<br/>'; }
        elseif( $result == '127' ){ echo 'Configure path for mysqldump. Cannot restore backup files.<br/>';}
        else{ echo 'Unknown error. Cannot restore backup files. Error Code mysql: '.$result.'<br/>'; }
    }
    exec( K_MYSQL_PATH . 'mysqldump', $output, $result );
    if( $result!='0' && $result!='1' ){
        if( $result == '126' ){ echo 'Check permissions on the server. Cannot create backup files.<br/>'; }
        elseif( $result == '127' ){ echo 'Configure path for mysqldump. Cannot create backup files.<br/>';}
        else{ echo 'Unknown error. Cannot create backup files. Error Code mysqldump: ' . $result . '<br/>'; }
    }

    //DOWNLOAD
    if( isset($_POST['download']) ){
        $FUNCS->validate_nonce( K_DB_NAME . $_POST['download'], $_POST['nonce'] );
        
        $is_windows = ( substr(PHP_OS, 0, 3)==='WIN' );
        $filename = K_DB_NAME . '_' . $_POST['download'] . '.sql';

        // prepare command
        $command = escapeshellcmd( K_MYSQL_PATH . 'mysqldump' );
        if( $is_windows ){
            $command = '"' . $command . '"';
        }

            $command .= ' --user=' . escapeshellarg( K_DB_USER ) . ' --password=' . escapeshellarg( K_DB_PASSWORD );
            $db_host = array_map( "trim", explode(':', K_DB_HOST) );
            $command .= ' --host=' . escapeshellarg( $db_host[0] );
            if( strlen($db_host[1]) ){
                $command .= ' --port=' . escapeshellarg( $db_host[1] );
            }
            $command .= ' ' . escapeshellarg( K_DB_NAME );
            if( !$is_windows ){
                $command .= ' |gzip --best';
                $filename .= '.gz';
            }
            else{
                $command = '"' . $command . '"'; // workaround for problem with executing commands with more than 2 double quotes in Windows
            }

            //header
            header( "Content-Type: application/octet-stream" );
            header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
            header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
            header( "Pragma: no-cache" );
            header( "Content-Disposition: attachment; filename=" . $filename );

            passthru( $command, $error );
            exit();
    }
    if ( !isset($_POST['download']) ){
        $reference = mt_rand();
        $nonce = $FUNCS->create_nonce( $reference );
   }

    //BACKUP
    if( isset($_POST['backup']) ){
        $FUNCS->validate_nonce( K_DB_NAME . $_POST['backup'], $_POST['nonce'] );
           
        //create backup folder if necessary
        if( !is_dir( $path ) ){
            mkdir( $path );
        }
        //create htaccess file if necessary
        if( !file_exists($path . '/.htaccess') ){
            $handle = fopen($path . '/.htaccess', 'x+');
            fwrite($handle, 'deny from all');
            fclose($handle);
        }
        
        $is_windows = ( substr(PHP_OS, 0, 3)==='WIN' );
        $filename = K_DB_NAME . '_' . $_POST['backup'] . '.sql';

        // prepare command
        $path .= K_DB_NAME . '_' . $_POST['backup'] . '.sql';
        $command = escapeshellcmd( K_MYSQL_PATH . 'mysqldump' );
        if( $is_windows ){
            $command = '"' . $command . '"';
        }

        $command .= ' --user=' . escapeshellarg( K_DB_USER ) . ' --password=' . escapeshellarg( K_DB_PASSWORD );
        $db_host = array_map( "trim", explode(':', K_DB_HOST) );
        $command .= ' --host=' . escapeshellarg( $db_host[0] );
        if( strlen($db_host[1]) ){
            $command .= ' --port=' . escapeshellarg( $db_host[1] );
        }
        $command .= ' ' . escapeshellarg( K_DB_NAME );
        $command .= ' > ' . $path;
        if( $is_windows ){
            $command = '"' . $command . '"'; // workaround for problem with executing commands with more than 2 double quotes in Windows
        }
        
        exec($command, $error);
        $message = '<p>Database backup saved to '.$path.'</p>';
    }

    //HTML OUTPUT
    $now = date('Y-m-d_His');
    $nonce = $FUNCS->create_nonce( K_DB_NAME . $now );

$actions = <<< HERE
    <center style="padding-top:10%;">
        $message
        <form method="post" action="">
            <input type="submit" name="submit" value="Save a Backup" style="font-size:1em; height:2em; margin-bottom:2em;"/>
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

        <form method="post" action="cron_config.php">
            <input type="submit" name="submit" value="Configure a Cron Job" style="font-size:1em; height:2em; margin-bottom:2em;"/>
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