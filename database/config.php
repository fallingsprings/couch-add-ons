<?php
    //Path to Backup Folder
    $path = str_replace( '\\', '/', dirname(realpath(__FILE__) )).'/backups/';    

    // Explicitly define the full path to mysql files if automatic discovery fails (make sure add the trailing slash)
    //define( 'K_MYSQL_PATH', '' );
    //define( 'K_MYSQL_PATH', 'E:/one one/' );

    // try to figure out path of MySQL executables
    if ( !defined('K_MYSQL_PATH') ){
        $rs = $DB->raw_select( "SHOW VARIABLES LIKE 'basedir'" );
        if( count($rs) ){
            $mysql_path = rtrim( str_replace('\\', '/', $rs[0]['Value']), '/' ) . '/bin/';
            define( 'K_MYSQL_PATH', $mysql_path );
        }
    }
