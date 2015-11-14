
<?php 
@session_start ();

$currenturl= $_GET['current_url'];
    $filesong = $_GET['song_id']. '.mp3';
//   echo $filesong;die;
    $file = "static/songs/" . $filesong;
     if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
       
    }
    header('Location:'.$currenturl.'');
?>
