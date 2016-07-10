<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );
    require( 'config.php' );

// Backup site database
    $now = date('Y-m-d');
    $path .= escapeshellarg( K_DB_NAME ) . '_' . $now . '.sql';

    $command = escapeshellcmd( K_MYSQL_PATH . 'mysqldump' );
    if( $is_windows ){
        $command = '"' . $command . '"';
    }

    $command .= ' --user=' . escapeshellarg( K_DB_USER );
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
    
    exec($command);

// Optionally email a notification to the webmaster:
//    mail(webmaster@mysite.com', K_DB_NAME . ' Database Backup', 'Database backup for '.K_DB_NAME.' was saved on '.$now.'.');