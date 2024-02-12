<?php

namespace App\Models;

use App\Models\Classes\Account;
use App\Models\Classes\NormalUser;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $user;
    protected $accountNumber;
    protected $balance;

    protected function setUp(): void
    {
        $this->user = new NormalUser();
        $this->accountNumber = '1';
        $this->balance = '0';
    }

    protected function tearDown(): void
    {
        $this->user->name = '';
    }

    public function testUserObjectIsBeingCreated()
    {
        $this->assertInstanceOf(NormalUser::class, $this->user);
    }

    public function testUserHasAccount()
    {
        // Setup

        //A user
        $user = $this->user;
        
        // An account
        $account = new Account($this->accountNumber, $this->balance);
        
        // Do something
        $this->user->setAccount($this->accountNumber, $this->balance);

        // Make Assertions
        $this->assertEquals($account, $this->user->getAccount());
    }

    public function testUserHasAccountNumber()
    {
        // Setup

        //A user
        $user = $this->user;
        
        // An account
        $account = new Account($this->accountNumber, $this->balance);
        
        // Do something
        $this->user->setAccount($this->accountNumber, $this->balance);
        $userAccount = $this->user->getAccount();
        $accountNumber = $userAccount->getAccountNumber();

        // Make Assertions
        $this->assertEquals(1, $accountNumber);
    }

    /** @test */
    public function user_account_can_only_be_set_once()
    {
        // Setup

        //A user
        $user = $this->user;
        
        // An account
        $account = new Account($this->accountNumber, $this->balance);//fix test to take in arguments.
        $account2 = new Account($this->accountNumber, $this->balance);
        
        // Do something
        $this->user->setAccount($this->accountNumber, $this->balance);
        $this->user->setAccount($this->accountNumber=2, $this->balance);
        $userAccount = $this->user->getAccount();
        $accountNumber = $userAccount->getAccountNumber();

        // Make Assertions
        $this->assertEquals(1, $accountNumber);
    }
}

?>