<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

    exec('mysqldump', $return, $test);
    if( $test == '127' ) die('Check server configuration. PHP cannot access the "mysqldump" function. Cannot create backup file.');
            
    if( !isset($_POST['submit']) ){
        echo '<form method="post" action="">';
        echo '<input type="submit" name="submit" value="Create a Backup of the Current Database?">';
        echo '</form>';
    }else{
        $now = date('Y-m-d.H.i.s');
        $path = K_COUCH_DIR.'database/backups/'.K_DB_NAME.'_'.$now.'.sql';
        $command='mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
        exec($command);
        echo '<p>Database backup saved to '.$path.'</p>';
        echo '<form method="post" action="download.php" onsubmit="if( !confirm(\'download file?\') ){return false;}">';
        echo '<input type="submit" name="download" value="Download a Copy?">';
        echo '</form>';
    }    


//  /Applications/MAMP/Library/bin/