<?php
    if ( !defined('K_COUCH_DIR') ) define( 'K_COUCH_DIR', str_replace('database/', '', str_replace( '\\', '/', dirname(realpath(__FILE__) ).'/')) );
    require_once( K_COUCH_DIR.'header.php' );

    define( 'K_ADMIN', 1 );
    if( $AUTH->user->access_level < K_ACCESS_LEVEL_ADMIN ) die( 'Please login as admin.' );

    $path = str_replace( '\\', '/', dirname(realpath(__FILE__)) ).'/backups/';
    if( !is_dir( $path ) ){
        die( 'Could not find backup folder at '.$path );
    }

    //DOWNLOAD
    if( isset($_POST['download']) ){
        $FUNCS->validate_nonce( $_POST['download'], $_POST['nonce'] );
        header( "Content-Type: application/octet-stream" );
        header( "Expires: Fri, 01 Jan 1990 00:00:00 GMT" );
        header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
        header( "Pragma: no-cache" );
        header('Content-Disposition: attachment; filename="'.$_POST['download'].'"');        //create download
        readfile( $path.$_POST['download'] );
        exit();
    }

    header( 'Content-Type: text/html; charset='.K_CHARSET );

    echo '<a href="index.php"><< BACK</a>';
    echo '<center style="padding-top:3%;">';

    //DELETE
    if( isset($_POST['delete']) ){
        $FUNCS->validate_nonce( $_POST['delete'], $_POST['nonce'] );
        if( !is_dir( $path.'trash/' ) ){
            //create trash folder if necessary
            mkdir( $path.'trash/' );
        }

        rename($path.$_POST['delete'], $path.'trash/'.$_POST['delete']);
        echo $_POST['delete'].' moved to trash.';
        echo '<form method="post" action=""><input type="hidden" name="restore" value="'.$_POST["delete"].'"/><input type="submit" value="Restore?" style="font-size:1em; height:2em; margin-bottom:2em; margin-top:.5em;"/></form>';
    }

    //UN-TRASH FILE
    if( isset($_POST['restore']) ){
        if( !file_exists($path.'trash/'.$_POST['restore'])){
            die('ERROR:'.$path.'trash/'.$_POST['restore'].' is missing.');
        }
        rename($path.'trash/'.$_POST['restore'], $path.$_POST['restore']);
        echo $_POST['restore'].' was returned from the trash.';
    }

    //RENAME
    if( isset($_POST['rename_to']) ){
        $FUNCS->validate_nonce( $_POST['rename_from'], $_POST['nonce'] );
        if( !file_exists($path.$_POST['rename_from'])){
            die('ERROR:'.$path.$_POST['rename_from'].' is missing.');
        }
        if( !file_exists($path.$_POST['rename_to']) ){
            // Sanitize new file name
            $new_name = trim($_POST['rename_to']);
            $new_name = ( preg_match('~.sql$~', $new_name) ) ? $new_name : $new_name.'.sql';
            $new_name = preg_replace('/[^A-Za-z0-9\-.()]/', '_', $new_name);
            // Remove any runs of periods
            $new_name = preg_replace("([\.]{2,})", '_', $new_name);
            
            rename($path.$_POST['rename_from'], $path.$new_name);
            echo $_POST['rename_from'].' renamed to '.$new_name;
        }else{
            if( $_POST['rename_to'] == '' ){
                echo 'You must enter a filename.';
            }else{
            echo $_POST['rename_to'].' already exists.';
            }
        }
    }

    //EMPTY TRASH
    if( isset($_POST['empty_trash']) ){
        $trash = $path.'trash';
        $files = array_diff(scandir($trash), array('.','..'));
        foreach ($files as $file) {
            $FUNCS->validate_nonce( $file, $_POST['nonce'] );
            break;
        }
        foreach ($files as $file) {
            unlink("$trash/$file");
        }
        echo 'Trash Emptied.';
    }

    //OUTPUT HTML
    $files = scandir($path, 1);
    foreach($files as $file_name){
        $count = (preg_match('~.sql$~', $file_name)) ? 1 : $count;
        if ( $count ) break;
    }
    if( $count != 1 ){
        echo '<p>No backup files available. Use the backup utility to create database backups.</p>';
    }
    
    // Listing of Files
    echo '<style>.odd{background-color:#ddd;}.even{background-color:#eee;}</style>';
    echo '<table cellspacing="0" style="background-color:#eee;border:1px solid #aaa;padding-top:.2em; padding:.3em;">';
    foreach($files as $file_name){
        if (preg_match('~.sql$~', $file_name)){
            $count += 1;
            $row = ( $count % 2 == 0 ) ? 'even' : 'odd';
            $nonce = $FUNCS->create_nonce( $file_name );
$table = <<< HERE

        <tr class="$row">
            <td>
                <form method="post" action="" style="margin:0;">
                    <input type="hidden" name="delete" value="$file_name"/>   
                    <input type="hidden" name="nonce" value="$nonce"/>   
                    <input type="submit" value="X" title="delete" style="font-size:.8em;"/>
                </form>
            </td>
            <td style="padding-right:.6em;padding-left:.3em;min-width:250px;">$file_name</td>
            <td>
                <form method="post" action="" style="margin:0;" onsubmit="{success();}">
                    <input type="hidden" name="download" value="$file_name"/>   
                    <input type="hidden" name="nonce" value="$nonce"/>   
                    <input type="image" src="download.png" alt="download" title="download"/>
                </form>
            </td>
        </tr>
        <tr class="$row">
            <td colspan="3" style="padding-top:.2em;">
                <form method="post" action="" onsubmit="if(!confirm('rename $file_name?')){return false;}">
                    <input type="hidden" name="rename_from" value="$file_name"/>
                    <input type="hidden" name="nonce" value="$nonce"/>   
                    <input type="text" name="rename_to" style="width:72%;"/>
                    <input type="submit" value="Rename" style="width:25%;font-size:.8em;"/>
                </form>
            </td>
        </tr>

HERE;
echo $table;
        }
    }
    echo '</table>';

    $trash = $path.'trash';
    $files = array_diff(scandir($trash), array('.','..'));
    foreach ($files as $file) {
        echo '<form method="post" action=""><input type="submit" name="empty_trash" value="Empty Trash" style="font-size:1em; height:2em; margin-top:2em;"/><input type="hidden" name="nonce" value="'.$FUNCS->create_nonce( $file ).'"/></form>';
        break;
    }

$script = <<< HERE

<script type="text/javascript">
    function success(){
        var message = document.createElement('p');
        message.innerHTML = 'Download started. Check your downloads folder.';
        var page = document.getElementsByTagName('center');
        page[0].insertBefore(message, page[0].firstChild);
        }       
</script>

HERE;
echo $script;

