<?php
require_once __DIR__ . '/../vendor/autoload.php';


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

// Use credentials
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
class Database{
    public PDO $conn;
    private  $host; 
    private  $user;
    private  $pass;
    private  $db_name;
    public function __construct(){
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->user = $_ENV['DB_USER'] ?? 'root';
        $this->pass = $_ENV['DB_PASS'] ?? '';
        $this->db_name = $_ENV['DB_NAME'] ?? '';
    }
   
    public function get_connection(): PDO{

            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            return new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        }

        }
