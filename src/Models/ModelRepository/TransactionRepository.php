<?php

namespace App\Models\ModelRepository;

use App\AbstractClasses\User;
// use App\Models\Classes\NormalUser;

/**
 * Class TransactionRepository
 *
 * Handles database operations related to transactions.
 */
class TransactionRepository extends ModelRepository
{
    /**
     * Retrieve transactions for a user on a specific date.
     *
     * @param User   $user The user object.
     * @param string $date The date to filter transactions.
     *
     * @return array An array containing transactions for the user on the specified date.
     */
    public function getTransactionBydate(User $user, string $date)
    {
        //format date to how it is in the db
        $userId = $user->getId();
        $sql = 'SELECT * FROM transactions WHERE user_id = :userId AND date(time) = :dateFilter';
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
          'userId'=>$userId,
          'dateFilter'=>$date  
        ]);
        $transactions = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $transactions;
    }


    /**
     * Retrieve all transactions with associated user information.
     *
     * @return array An array containing all transactions with associated user information.
     */
    public function getAllTransactions()
    {
      $sql = "SELECT user.username, user.email, user.account_number, transaction.type, 
              transaction.amount, transaction.time, transaction.transaction_id
              FROM users AS user
              LEFT JOIN transactions AS transaction
              ON user.id = transaction.user_id";

      $statement = $this->getPdo()->query($sql);
      $transactions = $statement->fetchAll(\PDO::FETCH_ASSOC);

      return $transactions;
    }

    
    /**
     * Retrieve transactions for a specific user by their username.
     *
     * @param string $username The username of the user.
     *
     * @return array An array containing transactions for the specified user.
     */
    public function getTransactionByUsername($username)
    {
      $sql = "SELECT user.username, user.email, user.account_number, transaction.type, 
              transaction.amount, transaction.time, transaction.transaction_id
              FROM users AS user
              LEFT JOIN transactions AS transaction
              ON user.id = transaction.user_id
              WHERE user.username = :username";

      $statement = $this->getPdo()->prepare($sql);
      $statement->execute([
        'username'=>$username,
      ]);
      $transactions = $statement->fetchAll(\PDO::FETCH_ASSOC);

      return $transactions;
    }
}

?>