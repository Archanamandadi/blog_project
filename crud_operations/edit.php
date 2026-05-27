<?php
include 'db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get ID from URL
$id = $_GET['id'];

// Fetch existing data
$result = $conn->query("SELECT * FROM posts WHERE id=$id");
$row = $result->fetch_assoc();

// Update when form submitted
if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $sql = "UPDATE posts SET title='$title', content='$content' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "Post updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
</head>
<body>

<h2>Edit Post</h2>

<form method="POST">
    Title:<br>
    <input type="text" name="title" value="<?php echo $row['title']; ?>"><br><br>

    Content:<br>
    <textarea name="content"><?php echo $row['content']; ?></textarea><br><br>

    <button type="submit" name="update">Update</button>
</form>

</body>
</html>