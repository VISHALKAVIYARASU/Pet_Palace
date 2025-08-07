<?php
session_start();
header("Content-Type: text/plain");

if (!empty($_SESSION)) {
    print_r($_SESSION);
} else {
    echo "No session data found.";
}
?>
