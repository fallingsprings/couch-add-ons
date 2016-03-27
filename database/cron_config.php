<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    $command = 'php -q ';
    $command .= $path . 'cron_backup.php';

    echo '<a href="./"><< BACK</a>';

    echo '<p>Use the following command to set up a Cron Job.</p>';

    echo '<pre>' . $command . '</pre>';

    echo '<p>If the Cron Job fails to work, check with your host about their restrictions on Cron Jobs. You may need to tweak the command or use the <a href="cron_generator.php">Cron Generator</a> to create a file to place in your cgi-bin.</p>';