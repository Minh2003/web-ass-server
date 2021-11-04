<?php
class dish_model
{
  public $id;
  public $name;
  public $description;
  public $image;

  public function __construct($id, $name, $description, $image)
  {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->image  = $image;
  }
}
?>