<?php

@session_start();


define('ROOT_DIR', dirname(__FILE__));

define('INCLUDE_DIR', ROOT_DIR . '/includes');

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once INCLUDE_DIR . '/member.php';

require_once ROOT_DIR . '/modules/functions.php';

header('Content-type: text/json');

header('Content-type: application/json');

    $myalbum = trim($_GET['title']);
    $myalbum = str_replace(' ','_',$myalbum);
    $myalbumfolder = $_GET['album_id'];

    $myNewFolderPath = "./static/" . $myalbumfolder . "";
    if (mkdir($myNewFolderPath, 0777)) {
        
    } else {
        // something went wrong
    }

    $db->query("SELECT id from vass_songs WHERE album_id  = '" . $myalbumfolder . "' ");

    while ($row = $db->get_row()) {
        $songid[] = $row['id'];
//        print_r($row);
//       echo $row['id']; 
    }
//       print_r($songid);die;
    foreach ($songid as $value) {
        copy("./static/songs/" . $value . ".mp3", "./static/" . $myalbumfolder . "/" . $value . ".mp3");
//       print_r($value);
    }

//   echo $row['id'];
    $the_folder = "./static/" . $myalbumfolder . "";
    $zip_file_name =trim(''.$myalbum.'.zip');
//    echo $zip_file_name;die;

    $download_file = true;

//$delete_file_after_download= true; doesnt work!!


    class FlxZipArchive extends ZipArchive {

        /** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  * */
        public function addDir($location, $name) {
            $this->addEmptyDir($name);

            $this->addDirDo($location, $name);
        }

// EO addDir;

        /**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann
         * @access private   * */
        private function addDirDo($location, $name) {
            $name .= '/';
            $location .= '/';

            // Read all Files in Dir
            $dir = opendir($location);
            while ($file = readdir($dir)) {
                if ($file == '.' || $file == '..')
                    continue;
                // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
                $do = (filetype($location . $file) == 'dir') ? 'addDir' : 'addFile';
                $this->$do($location . $file, $name . $file);
            }
        }

    }

    $za = new FlxZipArchive;
    $res = $za->open($zip_file_name, ZipArchive::CREATE);
    if ($res === TRUE) {
        $za->addDir($the_folder, basename($the_folder));
        $za->close();
    } else {
        echo 'Could not create a zip archive';
    }
  
    $files = glob('./static/'. $myalbumfolder .'/*'); // get all file names
    foreach($files as $file){ // iterate files
    if(is_file($file))
    unlink($file); // delete file
   }
   rmdir("./static/" . $myalbumfolder . "");

    if ($download_file) {
       ob_get_clean();
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=" . basename($zip_file_name) . ";");
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . filesize($zip_file_name));
        readfile($zip_file_name);
        unlink($zip_file_name); 
     
    } 

 header("Location:".$_GET['currenturl']."");

?>
