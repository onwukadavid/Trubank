<?php

require './../vendor/autoload.php';

use function App\Helpers\redirect;

redirect('Login.php', $_SERVER['REQUEST_URI']);
setcookie('user', '', time() - 60);

?>