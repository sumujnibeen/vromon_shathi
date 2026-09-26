<?php
if (!isset($_SESSION['role']) || strtolower($_SESSION['role']) == 'admin') {
    header('Location: ../two_op.php');
    exit;
}
?>
<!--    ignore -->