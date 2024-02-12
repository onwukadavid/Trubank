<?php

use App\Models\ModelRepository\UserRepository;

use function App\Helpers\redirect;

require_once './../vendor/autoload.php';

if(isset($_COOKIE['user']))
{
    return redirect('Dashboard.php', $_SERVER['REQUEST_URI']);
}

$userRepo = new UserRepository();
$userData = [];
$user_obj = True;
$error='';

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    if(!$username || !$password)
    {
        $error = 'Please Enter all fields';
        
    }else{

        $userData['username'] = $username;
        $userData['password'] = $password;
        $url = $_SERVER['REQUEST_URI'];
        $user_obj = $userRepo->login($userData, $url, $userRepo);

        if($user_obj)
        {
            $user = serialize($user_obj);
            setcookie('user',$user, time() + 3600);
            redirect('Dashboard.php', $url);
        }

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
        <?php if(!$user_obj):  ?>
            <!-- redirect to dashboard page -->
            <!-- <div class="alert alert-success">
                Success! This user is in the database. 
            </div> -->
            <div class="alert alert-danger">
                Invalid Username/Password
            </div>
        <?php endif; ?>

        <?php if($error):  ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>
        

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="name" class="form-label">Userame</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= $_POST['username'] ?? ''; ?>">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="text" class="form-control" id="password" name="password" value="<?= $_POST['password'] ?? ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <small>Don't have an account? <a href="registration.php">click here to register</a></small>
        <?php //print_r($_SERVER) ?>
    </div>
</body>
</html>