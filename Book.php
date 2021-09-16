<?php
class Book {
    private $id;
    private $title;
    private $author;
    private $has_cover;

    public function __construct($id, $title, $author, $has_cover) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->has_cover = $has_cover;
    }

    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_author() {
        return $this->author;
    }

    public function get_has_cover() {
        return $this->has_cover;
    }
}
?>