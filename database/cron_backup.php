<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );
    require( 'config.php' );

// Backup site database
    $now = date('Y-m-d_H.i.s');
    $path = str_replace( '\\', '/', dirname(realpath(__FILE__) )).'/backups/';
    $path .= K_DB_NAME.'_'.$now.'.sql';
    $command = MYSQL_PATH.'mysqldump '.K_DB_NAME.' > '.$path;
    
    exec($command);

// Optionally, email a notification to the webmaster
// mail('webmaster@mysite.com', K_DB_NAME.' Database Backup', 'Database backup for '.K_DB_NAME.' saved on '.$now.'.');