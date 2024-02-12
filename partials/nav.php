<nav>
    <?php if($user->is_admin == True): ?>
        <a href="Dashboard.php">Personal Dashboard</a> |
        <a href="AdminDashboard.php">Admin Dashboard</a> |
        <a href="logout.php">Logout</a> |
        <a href="transactions.php">View Transactions</a> |
        
        <?php if(strpos($_SERVER['REQUEST_URI'],'AdminDashboard') == True): ?>
            <a href="viewUsersTransactions.php">View users transactions</a> |
        <?php endif ?>
    <?php else: ?>
        <a href="Dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a> |
        <a href="transactions.php">View Transactions</a>
    <?php endif ?>
</nav>

