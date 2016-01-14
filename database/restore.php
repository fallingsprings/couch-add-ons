<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    exec(MYSQL_PATH.'mysql', $return, $test);
    if( $test == '127' ) die('Check your path name configuration. PHP cannot access the "mysql" function. Cannot restore backup file.');
    if( $test == '126' ) die('Check permissions on the server. PHP cannot execute the "mysql" function. Cannot restore backup file.');
    if( $test != '0' && $test != '1' ) die('Unknown error. Cannot restore backup file. Error Code: '.$test);
            
    header( 'Content-Type: text/html; charset='.K_CHARSET );
    if( !is_dir( $path ) ){
        die( 'Could not find backup folder at '.$path );
    }

    $files = scandir($path, 1);

    echo '<a href="index.php"><< BACK</a>';
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
            echo 'Be sure you have a good backup before proceeding.<br/>';
            
            echo '<form method="post" action="" onsubmit="if( !confirm(\'Restore database?\') ){return false;}">';
            echo '<select name="backup" style="font-size:1em; height:2em; padding-right:.5em;">';
            foreach($files as $value){
                if (preg_match('~.sql$~', $value)){
                    echo '<option value="'.$value.'">'.$value.'</option>';
                }
            }
            echo '</select>';
            echo '&nbsp;<input type="submit" value="Restore Backup Now" style="font-size:1em; height:2em;">';
            echo '</form>';
        }
    }

    if( isset($_POST['backup']) ){
        if( file_exists($path .'/'. $_POST['backup'])){
            $command = MYSQL_PATH.'mysql --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' < '.$path . $_POST['backup'];
           exec($command);
            echo '<p>Database restored from '.$path . $_POST["backup"].'</p>';
        }else{
            echo '<p>Error: can\'t find the file \''. $_POST["backup"].'\'</p>';
        }
    }
    echo '</center>';