<?php

    ob_start();

    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

//    exec('/Applications/MAMP/Library/bin/mysqldump', $return, $test);
    exec('mysqldump', $return, $test);
    if( $test == '127' ) die('Check server configuration. PHP cannot access the "mysqldump" function. Cannot create backup file.');
            
    header( 'Content-Type: text/html; charset='.K_CHARSET );
    echo '<center style="padding-top:10%;">';
    if( !isset($_POST['submit']) ){
        echo '<form method="post" action="">';
        echo '<input type="submit" name="submit" value="Create a Backup of the Current Database?" style="font-size:1em; height:2em;">';
        echo '</form>';
    }else{
        $now = date('Y-m-d_H.i.s');
        $path = str_replace( '\\', '/', dirname(realpath(__FILE__) ));
        $path .= '/backups/'.K_DB_NAME.'_'.$now.'.sql';
        $command='mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
//        $command='/Applications/MAMP/Library/bin/mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
        exec($command);
        echo '<p>Database backup saved to '.$path.'</p>';
        echo '<form method="post" action="download.php" onsubmit="if(!confirm(\'download file?\')){return false;}else{message();}">';
        echo '<input type="submit" name="download" value="Download a Copy?" style="font-size:1em; height:2em;">';
        echo '</form>';
    }
    echo '</center>';

$script = <<< HERE
<script type="text/javascript">
    function message(){
        var message = document.createElement('p');
        message.innerHTML = 'Backup Downloaded. Check your downloads folder.';
        var button = document.getElementsByTagName('input');
        button[0].parentNode.replaceChild(message, button[0]);
    }       
</script>
HERE;
echo $script;

//  For trouble with MAMP servers on OSX replace line 11 with:
//  exec('/Applications/MAMP/Library/bin/mysqldump', $return, $test);

//  and line 24 with:
//  $command='/Applications/MAMP/Library/bin/mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
