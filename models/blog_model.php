<?php
class blog_model
{
  public $id;
  public $title;
  public $content;
  public $image;
  public $date;

  public function __construct($id, $title, $content, $image, $date)
  {
    $this->id = $id;
    $this->title = $title;
    $this->content = $content;
    $this->image  = $image;
    $this->date = $date;
  }
}
?>