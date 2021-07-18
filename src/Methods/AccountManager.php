<?php
namespace Src\Methods;

class AccountManager {

  private $db = NULL;

  public function __construct($db) {
    $this->db = $db;
  }

  private function hashPassword($string) {
    return password_hash($string,  PASSWORD_DEFAULT );
  }

  private function checkExistingAccount($var) {
    $statement = is_int($var) ? "
    SELECT * FROM users WHERE id = $var;
    " : "
    SELECT * FROM users WHERE email = '$var';
  ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return $statement->rowCount();
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }


  public function createAccount($email, $password) {
    $match = $this->checkExistingAccount($email);
    if ($match) return NULL;
    $hash = $this->hashPassword($password);
    $statement = "
      INSERT INTO users
        (email, password, created, active)
      VALUES
        ('$email', '$hash', (SELECT now()), 1);
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return boolval($statement->rowCount());
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }


  public function updatePassword($id, $password) {
    $match = $this->checkExistingAccount($id);
    if (!$match) return NULL;
    $hash = $this->hashPassword($password);
    $statement = "
      UPDATE users
      SET password = '$hash'
      WHERE id = $id;
    ";

    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return boolval($statement->rowCount());
    } catch (\PDOException $e) {
      exit($e->getMessage());
    }
  }

  public function authorize($email, $password) {
    $match = $this->checkExistingAccount($email);
    if (!$match) return NULL;
    $statement = "
    SELECT password FROM users WHERE email = '$email';
  ";
  try {
    $statement = $this->db->prepare($statement);
    $statement->execute();
    ['password' => $pass] = $statement->fetch(\PDO::FETCH_ASSOC);
    return password_verify($password, $pass);
  } catch (\PDOException $e) {
      exit($e->getMessage());
  }
      
  }


  public function activateAccount($id) {
    $match = $this->checkExistingAccount($id);
    if (!$match) return NULL;
    $statement = "
      UPDATE users SET active = 1 WHERE id = $id AND active = 0;
      DROP EVENT IF EXISTS delete_user_$id;
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return boolval($statement->rowCount());
  } catch (\PDOException $e) {
      exit($e->getMessage());
  }
  }


  public function closeAccount($id) {
    $match = $this->checkExistingAccount($id);
    if (!$match) return NULL;
    $statement = "
      UPDATE users SET active = 0 WHERE id = $id AND active = 1;
    ";

    try {
        $statement = $this->db->prepare($statement);
        $statement->execute();
        return boolval($statement->rowCount());
    } catch (\PDOException $e) {
        exit($e->getMessage());
    }
  }

  private function checkAccountActive($id) {
    $statement = "
    SELECT * FROM users WHERE id = $id AND active = 1;
  ";
  try {
    $statement = $this->db->prepare($statement);
    $statement->execute();
    return boolval($statement->rowCount());
  } catch (\PDOException $e) {
      exit($e->getMessage());
  }
      
  }

  private function checkDeleteEvent($id) {
    $statement = "
      SHOW EVENTS WHERE name = 'delete_user_$id';
    ";
    try {
      $statement = $this->db->prepare($statement);
      $statement->execute();
      return boolval($statement->rowCount());
  } catch (\PDOException $e) {
      exit($e->getMessage());
  }
  }


  public function scheduleDeletion($id) {
    $match = $this->checkExistingAccount($id);
    if (!$match) return NULL;
    $isActive = $this->checkAccountActive($id);
    if ($isActive) $this->closeAccount($id);

    $doesEventExist = $this->checkDeleteEvent($id);
    if ($doesEventExist) return false;
  
    $statement = "
      CREATE EVENT delete_user_$id
      ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 MONTH
      DO DELETE FROM users WHERE id = $id;
    ";

    try {
        $statement = $this->db->prepare($statement);
        $statement->execute();
        return true;
    } catch (\PDOException $e) {
        exit($e->getMessage());
    }
  }

}