<?php

namespace App\Models\ModelRepository;

use PDO;
use PDOException;


/**
 * Class ModelRepository
 *
 * Base class for interacting with the database.
 */
class ModelRepository
{
    protected $pdo = null;

    /**
     * Get the PDO instance.
     *
     * @return \PDO The PDO instance.
     *
     * @throws \PDOException If connection to the database fails.
     */
    protected function getPdo(): \PDO
    {
        
        if($this->pdo === null)
        {
            try{

                $host = '127.0.0.1';
                $dbname = 'trubank';
                $charset = 'utf8mb4';
        
                $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

                $options = [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ];
        
                $this->pdo = new PDO($dsn, $username='root', $password='', $options);

                
            }catch(PDOException $PDOException){
                
                throw new PDOException($PDOException->getMessage(), (int) $PDOException->getCode());
                
            }
        }

        return $this->pdo;
    }
}

?>