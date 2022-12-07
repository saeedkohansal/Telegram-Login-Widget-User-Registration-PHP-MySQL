<?php
// Start the session
session_start();


// When the user is logged in, go to the user page
if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == TRUE) {
    die(header('Location: user.php'));
}


// Import database connection and class
require('db-config.php');


// Place bot token of your bot here
define('BOT_TOKEN', 'XXXXXXXXXXXX:XXXXXXXXXXXXXXXXXXXXXXXX');


// The Telegram hash is required to authorize
if (!isset($_GET['hash'])) {
    die('Telegram hash not found');
}


// Official Telegram authorization - function
function checkTelegramAuthorization($auth_data)
{
    $check_hash = $auth_data['hash'];
    unset($auth_data['hash']);
    $data_check_arr = [];
    foreach ($auth_data as $key => $value) {
        $data_check_arr[] = $key . '=' . $value;
    }
    sort($data_check_arr);
    $data_check_string = implode("\n", $data_check_arr);
    $secret_key = hash('sha256', BOT_TOKEN, true);
    $hash = hash_hmac('sha256', $data_check_string, $secret_key);
    if (strcmp($hash, $check_hash) !== 0) {
        throw new Exception('Data is NOT from Telegram');
    }
    if ((time() - $auth_data['auth_date']) > 86400) {
        throw new Exception('Data is outdated');
    }
    return $auth_data;
}


// User authentication - function
function userAuthentication($db, $auth_data)
{
    // Creating user - function
    function createNewUser($db, $auth_data)
    {
        // User not found, so create it
        $id = $db->Insert(
            "INSERT INTO `users`
                (`first_name`, `last_name`, `telegram_id`, `telegram_username`, `profile_picture`, `auth_date`)
                    values (:first_name, :last_name, :telegram_id, :telegram_username, :profile_picture, :auth_date)",
            [
                'first_name'        => $auth_data['first_name'],
                'last_name'         => $auth_data['last_name'],
                'telegram_id'       => $auth_data['id'],
                'telegram_username' => $auth_data['username'],
                'profile_picture'   => $auth_data['photo_url'],
                'auth_date'         => $auth_data['auth_date']
            ]
        );
    }

    // Updating user - function
    function updateExistedUser($db, $auth_data)
    {
        // User found, so update it
        $db->Update(
            "UPDATE `users`
                SET `first_name`        = :first_name,
                    `last_name`         = :last_name,
                    `telegram_username` = :telegram_username,
                    `profile_picture`   = :profile_picture,
                    `auth_date`         = :auth_date
                        WHERE `telegram_id` = :telegram_id",
            [
                'first_name'        => $auth_data['first_name'],
                'last_name'         => $auth_data['last_name'],
                'telegram_username' => $auth_data['username'],
                'profile_picture'   => $auth_data['photo_url'],
                'auth_date'         => $auth_data['auth_date'],
                'telegram_id'       => $auth_data['id']
            ]
        );
    }

    // User checker - function
    function checkUserExists($db, $auth_data)
    {
        // Get the user Telegram ID
        $target_id = $auth_data['id'];

        // Check the user is exist in database or not
        $isUser = $db->Select(
            "SELECT `telegram_id`
                FROM `users`
                    WHERE `telegram_id` = :id",
            [
                'id' => $target_id
            ]
        );

        // Return true if the user exists in database
        if (!empty($isUser) && $isUser[0]['telegram_id'] === $target_id) {
            return TRUE;
        }
    }

    // Check the user
    if (checkUserExists($db, $auth_data) == TRUE) {
        // User found, so update it
        updateExistedUser($db, $auth_data);
    } else {
        // User not found, so create it
        createNewUser($db, $auth_data);
    }

    // Create logged in user session
    $_SESSION = [
        'logged-in' => TRUE,
        'telegram_id' => $auth_data['id']
    ];
}


// Start the process
try {
    // Get the authorized user data from Telegram widget
    $auth_data = checkTelegramAuthorization($_GET);

    // Authenticate the user
    userAuthentication($db, $auth_data);
} catch (Exception $e) {
    // Display errors
    die($e->getMessage());
}


// Go to the user page
die(header('Location: user.php'));
