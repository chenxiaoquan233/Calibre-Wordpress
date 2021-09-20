<?php
require_once(__DIR__."/../../../../wp-load.php");
require_once(__DIR__."/..//CalibreDB.php");

if(isset($_POST['id']) && isset($_POST['file_type'])) {
    $file_path = get_option('calibre_database_path').'/'.CalibreDB::get_book_path_by_id($_POST['id']);
    $book_file = NULL;
    if($dir = opendir($file_path)) {
        while($file = $file_path.'/'.readdir($dir)) {
            $file_ext = pathinfo($file)['extension'];
            if(is_file($file) && $file_ext == $_POST['file_type']) {
                $book_file = $file;
                break;
            }
        }
    }
    if($_POST['file_type'] == 'pdf' && !is_null($book_file)) {
        header("Content-type: application/pdf");
        header("Content-Length: ".filesize($book_file));
        readfile($book_file);
    }
}
?>