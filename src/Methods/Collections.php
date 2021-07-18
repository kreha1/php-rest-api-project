<?php
namespace Src\Methods;

class Collections {

  private $db = NULL;

  public function __construct($db) {
    $this->db = $db;
  }

  // GET users
  public function getActiveUsers() {
    $statement = "
      SELECT id, email, created
      FROM users
      WHERE active=1;
    ";

    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  // GET posts
  public function getLatestPosts() {
    $statement = "
      SELECT * FROM posts
      WHERE active = 1
      ORDER BY created;
    ";
  
    try {
      $statement = $this->db->query($statement);
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function getUserPosts($id) {
    $statement = "
      SELECT * FROM posts WHERE user = $id AND active = 1
      ORDER BY created;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
      return $result;
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }
}