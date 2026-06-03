<?php
session_start();
include 'db.php';

if (isset($_POST['login'])) {

    //  Get & clean input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    //  Server-side validation (STEP 2)
    if (empty($username) || empty($password)) {
        echo "All fields are required!";
    } else {

        //  Prepared SELECT (STEP 1)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        //  Verify password
        if ($user && password_verify($password, $user['password'])) {

            //  Session security
            session_regenerate_id(true);

            //  Store session data
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // 🔥 IMPORTANT (STEP 3)

            //  Redirect
            header("Location: index.php");
            exit();

        } else {
            echo "Invalid username or password!";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <!--  Client-side validation -->
    <script>
        function validateLogin() {
            let username = document.forms["loginForm"]["username"].value.trim();
            let password = document.forms["loginForm"]["password"].value.trim();

            if (username === "" || password === "") {
                alert("All fields are required!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<h2>Login</h2>

<form method="POST" name="loginForm" onsubmit="return validateLogin()">
    
    Username:<br>
    <input type="text" name="username" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <button type="submit" name="login">Login</button><br><br>

    <a href="register.php">Register</a>
</form>

</body>
</html>