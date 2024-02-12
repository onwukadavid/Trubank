<?php

namespace App\Interfaces;

interface UserInterface
{
    public function setAccount(int $accountNumber, int $balance);
    public function getAccount();
}

?>