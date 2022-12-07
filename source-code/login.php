<?php
// Start the session
session_start();


// When the user is logged in, go to the user page
if (isset($_SESSION['logged-in']) && $_SESSION['logged-in'] == TRUE) {
    die(header('Location: user.php'));
}


// Place username of your bot here
define('BOT_USERNAME', 'XXXXXXXXXXXX');
?>
<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Login</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <h1>Sign Up & Login With Telegram</h1>
    <div class="middle-center">
        <h1>Hello, Anonymous!</h1>
        <script async src="https://telegram.org/js/telegram-widget.js" data-telegram-login="<?= BOT_USERNAME ?>" data-size="large" data-auth-url="auth.php"></script>
    </div>
</body>

</html>