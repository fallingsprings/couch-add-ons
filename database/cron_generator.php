<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );
    require( 'config.php' );

    /* output header */
    header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Content-Type: application/octet-stream" );
    header( "Content-Disposition: attachment; filename=backup_db.php" );

    $path .= K_DB_NAME . '_';
    $command = "\$command='" . K_MYSQL_PATH  . "mysqldump";
    $command .= ' --password=' . K_DB_PASSWORD;
    $command .= ' --user=' . K_DB_USER;
    $db_host = array_map( "trim", explode(':', K_DB_HOST) );
    $command .= ' --host=' . $db_host[0];
    if( strlen($db_host[1]) ){
        $command .= ' --port=' . $db_host[1];
    }
    $command .= ' ' . K_DB_NAME . " > " . $path . "'.\$now.'.sql';";
    
    echo '<';
    echo "?php\n";

    echo "//Place this file in your cgi-bin and call it from a Cron Job.\n";
    echo "//Check with your host for details on their requirements for Cron Job scripts.\n\n";

    echo "\$now = date('Y-m-d');\n";
    echo $command . "\n\n";
    echo "exec(\$command);\n\n";
    echo "//Optionally, email a notification to the webmaster.\n";
    echo "//mail('webmaster@mysite.com', '". K_DB_NAME . " Database Backup', 'Database backup for " . K_DB_NAME . " was saved on ' .\$now. '.');\n";

    echo '?';
    echo ">";
