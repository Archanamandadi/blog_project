<?php
include 'db.php';
session_start();

// 🔐 Authentication check
if (!isset($_SESSION['username'])) {
    header("Location: ../crud_operations/login.php");
    exit();
}

// 🔍 Search
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// 📄 Pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 🧠 Query
$sql = "SELECT * FROM posts";

if (!empty($search)) {
    $sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
}

$sql .= " LIMIT $offset, $limit";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

    <h2>All Blog Posts</h2>

    <a class="btn" href="../crud_operations/create.php">+ Add New Post</a>

    <!-- 🔍 Search -->
    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search posts..."
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- 📌 Posts -->
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
    ?>
        <div class="post">
            <h3><?php echo $row['title']; ?></h3>
            <p><?php echo $row['content']; ?></p>

            <div class="actions">
                <a href="../crud_operations/update.php?id=<?php echo $row['id']; ?>">Edit</a>
                <a href="../crud_operations/delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?')">Delete</a>
            </div>
        </div>
    <?php
        }
    } else {
        echo "<p>No posts found</p>";
    }
    ?>

    <!-- 📄 Pagination -->
    <div class="pagination">
    <?php
    $total_sql = "SELECT COUNT(*) as total FROM posts";

    if (!empty($search)) {
        $total_sql .= " WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
    }

    $total_result = $conn->query($total_sql);
    $total_row = $total_result->fetch_assoc();
    $total_posts = $total_row['total'];

    $total_pages = ceil($total_posts / $limit);

    for ($i = 1; $i <= $total_pages; $i++) {
        $active = ($i == $page) ? "active" : "";
        echo "<a class='$active' href='?page=$i&search=$search'>$i</a>";
    }
    ?>
    </div>

</div>

</body>
</html>