<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

//    exec('/Applications/MAMP/Library/bin/mysqldump', $return, $test);
    exec('mysqldump', $return, $test);
    if( $test == '127' ) die('Check server configuration. PHP cannot access the "mysqldump" function. Cannot create backup file.');
            
    $now = date('Y-m-d_H.i.s');
    $command='mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.  '| gzip --best';
//    $command='/Applications/MAMP/Library/bin/mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.  '| gzip --best';

    //header 
    header( "Content-Type: application/octet-stream" );
    header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
    header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    header( "Pragma: no-cache" );
    header( "Content-Disposition: attachment; filename=".K_DB_NAME."_".$now.".sql" );
    //create download
    passthru( $command );
    exit();

//  For trouble with MAMP servers on OSX replace line 12 with:
//  exec('/Applications/MAMP/Library/bin/mysqldump', $return, $test);

//  and line 16 with:
//  $command='/Applications/MAMP/Library/bin/mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.  '| gzip --best';