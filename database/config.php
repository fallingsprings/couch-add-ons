<?php

    $mysql_path = mysql_query( "SHOW VARIABLES LIKE 'basedir'" );
    $mysql_path = mysql_fetch_array($mysql_path);
    $environment = explode('\\', $mysql_path[1]);
    if ( !$environment[1] ){
        define( 'MYSQL_PATH', $mysql_path[1].'/bin/' );
    }else{
        define( 'MYSQL_PATH', $mysql_path[1].'\bin\\' );
    }

// Explicitly define the full path to
// 'mysql' and 'mysqldump' here
// if automatic discovery fails.

// define( 'MYSQL_PATH', '' );