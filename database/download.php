<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    exec(MYSQL_PATH.'mysqldump', $return, $test);
    if( $test == '126' ) die('Check permissions on the server. PHP cannot execute the "mysqldump" function. Cannot create backup file.');
    if( $test == '127' ) die('Check your path name configuration. PHP cannot access the "mysqldump" function. Cannot create backup file.');
    if( $test != '0' && $test != '1' ) die('Unknown error. Cannot create backup file. Error Code: '.$test);
            
    $now = date('Y-m-d_H.i.s');
    $command= MYSQL_PATH.'mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' |gzip --best';
        exec($command);

    //header 
    header( "Content-Type: application/octet-stream" );
    header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Content-Disposition: attachment; filename=".K_DB_NAME."_".$now.".sql" );
    //create download
    passthru( $command );
    exit();