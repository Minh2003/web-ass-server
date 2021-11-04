<?php

namespace Controllers;

use blog_model;
use dish_model;
use Db;

class AdminController
{
  public function createDish()
  {
    $db = Db::getInstance();

    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    $sql = "Insert into dish (name,description,image) Values ('$name','$description','$image')";
    mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $dish = new dish_model($id, $name, $description, $image);
    echo json_encode($dish);
  }

  public function deleteDish($param)
  {
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from dish where id = $id";
    mysqli_query($db, $sql);

    echo json_encode("Successfully!");
  }

  public function createBlog()
  {
    $db = Db::getInstance();

    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_POST['image'];
    $date = time();

    $sql = "insert into blog(title, content, image) values ('$title','$content', '$image')";
    mysqli_query($db, $sql);
    $id = mysqli_insert_id($db);

    $blog = new blog_model($id, $title, $content, $image, $date);
    echo json_encode($blog);
  }

  public function deleteBlog($param)
  {
    $db = Db::getInstance();

    $id = substr($param, 1, -1);

    $sql = "delete from blog where id = $id";
    mysqli_query($db, $sql);

    echo json_encode("Successfully!");
  }

  public function updateBlog($param)
  {
    $db = Db::getInstance();

    $id = substr($param, 1, -1);
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_POST['image'];

    $sql = "update blog set title = '$title', content = '$content', image = '$image' where id = '$id'";
    mysqli_query($db, $sql);

    $date = time();
    $blog = new blog_model($id, $title, $content, $image, $date);
    echo json_encode($blog);
  }

  public function deleteUser($user_id)
  {
  }

  public function deleteComment()
  {
  }
}
