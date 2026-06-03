<?php
include 'db.php';
session_start();

//  Authentication
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

//  Role Check (STEP 3)
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'editor') {
    echo "Access Denied!";
    exit();
}

// Form handling
if (isset($_POST['submit'])) {

    //  Get input
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    //  Server-side validation 
    if (empty($title) || empty($content)) {
        echo "All fields are required!";
    } else {

        //  Prepared Statement (STEP 1)
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {

            // Better UX
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
    <title>Add Post</title>

    <!--  Client-side validation  -->
    <script>
        function validateForm() {
            let title = document.forms["postForm"]["title"].value.trim();
            let content = document.forms["postForm"]["content"].value.trim();

            if (title === "" || content === "") {
                alert("All fields are required!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h2>Add New Post</h2>

<form method="POST" name="postForm" onsubmit="return validateForm()">

    Title: <br>
    <input type="text" name="title" required><br><br>

    Content: <br>
    <textarea name="content" required></textarea><br><br>

    <button type="submit" name="submit">Add Post</button>
</form>

</body>
</html>