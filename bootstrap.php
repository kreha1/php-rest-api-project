<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;
$dotenv = new DotEnv(__DIR__);
$dotenv->load();

use Src\System\Database;
$dbConnection = (new Database())->getConnection();