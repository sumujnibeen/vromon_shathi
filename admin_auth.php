<?php
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) == 'admin') {
} else {
    header('Location: index.php');
    exit;
}
?>
<!--    ignore -->