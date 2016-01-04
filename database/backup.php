<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );
    require('config.php');

    exec( MYSQL_PATH.'mysqldump', $return, $test );
    if( $test == '126' ) die('Check permissions on the server. PHP cannot execute the "mysqldump" function. Cannot create backup file.');
    if( $test == '127' ) die('Check your path name configuration. PHP cannot access the "mysqldump" function. Cannot create backup file.');
    if( $test != '0' && $test != '1' ) die('Unknown error. Cannot create backup file. Error Code: '.$test);
            
    if( !is_dir( $path ) ){
        //create folder
        mkdir( $path );
    }
    if( !file_exists($path.'/.htaccess') ){
        //create .htaccess file
        $handle = fopen($path.'/.htaccess', 'x+');
        fwrite($handle, 'deny from all');
        fclose($handle);
    }
    
    header( 'Content-Type: text/html; charset='.K_CHARSET );
    echo '<a href="index.php"><< BACK</a>';
    echo '<center style="padding-top:10%;">';
    if( !isset($_POST['submit']) ){
        echo '<form method="post" action="">';
        echo '<input type="submit" name="submit" value="Create a Backup of the Current Database?" style="font-size:1em; height:2em;">';
        echo '</form>';
    }else{
        $now = date('Y-m-d_H.i.s');
        $path .= K_DB_NAME.'_'.$now.'.sql';
        
        $command= MYSQL_PATH. 'mysqldump --user='.K_DB_USER.' --password='.K_DB_PASSWORD.' --host='.K_DB_HOST.' '.K_DB_NAME.' > '.$path;
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
        message.innerHTML = 'Download started. Check your downloads folder.';
        var button = document.getElementsByTagName('input');
        button[0].parentNode.replaceChild(message, button[0]);
    }       
</script>
HERE;
echo $script;