<?php

namespace App\AbstractClasses;

use App\Interfaces\UserInterface;
use App\Models\Classes\Account;

// Refactor code to probabaly have a user repo object as account or user class

/**
 * Class User
 *
 * Abstract class representing a user.
 */
abstract class User implements UserInterface
{
    private int $id;
    private string $username;
    private string $email;
    private \DateTime|string $created_at;
    private \DateTime|string $updated_at;
    private \DateTime|string $last_login;
    private $account = null;
    private bool $is_admin;
    private bool| string $status;

    public function __get($property)
    {
        if(property_exists($this, $property))
        {
            return $this->$property;
        }
    }

    public function __set($property, $value)
    {
        $this->$property = $value;
    }

    public function getId(): int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setAccount(int $accountNumber, int $balance): Account
    {
        if($this->account === null)
        {
            $this->account = new Account($accountNumber, $balance);
        }

        return $this->getAccount();
    }
    
    public function getAccount(): Account
    {
        return $this->account;
    }

    public function getIsAdmin()
    {
        return $this->is_admin;
    }
    public function setIsAdmin($is_admin)
    {
        $this->is_admin = $is_admin;
    }

    public function getStatus()
    {
        return $this->status;
    }
    public function setStatus($status)
    {
        $this->status = $status;
    }
}

?>