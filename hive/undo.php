<?php

session_start();

header('Location: index.php');

$db = include 'database.php';
$stmt = $db->prepare('SELECT * FROM moves WHERE id = '.$_SESSION['last_move']);
$stmt->execute();
$result = $stmt->get_result()->fetch_array();
$_SESSION['last_move'] = $result[5];
set_state($result[6]);

?>
