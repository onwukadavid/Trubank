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
$transactionRepo = new TransactionRepository();
$users = $userRepo->fetchAllUsers();

if(!isset($_REQUEST['username']) || $_REQUEST['username'] === 'All')
{
    $transactions = $transactionRepo->getAllTransactions();
}else{
    $username = $_REQUEST['username'];
    $transactions = $transactionRepo->getTransactionByUsername($username);
}

// if($_SERVER['REQUEST_METHOD'] === 'POST')
// {
//     $username = $_POST['username'];
//     $transactions = $transactionRepo->getTransactionByUsername($username);
// }

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


    <div class="container">
        <div class="row">
            <div class="col-6">
                <!-- H4 Tag at the extreme left end -->
                <h4>Admin ID: </h4>
            </div>
            <div class="col-6 text-right mb-2">
                <!-- Dropdown at the extreme right -->
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" id="userTransactionsDropdown" name="userTransactionsDropdown" aria-expanded="false">
                        Select a user
                    </button>
                    <ul class="dropdown-menu">
                        <form action="viewUsersTransactions.php" method="GET">
                        <input class="form-control border-bottom border-secondary" type="submit" style="cursor:pointer; border:none;" id="username" name="username" onclick="updateDropdownText('All')" value="All" />
                        </form>
                        <?php foreach($users as $user): ?>
                            <form action="viewUsersTransactions.php" method="GET">
                                <input style="border:none;" class="form-control border-bottom border-secondary" type="submit" style="cursor: pointer;" id="username" name="username" onclick="updateDropdownText('<?= $user['username'] ?>')" value="<?= $user['username'] ?>" />
                            </form>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <table class="table table-dark table-hover">
        <thead>
            <tr>
            <th scope="col">Username</th>
            <th scope="col">Email</th>
            <th scope="col">Account Number</th>
            <th scope="col">Type</th>
            <th scope="col">Amount</th>
            <th scope="col">Time</th>
            <th scope="col">Transaction Id</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php foreach ($transactions as $transaction): ?>
                <tr>
                    <th scope="row"><?= $transaction['username'] ?></th>
                    <td><?= $transaction['email'] ?></td>
                    <td><?= $transaction['account_number'] ?></td>
                    <td><?= $transaction['type'] ?></td>
                    <td><?= $transaction['amount'] ?></td>
                    <td><?= $transaction['time'] ?></td>
                    <td><?= $transaction['transaction_id'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- <form action="transactions.php" method="post">
        <button type="submit" class="btn btn-primary">Generate statement</button>
    </form> -->

    <script>
        function updateDropdownText(text) {
            document.getElementById('username').innerText = text;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</div>
</body>
</html>