<?php
class Database{
    public PDO $conn;
    private  $host = "db"; 
    private  $user = "root";
    private  $pass ="manzi";
    private  $db_name = "webproject";
     
   
    public function get_connection(): PDO{
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            return new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ]);
        }

        }
