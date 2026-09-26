<?php
function connect()
{
    $db = new mysqli("localhost", "root", "", "tourism_management");
    return $db;
}

?>