<?php

// Do this when passing the object as a query string.

use App\Models\ModelRepository\UserRepository;

require './../vendor/autoload.php';

use function App\Helpers\redirect;

$error = '';
$success = '';
$accountHolder = '';
$accountHolderNumber = '';
$amount = '';

$userRepo = new UserRepository();

if(!isset($_COOKIE['user']))
{
    $url =$_SERVER['REQUEST_URI'];
    return redirect('Login.php', $url);
}else{
    $user = $_COOKIE['user'];
    $user = unserialize($user);
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    try{
        $accountHolderNumber = $_REQUEST['account_number'];

        if (!is_numeric($accountHolderNumber))
        {
            throw new ValueError('Invalid. Please enter a valid Account Number ');
        }

        #return username
        $accountHolder = $userRepo->getAccountHolder($accountHolderNumber); // use ajax to render this. Move this to transfer method under account
        // echo $accountHolder;
        if(!$accountHolder)
        {
            throw new ValueError('This user does not exist');
        }
        $amount = $_REQUEST['amount'];
        $success = $user->getAccount()->transfer($accountHolderNumber, $amount);
        
    }catch(ValueError $err){
        $error = $err->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Transfer</title>
</head>
<body>
    <div class="container" style="margin-top:50px;">

    <?php require '../partials/nav.php' ?>
    <?php require '../partials/title.php' ?>

    <h3>Make Transfer</h3>

    <h3>Balance: <?= $user->getAccount()->getBalance() ?></h3>

    <?php if($error): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>
    <?php if($success): ?>
        <div class="alert alert-success">
            Success
        </div>
    <?php endif; ?>

    <form method="POST" action="transfer.php">
        <div class="mb-3">
            <label for="name" class="form-label">Transfer To</label>
            <input type="text" class="form-control" id="account_number" name="account_number" value="<?= $accountHolderNumber ?>">
            <p>Account Holder</p>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Enter amount</label>
            <input type="text" class="form-control" id="amount" name="amount" value="<?= $amount ?>">
        </div>
        <button type="submit" class="btn btn-primary">Make Transfer</button>
    </form>

    </div>
</body>
</html>