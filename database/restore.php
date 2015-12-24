<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'/header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

    exec('mysql', $return, $test);
    if( $test == '127' ) die('Check server configuration. PHP cannot access the "mysql" function. Cannot restore the backup file.');

    header( 'Content-Type: text/html; charset='.K_CHARSET );
    $path = K_COUCH_DIR.'database/backups';//path to backup folder
    $files = array_diff(scandir($path, 1), array('..', '.', '.htaccess'));
    $command = 'mysql -h '.K_DB_HOST.' -u '.K_DB_USER.' -p'.K_DB_PASSWORD.' '.K_DB_NAME.' < '.$path .'/'. $_POST['backup'];

    if( !$files['0'] ){
        echo '<p>No backup files available. Use backup.php to create a database backup.</p>';
    }else{
        if( !isset($_POST['backup']) ){
            echo '<p>Your current database will be erased and replaced with the one you choose.<br/>';
            echo 'Be sure your backups are in good order before proceeding.<br/>';
            echo 'This is your only warning!</p>';
            
            echo '<form method="post" action="" onsubmit="if( !confirm(\'restore database?\') ){return false;}">';
            echo '<select name="backup">';
            foreach($files as $value){
                echo '<option value="'.$value.'">'.$value.'</option>';
            }
            echo '</select>';
            echo '&nbsp;<input type="submit" value="Restore Backup Now">';
            echo '</form>';
        }
    }

    if( isset($_POST['backup']) ){
        if( file_exists($path .'/'. $_POST['backup'])){
            exec($command);
            echo '<p>Database restored from '.$path.'/'. $_POST["backup"].'</p>';
        }else{
            echo '<p>Error: can\'t find the file '. $_POST["backup"].'</p>';
        }
    }