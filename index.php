<?php
include 'db.php';
session_start();

//  Authentication check
if (!isset($_SESSION['username'])) {
    header("Location: ../Security_enhancements/login.php");
    exit();
}

//  User Role
$role = $_SESSION['role'] ?? 'editor';

//  Search
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);

    if (strlen($search) > 100) {
        $search = substr($search, 0, 100);
    }
}

//  Pagination
$limit = 3;
$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

//  Fetch Posts
if (!empty($search)) {

    $sql = "SELECT * FROM posts 
            WHERE title LIKE ? OR content LIKE ? 
            LIMIT ?, ?";

    $stmt = $conn->prepare($sql);

    $search_param = "%{$search}%";
    $stmt->bind_param("ssii", $search_param, $search_param, $offset, $limit);

} else {

    $sql = "SELECT * FROM posts LIMIT ?, ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Posts</title>
    <link rel="stylesheet" href="../advanced_features/style.css">
</head>
<body>

<div class="container">

    <!--  USER INFO -->
    <p>
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 
        (Role: <strong><?php echo htmlspecialchars($role); ?></strong>)
    </p>

    <h2>All Blog Posts</h2>

    <!--  ADD POST -->
    <?php if ($role === 'admin' || $role === 'editor') { ?>
        <a class="btn" href="../Security_enhancements/create.php">+ Add New Post</a>
    <?php } ?>

    <!--  SEARCH -->
    <form method="GET" class="search-box">
        <input type="text" name="search"
               placeholder="Search posts..."
               value="<?php echo htmlspecialchars($search); ?>"
               maxlength="100"
               required>
        <button type="submit">Search</button>
    </form>

    <!--  POSTS -->
    <?php if ($result && $result->num_rows > 0) { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="post">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['content']); ?></p>

                <!--  EDIT -->
                <?php if ($role === 'admin' || $role === 'editor') { ?>
                    <div class="actions">
                        <a href="../Security_enhancements/edit.php?id=<?php echo $row['id']; ?>">
                            Edit
                        </a>

                        <!--  DELETE -->
                        <?php if ($role === 'admin') { ?>
                            <a href="../Security_enhancements/delete.php?id=<?php echo $row['id']; ?>"
                               onclick="return confirm('Delete this post?')">
                                Delete
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>

            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No posts found</p>
    <?php } ?>

    <!--  PAGINATION -->
    <div class="pagination">
        <?php
        if (!empty($search)) {

            $total_sql = "SELECT COUNT(*) as total 
                          FROM posts 
                          WHERE title LIKE ? OR content LIKE ?";

            $stmt_total = $conn->prepare($total_sql);
            $search_param = "%{$search}%";
            $stmt_total->bind_param("ss", $search_param, $search_param);
            $stmt_total->execute();
            $total_result = $stmt_total->get_result();

        } else {

            $total_sql = "SELECT COUNT(*) as total FROM posts";
            $total_result = $conn->query($total_sql);
        }

        $total_row = $total_result->fetch_assoc();
        $total_posts = $total_row['total'];

        $total_pages = ceil($total_posts / $limit);

        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? "active" : "";
            echo "<a class='$active' href='?page=$i&search=" . urlencode($search) . "'>$i</a>";
        }
        ?>
    </div>

    <!--  LOGOUT  -->
    <div style="margin-top: 20px; text-align: center;">
        <a class="btn" href="../Security_enhancements/logout.php">Logout</a>
    </div>

</div>

</body>
</html>
