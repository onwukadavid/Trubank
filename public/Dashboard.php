<?php

    // Do this when passing the object as a query string.
    require './../vendor/autoload.php';

    use App\Models\Classes\NormalUser;

    use function App\Helpers\redirect;
    
    if(!isset($_COOKIE['user']))
    {
        // $user = 'No user object was passed';
        $url =$_SERVER['REQUEST_URI'];
        return redirect('Login.php', $url);
    }else{
        // $user = $_COOKIE['user'];
        $user = unserialize($_COOKIE['user']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <!-- <h5>Ensure That the data is synced on 2 different browsers</h5> -->

    <?php require '../partials/nav.php' ?>

    <?php require '../partials/title.php' ?>

    <h4>Account Number: <?= $user->getAccount()->getAccountNumber() ?></h4>

    <h3>What would you like to do today?</h3>
    <ul>
        <li><a href="deposit.php">Deposit</a></li>
        <li><a href="withdraw.php">Withdraw</a></li>
        <li><a href="transfer.php">Transfer</a></li>
        <li><a href="transactions.php">View transactions</a></li>
    </ul>
</body>
</html>