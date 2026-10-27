<?php
declare(strict_types=1);

require_once __DIR__ . '/EnvReader.php';

class Database
{
    private ?PDO $dbConnection = null;

    public function connect(): PDO
    {
        if ($this->dbConnection === null) {
            $config = read_env();
            $dsn = 'mysql:host=' . $config['DB_HOST'] . ';dbname=' . $config['DB_NAME'] . ';charset=utf8mb4';
            $this->dbConnection = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }

        return $this->dbConnection;
    }

    public function execute(string $sqlCommand, array $args = []): void
    {
        $this->connect()->prepare($sqlCommand)->execute($args);
    }

    public function selectData(string $sqlStatement): PDOStatement
    {
        return $this->connect()->query($sqlStatement);
    }

    public function selectRow(string $sqlStatement): array
    {
        return $this->selectData($sqlStatement)->fetch() ?: [];
    }

    public function closeConnection(): void
    {
        $this->dbConnection = null;
    }
}
