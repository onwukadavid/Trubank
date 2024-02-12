<?php

namespace App\Interfaces;

use App\AbstractClasses\User;
use App\Models\ModelRepository\UserRepository;

interface AccountInterface
{
    public function getAccountNumber();
    public function deposit(string $transactionType, string $amount);
    public function withdraw(string $transactionType, string $amount);
    public function transfer(string $BeneficiaryAccountNumber,string $amount);
    public function generateStatement(int $userId, string $username, UserRepository $userRepo);
}

?>