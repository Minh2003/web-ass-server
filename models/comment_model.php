<?php
  class comment_model {
    public $id;
    public $user_id;
    public $blog_id;
    public $description;

    public function __construct($id, $user_id, $blog_id, $description) {
      $this->id = $id;
      $this->user_id = $user_id;
      $this->blog_id = $blog_id;
      $this->description = $description;
    }
  }
?>