<?php

namespace App\Models\ModelRepository;

// require './../../../vendor/autoload.php';
require __DIR__ . './../../../vendor/autoload.php';


use App\AbstractClasses\User;
use App\Interfaces\UserInterface;
use App\Models\Classes\NormalUser;
use PDOException;
use ValueError;

use function App\Helpers\generateAccountNumber;
use function App\Helpers\generateTransactionId;
use function App\Helpers\redirect;

class UserRepository extends ModelRepository
{

    /************************************************* Create *************************************************/
    public function save(array $userData): bool
    {
        $username = $userData['username'];
        $email = $userData['email'];
        $password = $userData['password'];
        $account_number = $this->getGeneratedAccountNumber();

        $sql = "INSERT INTO users (username,  email, account_number, password) 
        VALUES (:username, :email, :account_number, :password)";
        
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'username'=>$username,
            'email'=>$email,
            'account_number'=>$account_number,
            'password'=>$password,
        ]);

        return $statement->rowCount() == 1;
    }

    /************************************************* Read *************************************************/
    public function fetchSingleUser(array|string $userData)
    {
        $username = $userData['username'];
        // $password = $userData['password'];

        $sql = "SELECT * FROM users WHERE username = :username";
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'username'=>$username,
        ]);

        $userArray = $statement->fetch(\PDO::FETCH_ASSOC);
        
        return $userArray;
    }

    public function fetchAllUsers()
    {
        $sql = "SELECT * FROM users";
        // return $this->getPdo()->query($sql)->fetchAll(\PDO::FETCH_CLASS, NormalUser::class);
        return $this->getPdo()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
        
    }

    /************************************************* Update *************************************************/

    /************************************************* Delete *************************************************/

    /************************************************* Utility *************************************************/
    public function makeUser(array $userData): NormalUser
    {
        $user = new NormalUser();
        $user->setId($userData['id']);
        $user->setUsername($userData['username']);
        $user->setEmail($userData['email']);
        $user->setIsAdmin($userData['is_admin']);
        $user->setStatus($userData['status']);
        $user->setAccount($userData['account_number'], $userData['balance']);
        
        return $user;
    }


    /**
     * Get generated unique account number.
     *
     * @return int The generated account number.
     */
    private function getGeneratedAccountNumber(): int
    {
        $accountNumber = generateAccountNumber();

        return $accountNumber;
    }

    // Move to auth folder.
    /**
     * Authenticate user login credentials.
     *
     * @param array         $loginDetails The login details provided by the user.
     * @param string        $url          The URL for redirection after successful login.
     * @param UserRepository $userRepo   The user repository for database operations.
     *
     * @return mixed User object upon successful authentication, otherwise false.
     */
    public function login(array $loginDetails, string $url, UserRepository $userRepo)
    {
        $potentialUserData = $this->fetchSingleUser($loginDetails);
        $username = $potentialUserData['username'];
        $password = $potentialUserData['password'];

        $is_same = password_verify($loginDetails['password'], $password);
        if(!$is_same)
        {
            return false;
        }
        $user = $this->makeUser($potentialUserData);
        // redirect('Dashboard.php', $url, $user);
        $this->set_login_in_the_database($username);
        return $user;
    }


    /**
     * Update the last login timestamp for the user in the database.
     *
     * @param string $username The username of the user.
     *
     * @return bool True if the last login timestamp is successfully updated, false otherwise.
     */
    protected function set_login_in_the_database($username)
    {
        $username = $username;
        $last_login = date('Y-m-d H:i:s', time());

        $sql = "UPDATE users SET last_login = :last_login WHERE username = :username";
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'last_login'=>$last_login,
            'username'=>$username,
        ]);
        
        return $statement->rowCount() == 1;
    }


    /**
     * Retrieve all transactions associated with a user.
     *
     * @param int $id The ID of the user.
     *
     * @return array An array containing all transactions associated with the user.
     */
    public function getUserTransactions(int $id)
    {
        $userId = $id;
        $sql = "SELECT * FROM transactions WHERE user_id = :userId ORDER BY time desc";
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'userId'=>$userId
        ]);
        $transactions = $statement->fetchAll(\PDO::FETCH_ASSOC);

        return $transactions;
    }


    /**
     * Update the account balance based on the transaction type (deposit or withdraw).
     *
     * @param string $transactionType The type of transaction (deposit or withdraw).
     * @param int    $amount          The amount to be deposited or withdrawn.
     * @param User   $user            The user performing the transaction.
     *
     * @return bool True if the account balance is successfully updated, false otherwise.
     *
     * @throws ValueError    If an invalid transaction type is provided.
     * @throws PDOException  If an error occurs during database operation.
     */
    public function updateAccountBalance(string $transactionType, int $amount, User $user)
    {// set transactionType to debit or credit
        $accountNumber = $user->getAccount()->getAccountNumber();
        switch($transactionType)
        {
            case 'deposit':
                $sql = "UPDATE users SET balance = balance + :amount WHERE account_number = :accountNumber";
                $tType = 'Credit'; // look for a better name to represent debit and credit.
                break;

            case 'withdraw':
                $sql = "UPDATE users SET balance = balance - :amount WHERE account_number = :accountNumber";
                $tType = 'Debit'; // look for a better name to represent debit and credit.
                break;

            default:
                throw new ValueError('Invalid Transaction type. Expected (deposit, withdraw, transfer)');

        }
        $transactionId = generateTransactionId();
        $userId = $user->getId();
        $transaction_sql = "INSERT INTO transactions(type, amount, transaction_id, user_id)
                            VALUES (:transaction_type, :amount, :transaction_id, :user_id)"; //Add status column
        try{

            $this->getPdo()->beginTransaction();
            $statement = $this->getPdo()->prepare($sql);
            $statement2 = $this->getPdo()->prepare($transaction_sql);
            $statement->execute([
                'amount'=>$amount,
                'accountNumber'=>$accountNumber
            ]);
            $statement2->execute([
                'transaction_type'=>$tType,
                'amount'=>$amount,
                'transaction_id'=>$transactionId,
                'user_id'=>$userId
            ]);
            $this->getPdo()->commit();
        }catch(PDOException $exception){

            $this->getPdo()->rollBack();
            throw new PDOException($exception->getMessage());
        }
        
        return $statement->rowCount() == 1;
    }


    /**
     * Make a transfer of funds between two user accounts.
     *
     * @param User   $user                     The user initiating the transfer.
     * @param string $BeneficiaryAccountNumber The account number of the beneficiary.
     * @param int    $amount                   The amount to be transferred.
     *
     * @return string Status message indicating the success of the transfer operation.
     *
     * @throws PDOException If an error occurs during database operation.
     */
    public function makeTransfer(User $user, string $BeneficiaryAccountNumber, int $amount)
    {
        try{
            $tType1 = 'Debit';
            $tType2 = 'Crebit';

            $senderTransactionId1 = generateTransactionId();
            $beneficiaryTransactionId2 = generateTransactionId();

            $userAccountNumber = $user->getAccount()->getAccountNumber();

            $userId1 = $user->getId();
            $beneficiary = $this->getAccountHolder($BeneficiaryAccountNumber);
            $beneficiaryId = $beneficiary['id'];

            $sql1 = "UPDATE users SET balance = balance - :amount WHERE account_number = :userAccountNumber";
            $sql2 = "UPDATE users SET balance = balance + :amount WHERE account_number = :BeneficiaryAccountNumber";
            $transaction_sql1 = "INSERT INTO transactions(type, amount, transaction_id, user_id)
                            VALUES (:transaction_type, :amount, :transaction_id, :user_id)";
            $transaction_sql2 = "INSERT INTO transactions(type, amount, transaction_id, user_id)
                            VALUES (:transaction_type, :amount, :transaction_id, :user_id)";

            $this->getPdo()->beginTransaction();
            $statement1 = $this->getPdo()->prepare($sql1);
            $statement2 = $this->getPdo()->prepare($sql2);
            $statement3 = $this->getPdo()->prepare($transaction_sql1);
            $statement4 = $this->getPdo()->prepare($transaction_sql2);

            $statement1->execute([
                'amount'=>$amount,
                'userAccountNumber'=>$userAccountNumber
            ]);
            $statement2->execute([
                'amount'=>$amount,
                'BeneficiaryAccountNumber'=>$BeneficiaryAccountNumber
            ]);
            $statement3->execute([
                'transaction_type'=>$tType1,
                'amount'=>$amount,
                'transaction_id'=>$senderTransactionId1,
                'user_id'=>$userId1
            ]);
            $statement4->execute([
                'transaction_type'=>$tType2,
                'amount'=>$amount,
                'transaction_id'=>$beneficiaryTransactionId2,
                'user_id'=>$beneficiaryId
            ]);
            $this->getPdo()->commit();

            return 'Success';

        }catch(PDOException $exception){
            $this->getPdo()->rollBack();
            throw new PDOException($exception->getMessage());
        }
    }


    /**
     * Retrieve the account balance for a user.
     *
     * @param User $user The user object.
     *
     * @return int The account balance.
     */
    public function retrieveAccountBalance(User $user)
    {
        $username = $user->getUsername();
        $accountNumber = $user->getAccount()->getAccountNumber();
        $sql = "SELECT balance FROM users WHERE username = :username AND account_number = :accountNumber";
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'username'=>$username,
            'accountNumber'=>$accountNumber,
        ]);

        $balance = $statement->fetchColumn();

        return $balance;
    }


    /**
     * Retrieve the account holder's information based on the account number.
     *
     * @param string $accountNumber The account number.
     *
     * @return array An array containing the username and user ID.
     */
    public function getAccountHolder(string $accountNumber): array
    {
        $sql = "SELECT username, id FROM users WHERE account_number = :accountNumber";
        $statement = $this->getPdo()->prepare($sql);
        $statement->execute([
            'accountNumber'=>$accountNumber
        ]);

        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        return $user;
    }
}

?>