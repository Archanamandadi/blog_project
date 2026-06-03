<?php
include 'db.php';
session_start();

if (isset($_POST['register'])) {

    //  Get & clean input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    //  Server-side validation 
    if (empty($username) || empty($password) || empty($role)) {
        echo "All fields are required!";
    } elseif (strlen($password) < 6) {
        echo "Password must be at least 6 characters!";
    } else {

        //  Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        //  Prepared INSERT 
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);

        if ($stmt->execute()) {
            echo "Registration Successful! <a href='login.php'>Login here</a>";
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
    <title>Register</title>

    <!--  Client-side validation -->
    <script>
        function validateRegister() {
            let username = document.forms["regForm"]["username"].value.trim();
            let password = document.forms["regForm"]["password"].value.trim();
            let role = document.forms["regForm"]["role"].value;

            if (username === "" || password === "" || role === "") {
                alert("All fields are required!");
                return false;
            }

            if (password.length < 6) {
                alert("Password must be at least 6 characters!");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>

<h2>Register</h2>

<form method="POST" name="regForm" onsubmit="return validateRegister()">

    Username:<br>
    <input type="text" name="username" required><br><br>

    Password:<br>
    <input type="password" name="password" required><br><br>

    <!-- Role  -->
    Role:<br>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="admin">Admin</option>
        <option value="editor">Editor</option>
    </select><br><br>

    <button type="submit" name="register">Register</button>

</form>

</body>
</html>