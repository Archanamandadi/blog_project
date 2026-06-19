<?php
include 'db.php';
session_start();
<div style="background:#333; padding:10px;">
    <a href="index.php" style="color:white; margin-right:15px;">Home</a>
    <a href="create.php" style="color:white; margin-right:15px;">Add Post</a>
    <a href="logout.php" style="color:white;">Logout</a>
</div>

//  Authentication
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

//  Role Check 
if ($_SESSION['role'] !== 'admin') {
    echo "Access Denied!";
    exit();
}

//  Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid ID";
    exit();
}

$id = (int) $_GET['id'];

//  Fetch existing data
$stmt = $conn->prepare("SELECT * FROM posts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "Post not found";
    exit();
}

//  Update
if (isset($_POST['update'])) {

    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    //  Server-side validation (STEP 2)
    if (empty($title) || empty($content)) {
        echo "All fields are required!";
    } else {

        //  Prepared UPDATE 
        $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
        $stmt->bind_param("ssi", $title, $content, $id);

        if ($stmt->execute()) {

            //  Better UX
            header("Location: index.php");
            exit();

        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>

    <!--  Client-side validation -->
    <script>
        function validateForm() {
            let title = document.forms["editForm"]["title"].value.trim();
            let content = document.forms["editForm"]["content"].value.trim();

            if (title === "" || content === "") {
                alert("All fields are required!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h2>Edit Post</h2>

<form method="POST" name="editForm" onsubmit="return validateForm()">

    Title:<br>
    <input type="text" name="title"
        value="<?php echo htmlspecialchars($row['title']); ?>" required><br><br>

    Content:<br>
    <textarea name="content" required><?php echo htmlspecialchars($row['content']); ?></textarea><br><br>

    <button type="submit" name="update">Update</button>
</form>

</body>
</html>