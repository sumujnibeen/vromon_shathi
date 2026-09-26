<?php
session_start();

// সমস্ত session data remove করা
$_SESSION = [];

// session destroy করা
session_destroy();

// user কে login page বা homepage এ redirect করা
header("Location: login.php");
exit;
