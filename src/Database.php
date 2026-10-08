<?php
require_once __DIR__ . '/../vendor/autoload.php';

set_exception_handler("ErrorHandler::handle_exception");
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

// Use credentials

class Database{
    public PDO $conn;
    private  string $host; 
    private  string $user;
    private  string $pass;
    private  string $db_name;
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
