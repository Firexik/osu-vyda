<?php
require_once('EnvReader.php');

$config = read_env();

$dsn = 'mysql:host='.$config['DB_HOST'].';dbname='.$config['DB_NAME'];

class Database {

    private $dbConnection;

    public function connect(){
        try {
            $this->dbConnection = new PDO($GLOBALS['dsn'], $GLOBALS['config']['DB_USER'],
                                          $GLOBALS['config']['DB_PASSWORD']);
            //print "Connected to DB.\n";
        } catch (PDOException $e) {
            print "Couldn't connect to the database: " . $e->getMessage();
        }
        return $this->dbConnection;
    }

    public function execute($sqlCommand, $args = []){
        try{
            $dbStatement = $this->dbConnection->prepare($sqlCommand);
            if(count($args) > 0) {
                $dbStatement->execute($args);
            }
            else {
                $dbStatement->execute();
            }
        } catch(PDOException $e) {
            print "Couldn't commit SQL command: ". $e->getMessage();
        }
    }
    public function selectData($sqlStatement) : PDOStatement{
        try {
            $data = $this->dbConnection->query($sqlStatement);
            return $data;
        } catch (PDOException $e) {
            print "Couldn't select from table: " . $e->getMessage();
        }
    }

    public function selectRow($sqlStatement) {
        try {
            $data = $this->dbConnection->query($sqlStatement)->fetch();
            if(!$data) return [];
            else return $data;
        } catch (PDOException $e) {
            print "Couldn't select from table: " . $e->getMessage();
        }
    }

    public function closeConnection() {
        $this->dbConnection=null;
    }
}