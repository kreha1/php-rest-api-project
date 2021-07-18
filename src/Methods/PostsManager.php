<?php
namespace Src\Methods;

class PostsManager {

  private $db = NULL;

  public function __construct($db) {
    $this->db = $db;
  }

  // PUT posts/new
  public function createPost($user_id, $text) {
    $statement = "
      INSERT INTO posts (text, user, created, edited, active)
      VALUES ('$text', $user_id, (SELECT now()), NULL, 1);
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return $statement->rowCount()? true : false;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  // POST posts/{$id}/edit
  public function updatePost($post_id, $text) {
    $statement = "
      UPDATE posts
      SET text = '$text', edited = (SELECT now())
      WHERE id = $post_id;
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return $statement->rowCount() ? true : false;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  private function checkVisibility($id, $active) {
    $statement = "
    SELECT * FROM posts WHERE id = $id AND active = $active;
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return $statement->rowCount() ? true : false;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function showPost($id, $active) {
    $match = $this->checkVisibility($id, $active ? 0 : 1);
    if (!$match) return NULL;
    $statement = "
    UPDATE posts SET active = $active WHERE id = $id;
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return $statement->rowCount() ? true : false;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function deletePost($id) {
    $statement = "
      DELETE FROM posts WHERE id = $id;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return boolval($statement->rowCount());
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

}