<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
// Get ID from URL
$id = $_GET['id'];

// Delete query
$sql = "DELETE FROM posts WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo "Post deleted successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>