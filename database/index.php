<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

php?>
<html><body>
<center style="padding-top:10%;"><p><a style="font-size:1em; height:2em; color:blue;" href="backup.php">Create a Backup</a></p>
<p><a style="font-size:1em; height:2em; color:blue;" href="restore.php">Restore a Backup</a></p>
</center>
</body></html>