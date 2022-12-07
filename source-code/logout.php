<?php
// Kill all sessions and go to the login page
session_start();
session_unset();
session_destroy();
die(header('Location: login.php'));
