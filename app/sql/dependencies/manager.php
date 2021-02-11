<?php
class SQLManager {
    private $config, $mode, $connection;
    public function __construct() {
        $this->config = json_decode(file_get_contents(__DIR__."/../../../config.json"), true)['database'];
        $this->mode = $this->config['mode'];
        if (!($this->mode == 'mysql_database' || $this->mode == 'none' || $this->mode == 'sqlite3')) {
            echo "<h1>503 - Service Unavailable</h1><h2>Can't reach the database due to configurationerror: $this->mode</h2>";
            exit();
        }
        $this->config = $this->config[$this->mode];
        switch ($this->mode) {
            case 'mysql_database':
                $this->connection = new mysqli($this->config['host'], $this->config['user'], $this->config['password'], $this->config['database'], $this->config['port']);
                break;
            
            case 'sqlite3':
                $this->connection = new SQLite3($this->config['file'], null, $this->config['encryption_key']);
                break;
            
            default:
                $this->connection = null;
                break;
        }
        
    }

    public function query($queryString) {
        return $this->connection->query($queryString);
    }

    public function fetch_all($sqlResult) {
        if ($this->mode == 'mysql_database') {
            return $sqlResult->fetch_all();
        } elseif ($this->mode == 'sqlite3') {
            return $sqlResult->fetchArray();
        }
        
    }

    public function fetch_array($sqlResult) {
        if ($this->mode == 'mysql_database') {
            return $sqlResult->fetch_array();
        } elseif ($this->mode == 'sqlite3') {
            return $sqlResult->fetchArray();
        }
    }

    public function fetch_assoc($sqlResult) {
        if ($this->mode == 'mysql_database') {
            return $sqlResult->fetch_assoc();
        } elseif ($this->mode == 'sqlite3') {
            return $sqlResult->fetchArray();
        }
    }
}