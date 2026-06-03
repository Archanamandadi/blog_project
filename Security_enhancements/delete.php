<?php
include 'db.php';
session_start();

//  Authentication
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

//  Role Check (STEP 3)
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied!";
    exit();
}

//  Validate ID (STEP 2)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ID";
    exit();
}

$id = (int) $_GET['id'];

//  Prepared DELETE 
$stmt = $conn->prepare("DELETE FROM posts WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    //  Redirect after delete (better UX)
    header("Location: index.php");
    exit();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
?>