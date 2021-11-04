<?php
  class reservation_model {
    public $id;
    public $date;
    public $description;
    public $NoP;

  public function __construct($id, $date, $description, $NoP) {
    $this->id = $id;
    $this->date = $date;
    $this->description = $description;
    $this->NoP = $NoP;
  }
}
?>