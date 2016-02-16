<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    // test if system command available
    exec( K_MYSQL_PATH.'mysql', $output, $result );
    if( $result!='0' && $result!='1' ){
        if( $result == '126' ){ echo 'Check permissions on the server. Cannot restore backup files.<br/>'; }
        elseif( $result == '127' ){ echo 'Configure path for mysqldump. Cannot restore backup files.<br/>';}
        else{ echo 'Unknown error. Cannot restore backup files. Error Code mysqldump: ' . $result . '<br/>'; }
    }
            
    header( 'Content-Type: text/html; charset=' . K_CHARSET );
    if( !is_dir( $path ) ){
        die( 'Error: Could not find backup folder at ' . $path );
    }

    $files = scandir($path, 1);

    echo '<a href="./"><< BACK</a>';
    echo '<center style="padding-top:10%;">';
    foreach($files as $value){
        $count = (preg_match('~.sql$~', $value)) ? 1 : $count;
        if ( $count ) break;
    }
    if( $count != 1 ){
        echo '<p>No backup files available. Use the backup utility to create database backups.</p>';
    }else{
        
        if( !isset($_POST['backup']) ){
            echo '<p>Your current database will be erased and replaced with the one you choose.<br/>';
            echo 'Proceed with caution.<br/>';
            
            echo '<form method="post" action="" onsubmit="if( !confirm(\'Restore database?\') ){return false;}">';
            echo '<select name="backup" style="font-size:1em; height:2em; padding-right:.5em;">';
            foreach($files as $value){
                if (preg_match('~.sql$~', $value)){
                    echo '<option value="' . $value . '">' . $value . '</option>';
                }
            }
            echo '</select>';
            echo '&nbsp;<input type="submit" value="Restore Backup Now" style="font-size:1em; height:2em;">';
            echo '</form>';
        }
    }

    if( isset($_POST['backup']) ){
        if( file_exists($path .'/'. $_POST['backup'])){
            $is_windows = ( substr(PHP_OS, 0, 3)==='WIN' );
            $filename = $_POST['backup'] . '.sql';

            $path .= K_DB_NAME.'_' . $_POST['backup'] . '.sql';
            $command = escapeshellcmd( K_MYSQL_PATH . 'mysql' );
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
            $command .= ' < ' . $path . $_POST['backup'];
            if( $is_windows ){
                $command = '"' . $command . '"'; // workaround for problem with executing commands with more than 2 double quotes in Windows
            }
                   
           exec($command, $error);
            echo '<p>Database restored from '.$path . $_POST["backup"] . '</p>';
        }else{
            echo '<p>Error: can\'t find the file \'' . $_POST["backup"] . '\'</p>';
        }
    }
    echo '</center>';