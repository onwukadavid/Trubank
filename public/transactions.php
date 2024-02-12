<?php

require '../vendor/autoload.php';

use App\Models\ModelRepository\TransactionRepository;
use App\Models\ModelRepository\UserRepository;

use function App\Helpers\redirect;

if(!isset($_COOKIE['user']))
{
    $url =$_SERVER['REQUEST_URI'];
    return redirect('Login.php', $url);
}else{
    $user = $_COOKIE['user'];
    $user = unserialize($user);
}


$userRepo = new UserRepository();
$user = unserialize($_COOKIE['user']);
$userId = $user->getId();
$username = $user->getUsername();
if(!isset($_REQUEST['date']))
{
    $transactions = $userRepo->getUserTransactions($userId);
}else{
    $userTransaction = new TransactionRepository();
    $date = $_REQUEST['date'];
    $transactions = $userTransaction->getTransactionBydate($user, $date);
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $user->getAccount()->generateStatement($userId, $username, $userRepo);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Transactions</title>
</head>
<body>
<div class="container" style="margin-top:50px;">

    <?php require '../partials/nav.php' ?>

    <?php require '../partials/title.php' ?>

    <?php if(count($transactions) < 1): ?>
        <div class="alert alert-warning">
        No transactions were found.
        </div>
    <?php endif ?>


    <div class="container mt-5">
        <form method="GET" action="transactions.php">
            <div class="row justify-content-end">
            <div class="col-auto mb-2">
                <input type="text" class="form-control" placeholder="yy-mm-dd" id="date" name="date">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">Submit</button>
            </div>
            </div>
        </form>
    </div>


    <table class="table table-dark table-hover">
        <thead>
            <tr>
            <th scope="col">Transaction Id</th>
            <th scope="col">Time</th>
            <th scope="col">Type</th>
            <th scope="col">Amount</th>
            <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <th scope="row"><?= $transaction['transaction_id'] ?></th>
                    <td><?= $transaction['time'] ?></td>
                    <td><?= $transaction['type'] ?></td>
                    <td><?= $transaction['amount'] ?></td>
                    <td><?= $transaction['status'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <form action="transactions.php" method="post">
        <button type="submit" class="btn btn-primary">Generate statement</button>
    </form>
</div>
</body>
</html>