<?php

namespace App\Models\Classes;

use App\AbstractClasses\User;
use App\Interfaces\AccountInterface;
use App\Models\ModelRepository\UserRepository;
use Exception;
use ValueError;

use function App\Helpers\updateUserCookie;

// Refactor code to probabaly have a user repo object as account or user class
/**
 * Class Account
 *
 * Represents a bank account.
 */
class Account implements AccountInterface
{
    private $accountNumber;
    private int $balance;
    // add transaction attribute to be a composition of the transaction class

    /**
     * Account constructor.
     *
     * @param string $accountNumber The account number.
     * @param string $balance      The initial balance of the account.
     */
    public function __construct(string $accountNumber, string $balance)
    {
        $this->accountNumber = $accountNumber;
        $this->balance = (int) $balance;
    }

    public function getAccountNumber()
    {
        return $this->accountNumber;
    }


    public function getBalance()
    {
        return $this->balance;
    }



    /**
     * Deposit funds into the account.
     *
     * @param string $transactionType The type of transaction.
     * @param string $amount          The amount to deposit.
     *
     * @return bool The status of the deposit operation.
     *
     * @throws Exception When the provided amount is invalid.
     */
    public function deposit(string $transactionType, string $amount)
    {
        $userRepo = new UserRepository();
        $user = unserialize($_COOKIE['user']);
        // $balance = $userRepo->retrieveAccountBalance($user);# raise error if this doesn't work
        try{

            if(!is_numeric($amount) || (int) $amount <= 0)
            {
                throw new Exception('Invalid. Please enter a valid amount');
            }
            // var_dump($user->getAccount()->getAccountNumber());
            $amount = (int) $amount; 

            // check if i can pass $this rather than $user
            $status = $userRepo->updateAccountBalance($transactionType, $amount, $user); # raise error if this doesn't work
            $balance = $userRepo->retrieveAccountBalance($user);# raise error if this doesn't work

            $this->balance = $balance;
            $user->getAccount()->balance = $balance;
            updateUserCookie($user);

            return $status;

        }catch(ValueError $exception){
            throw new ValueError($exception);
        }

    }


    /**
     * Withdraw funds from the account.
     *
     * @param string $transactionType The type of transaction.
     * @param string $amount          The amount to withdraw.
     *
     * @return bool The status of the withdrawal operation.
     *
     * @throws Exception When the provided amount is invalid or exceeds the account balance.
     */
    public function withdraw(string $transactionType, string $amount)
    {
        //move this below the first if-statement
        $userRepo = new UserRepository();
        $user = unserialize($_COOKIE['user']);
        // $balance = $userRepo->retrieveAccountBalance($user);# raise error if this doesn't work
        try{

            if(!is_numeric($amount) || (int) $amount <= 0 || $amount > $this->balance)
            {
                throw new Exception('Invalid. Please enter a valid amount');
            }

            $amount = (int) $amount; 

            $status = $userRepo->updateAccountBalance($transactionType, $amount, $user); # raise error if this doesn't work
            $balance = $userRepo->retrieveAccountBalance($user);# raise error if this doesn't work

            $this->balance = $balance;
            $user->getAccount()->balance = $balance;
            updateUserCookie($user);

            return $status;

        }catch(ValueError $exception){
            throw new ValueError($exception);
        }

    }


    /**
     * Generate a statement for a user and store it in a text file.
     *
     * @param int            $id       The user ID.
     * @param string         $name     The username.
     * @param UserRepository $userRepo The user repository to retrieve transactions.
     *
     * @return string Status message indicating completion.
     *
     * @throws \Exception If an error occurs during statement generation.
     */
    public function generateStatement(int $id, string $name, UserRepository $userRepo)
    {
        try{

            $userId = $id;
            $username = $name;
    
            $transactions = $userRepo->getUserTransactions($userId);
    
            $file = "{$username}_statement.txt";

            if(file_exists($file))
            {
                // delete contents in the file
                $userFile = fopen($file, 'w');
                fclose($userFile);
            }
    
            foreach($transactions as $transaction)
            {
                $transaction_content = "{$transaction['time']} : {$transaction['transaction_id']} - {$transaction['type']} - {$transaction['amount']} - {$transaction['status']}\n";
                file_put_contents($file, $transaction_content, FILE_APPEND);
            }

            if (file_exists($file)) {
                // Set the appropriate headers for downloading the file
                header('Content-Type: application/octet-stream');
                header("Content-Disposition: attachment; filename={$file}");
                header('Content-Length: ' . filesize($file));
            
                // Read the file and output its content
                readfile($file);
            
                // Exit to prevent any further output
                exit();
            } else {
                // If the file does not exist, handle the error accordingly
                echo "Error: Statement file not found";
            }
            return 'Done';

        }catch(\Exception $exception){

            echo "{$exception->getMessage()}, Code: {$exception->getCode()}";
            exit();

        }

    }


    /**
     * Transfer funds from the current account to the beneficiary account.
     *
     * @param string $BeneficiaryAccountNumber The account number of the beneficiary.
     * @param string $amount                   The amount to transfer.
     *
     * @return bool The status of the transfer operation.
     *
     * @throws ValueError If the provided amount is invalid or exceeds the account balance.
     * @throws Exception If an error occurs during the transfer operation.
     */
    public function transfer(string $BeneficiaryAccountNumber,string $amount)
    {
        try{
            if(!is_numeric($amount) || (int) $amount <= 0 || (int) $amount > $this->balance) 
            {
                throw new ValueError('Invalid. Please enter a valid amount');
            }

            $user = unserialize($_COOKIE['user']);
            $userRepo = new UserRepository();
            $amount = (int) $amount;

            $status = $userRepo->makeTransfer($user, $BeneficiaryAccountNumber, $amount);
            $balance = $userRepo->retrieveAccountBalance($user);

            $this->balance = $balance; // Updates the balance of the current user/user object.
            $user->getAccount()->balance = $balance; // update the cookie to ensure the users balance is updated.
            updateUserCookie($user);

            return $status;

        } catch(ValueError $err){
            throw new ValueError($err->getMessage());
        }catch(Exception $exception){
            throw new Exception($exception->getMessage());
        }

    }

}

?>