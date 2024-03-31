<?php
require_once 'db.php';
require_once 'session_manager.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$story_id = $_GET['story_id'] ?? ''; // Get story ID from query string

// Fetch the story from the database
$stmt = $pdo->prepare("SELECT * FROM stories WHERE id = ? AND user_id = ?");
$stmt->execute([$story_id, $_SESSION['user_id']]);
$story = $stmt->fetch();

if (!$story) {
    echo "Story not found or you don't have permission to edit it.";
    exit;
}

// Form to edit the story
if ($_SERVER['REQUEST_METHOD'] == 'POST' && validate_csrf_token($_POST['csrf_token'])) {
    // Process form submission
    $title = $_POST['title'];
    $body = $_POST['body'];
    $link = $_POST['link'] ?? '';

    $updateStmt = $pdo->prepare("UPDATE stories SET title = ?, body = ?, link = ? WHERE id = ? AND user_id = ?");
    if ($updateStmt->execute([$title, $body, $link, $story_id, $_SESSION['user_id']])) {
        header('Location: news_index.php'); // Redirect after successful update
        exit;
    } else {
        echo "Error updating story.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Story</title>
</head>
<body>
    <h2>Edit Story</h2>
    <form method="post" action="">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div>
            <label for="title">Title:</label>
            <input type="text" name="title" value="<?php echo htmlspecialchars($story['title']); ?>" required>
        </div>
        <div>
            <label for="body">Body:</label>
            <textarea name="body" required><?php echo htmlspecialchars($story['body']); ?></textarea>
        </div>
        <div>
            <label for="link">Link (optional):</label>
            <input type="url" name="link" value="<?php echo htmlspecialchars($story['link']); ?>">
        </div>
        <input type="submit" value="Update Story">
    </form>
</body>
</html>
