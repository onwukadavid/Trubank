<?php

// Do this when passing the object as a query string.
require './../vendor/autoload.php';

use function App\Helpers\redirect;

$error = '';
$success = '';

if(!isset($_COOKIE['user']))
{
    // $user = 'No user object was passed';
    $url =$_SERVER['REQUEST_URI'];
    return redirect('Login.php', $url);
}else{
    $user = $_COOKIE['user'];
    $user = unserialize($user);
}

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    try
    {
        $amount = $_POST['amount'];
        $success = $user->getAccount()->withdraw('withdraw', $amount);

    }catch(Exception $e){
        $error=$e->getMessage();
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
    <title>Withdraw</title>
</head>
<body>
    <div class="container" style="margin-top:50px;">

        <?php require '../partials/nav.php' ?>
        <?php require '../partials/title.php' ?>

        <h3>Make withdrawal</h3>

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

        <form method="POST" action="withdraw.php">
            <div class="mb-3">
                <label for="name" class="form-label">Enter amount</label>
                <input type="text" class="form-control" id="amount" name="amount">
            </div>
            <button type="submit" class="btn btn-primary">Make withdrawal</button>
        </form>

    </div>
</body>
</html>