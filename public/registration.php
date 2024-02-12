<?php

use App\Models\ModelRepository\UserRepository;

use function App\Helpers\redirect;

require_once './../vendor/autoload.php';

$userRepo = new UserRepository();
$success=false;
$error = [];
$userData = [];

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $confirm_password = $_POST['confirm_password'];
    $password = $_POST['password'];
    $is_same = $password === $confirm_password ?? false;
    
    if(!$is_same)
    {
        $error = ['password'=>'Passwords do not match'];
    }else{
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $userData['username'] = $username;
        $userData['email'] = $email;
        $userData['password'] = $password;
        $success = $userRepo->save($userData);

        $url = $_SERVER['REQUEST_URI'];
        return redirect('Login.php', $url);
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
    <title>Create a user</title>
</head>
<body>
    <div class="container" style="margin-top:50px;">
        <?php if($success):  ?>
            <div class="alert alert-success">
                <!-- redirect to login page -->
                Success! The user has been added to the database.
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger">
                <?= $error['password'] ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="registration.php">
            <div class="mb-3">
                <label for="name" class="form-label">Userame</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $_POST['username'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= $_POST['email'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" id="password" name="password" value="<?= $_POST['password'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm password</label>
                <input type="text" class="form-control" id="confirm_password" name="confirm_password" value="<?= $_POST['confirm_password'] ?? ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <small>Already have an account? <a href="Login.php">Login</a></small>
    </div>
</body>
</html>