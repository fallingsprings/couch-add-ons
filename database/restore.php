<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

//    exec('/Applications/MAMP/Library/bin/mysql', $return, $test);
    exec('mysql', $return, $test);
    if( $test == '127' ) die('Check server configuration. PHP cannot access the "mysql" function. Cannot restore the backup file.');

    header( 'Content-Type: text/html; charset='.K_CHARSET );
    $path = str_replace( '\\', '/', dirname(realpath(__FILE__)) ).'/backups/';
    $files = scandir($path, 1);
    $command = 'mysql  --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' < '.$path . $_POST['backup'];
//    $command = '/Applications/MAMP/Library/bin/mysql --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' < '.$path . $_POST['backup'];

    echo '<center style="padding-top:10%;">';
    foreach($files as $value){
        $count = (preg_match('~.sql$~', $value)) ? 1 : $count;
        if ( $count ) break;
    }
    if( $count != 1 ){
        echo '<p>No backup files available. Use <a href="backup.php" style="color:blue;">backup.php</a> to create a database backup.</p>';
    }else{
        
        if( !isset($_POST['backup']) ){
            echo '<p>Your current database will be erased and replaced with the one you choose.<br/>';
            echo 'Be sure you have a good backup before proceeding.<br/>';
            
            echo '<form method="post" action="" onsubmit="if( !confirm(\'restore database?\') ){return false;}">';
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
            exec($command);
            echo '<p>Database restored from '.$path . $_POST["backup"].'</p>';
        }else{
            echo '<p>Error: can\'t find the file \''. $_POST["backup"].'\'</p>';
        }
    }
    echo '</center>';

//  For trouble with MAMP servers on OSX replace line 11 with:
//  exec('/Applications/MAMP/Library/bin/mysql', $return, $test);

//  and line 17 with:
//  $command = '/Applications/MAMP/Library/bin/mysql -h '.K_DB_HOST.' -u '.K_DB_USER.' -p'.K_DB_PASSWORD.' '.K_DB_NAME.' < '.$path . $_POST['backup'];