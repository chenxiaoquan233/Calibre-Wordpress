<?php
require_once(__DIR__.'/Book.php');
class CalibreDB{
    private static function connect() {
        $calibre_dir = get_option('calibre_database_path');
        $calibre_db = $calibre_dir."/metadata.db";
        $db = new SQLite3($calibre_db);
        return $db;
    }

    private static function disconnect($db) {
        $db->close();
    }

    public static function get_book_list($page, $page_size) {
        $db = CalibreDB::connect();
        $offset = ($page - 1) * $page_size;
        $sql =<<<EOF
            SELECT id, title, author_sort, has_cover
            FROM books
            LIMIT $page_size OFFSET $offset;
        EOF;
        
        $ret = $db->query($sql);
        $book_list = array();
        while($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            array_push($book_list, new Book($row['id'], $row['title'], $row['author_sort'], $row['has_cover']));
        }
        CalibreDB::disconnect($db);
        return $book_list;
    }

    public static function get_book_path_by_id($id) {
        $db = CalibreDB::connect();
        $sql =<<<EOF
            SELECT path
            FROM books
            WHERE id=$id;
        EOF;
        
        if($ret = $db->query($sql)) {
            if($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                $path = $row['path'];
            } else {
                $path = "";
            }
        } else {
            $path = "";
        }
        CalibreDB::disconnect($db);
        return $path;
    }
}
?>