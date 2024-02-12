<?php

namespace App\Helpers;

use App\AbstractClasses\User;
// use App\Models\Classes\NormalUser;

/**
 * Generate a unique account number.
 *
 * @return int The generated account number.
 */
function generateAccountNumber(): int
{
    // test accountNumber Value
    // $accountNumber = 6;


    // Get random 10 numbers long integer
    $accountNumber = random_int(1000000000, 9999999999);

    // Check if it has been generated in an account number file
    if(!file_exists('usedAccountNumber.txt'))
    {
        $file = fopen('usedAccountNumber.txt', 'a');
        fclose($file);
    }
    $accountNumberFile = fopen('usedAccountNumber.txt', 'a+');
    $usedAccountNumbers = [];

    while(!feof($accountNumberFile))
    {
        $usedAccountNumbers[] = (int) fgets($accountNumberFile);
    }

    while(True)
    {
        
        if(!in_array($accountNumber, $usedAccountNumbers, True))
        {
            fwrite($accountNumberFile, $accountNumber.PHP_EOL);
            fclose($accountNumberFile);
            break;
        }
        echo"This account already exists.\nGenerating a new account.\n";
        // $accountNumber+=1;
    }

    // return accountNumber
    return $accountNumber;

}
// generateAccountNumber();

/**
 * Redirect the user to a specified filename within the same directory which in turn redirects the user to the specified Url.
 *
 * @param string $filename The name of the file to redirect to.
 * @param string $url      The URL or file path.
 * @param mixed  $user     Optional. User information for authentication.
 *
 * @return void
 */
function redirect($filename, $url, $user=null)
{
    $user = isset($_COOKIE['$user']);

    // if(!$user)
    // {
    //     $filePath = dirname($url) . "/{$filename}";
    //     return header("Location: {$filePath}");
    // }
    
    $filePath = dirname($url) . "/{$filename}";
    $redirectUrl = "{$filePath}";

    return header("Location: {$redirectUrl}");
}

/**
 * Update the user cookie with the serialized user object.
 *
 * @param User $user The normal user object to be serialized and stored in the cookie.
 *
 * @return void
 */
function updateUserCookie(User $user)
{
    $user_serializer = serialize($user);
    setcookie('user',$user_serializer,time()+3600);
}

/**
 * Generate a unique transaction ID.
 *
 * @return int The generated transaction ID.
 */
function generateTransactionId()
{
    // test transactionId Value
    // $transactionId = 6;


    // Get random 10 numbers long integer
    $transactionId = random_int(1000000000, 9999999999);

    // Check if it has been generated in an account number file
    if(!file_exists('usedtransactionId.txt'))
    {
        $file = fopen('usedtransactionId.txt', 'a');
        fclose($file);
    }
    $transactionIdFile = fopen('usedtransactionId.txt', 'a+');
    $usedtransactionIds = [];

    while(!feof($transactionIdFile))
    {
        $usedtransactionIds[] = (int) fgets($transactionIdFile);
    }

    while(True)
    {
        
        if(!in_array($transactionId, $usedtransactionIds, True))
        {
            fwrite($transactionIdFile, $transactionId.PHP_EOL);
            fclose($transactionIdFile);
            break;
        }
        echo"This account already exists.\nGenerating a new account.\n";
        // $transactionId+=1;
    }

    // return transactionId
    return $transactionId;

}
?>