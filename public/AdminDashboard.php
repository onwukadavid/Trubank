<?php

    // Do this when passing the object as a query string.
    require './../vendor/autoload.php';

    use App\Models\Classes\NormalUser;
    use App\Models\ModelRepository\UserRepository;

    use function App\Helpers\redirect;
    
    if(!isset($_COOKIE['user']))
    {
        $url =$_SERVER['REQUEST_URI'];
        return redirect('Login.php', $url);
    }else{
        $user = unserialize($_COOKIE['user']);
    }

    if($user->is_admin==False)
    {
        $url =$_SERVER['REQUEST_URI'];
        return redirect('Login.php', $url);
    }

    $userRepo = new UserRepository();
    
    if(!isset($_REQUEST['username']))
    {
        $users = $userRepo->fetchAllUsers();
    }else{
        $userData['username'] = $_REQUEST['username'];
        $users['user'] = $userRepo->fetchSingleUser($userData);
        if($users['user'] === False)
        {
            $users = False;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
     integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->
    <title>Admin Dashboard</title>
</head>
<body>
    <div class="container" style="margin-top:50px;">
        <!-- <h5>Ensure That the data is synced on 2 different browsers</h5> -->

        <?php require '../partials/nav.php' ?>

        <?php require '../partials/title.php' ?>

        
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <!-- H4 Tag at the extreme left end -->
                    <h4>Admin ID: </h4>
                </div>
                <div class="container mt-5">
                    <form method="GET" action="AdminDashboard.php">
                        <div class="row justify-content-end">
                        <div class="col-auto mb-2">
                            <input type="text" class="form-control" placeholder="Enter username" id="username" name="username">
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <table class="table table-dark table-hover">
            <thead>
                <tr>
                <th scope="col">User Id</th>
                <th scope="col">Username</th>
                <th scope="col">Email</th>
                <th scope="col">Account Number</th>
                <th scope="col">Created At</th>
                <th scope="col">Last Login</th>
                <th scope="col">User type</th>
                <th scope="col">Status</th>
                </tr>
            </thead>
            <?php if($users === False): ?>
                <div class="alert alert-warning">
                    No Userwas found! Please ensure you entered the full name
                </div>
            <?php else: ?>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <th scope="row"><?= $user['id'] ?></th>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['account_number'] ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td><?= $user['last_login'] ?></td>
                            <td><?= $user['is_admin'] ? 'Admin' : 'User' ?></td>
                            <td><?= $user['status'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            <?php endif; ?>
        </table>
        <!-- <p>deactivate</p> -->
    </div>


    <script>
        function updateDropdownText(text) {
            document.getElementById('dropdownMenuButton').innerText = text;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>