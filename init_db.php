<?php
require 'bootstrap.php';

$statement = <<<EOS
    DROP TABLE IF EXISTS jwt;
    
    CREATE TABLE jwt (
        id INT NOT NULL AUTO_INCREMENT,
        apikey CHAR(16) NOT NULL,
        secretkey VARCHAR(128) NOT NULL,
        created DATETIME(3) NOT NULL,
        flag TINYINT(4) NOT NULL DEFAULT 1,
        PRIMARY KEY (id)
    ) ENGINE=INNODB DEFAULT CHARSET=UTF8;

    ALTER TABLE IF EXISTS posts drop FOREIGN KEY IF EXISTS posts_ibfk_1;
    DROP TABLE IF EXISTS users, posts;

    CREATE TABLE users (
        id INT NOT NULL AUTO_INCREMENT,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(100) NOT NULL,
        created DATETIME(3) NOT NULL,
        active TINYINT(4) NOT NULL DEFAULT 1,
        PRIMARY KEY (id)
        UNIQUE KEY (email)
    ) ENGINE=INNODB DEFAULT CHARSET=UTF8;

    INSERT INTO users
        (id, email, password, created, active)
    VALUES
        (1, 'someone@gmail.com', '$2y$10$X.lC7poLnPaIAE5BapcZNOgUSM8vHid44Vi2es2HLQ4txN5RcKR06', (SELECT utc_timestamp), 1),
        (2, 'other@gmail.com', '$2y$10$fL23n43mqHAdj4W/C4D03unge5dzYMAdYQyWQeG9HojSDYxscY5uG', (SELECT utc_timestamp), 1),
        (3, 'gitara@gmail.com', '$2y$10$Bd1tlYOvKPquqQQyV/W6Ke83UK3LdYxfAK4gwv9RqBKDDjJFkIGbe', (SELECT utc_timestamp), 1),
        (4, 'siema@gmail.com', '$2y$10$MalSxevD.JMqidGdb8znNu6rvMZVRJHvn.ift8zgg5xXwU0zT0fKu', (SELECT utc_timestamp), 1),
        (5, 'jpdjmd@gmail.com', '$2y$10$/IGqDdLI/jgRSMu9WkUJkuapJ.Ko9sTXdyY7tn1nK1HoY5WTxFrei', (SELECT utc_timestamp), 1),
        (6, '2137@gmail.com', '$2y$10$ENbtLRCdy6/m9vkeHBxadeqctN.7je938wjL1967kbBkkrXjRGNb6', (SELECT utc_timestamp), 1),
        (7, 'lol@gmail.com', '$2y$10$.O5v5PrGAS7wi953XKEUc.v8lnCZWYqLuyhS1gOz3/NTG5e1Nx3gu', (SELECT utc_timestamp), 1);

    CREATE TABLE posts (
        id INT NOT NULL AUTO_INCREMENT,
        text VARCHAR(100) NOT NULL,
        user INT DEFAULT NULL,
        created DATETIME(3) NOT NULL,
        edited DATETIME(3) NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (user)
            REFERENCES users(id)
            ON DELETE SET NULL
    ) ENGINE=INNODB DEFAULT CHARSET=UTF8;
EOS;

try {
    $createTable = $dbConnection->exec($statement);
    echo "Success!\n";
} catch (\PDOException $e) {
    exit($e->getMessage());
}
?>