<?php
require_once 'db.php';
require_once 'session_manager.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : '';

// Fetch the comment from the database
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($comment_id)) {
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE id = ?");
    $stmt->execute([$comment_id]);
    $comment = $stmt->fetch();

    // Verify if comment exists and belongs to the logged-in user
    if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
        echo "Comment not found or you don't have permission to edit it.";
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // CSRF token check for security
    if (!validate_csrf_token($_POST['csrf_token'])) {
        die('CSRF token validation failed.');
    }

    $comment_id = $_POST['comment_id'];
    $comment_text = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // Update the comment in the database
    $stmt = $pdo->prepare("UPDATE comments SET comment = ? WHERE id = ? AND user_id = ?");
    if ($stmt->execute([$comment_text, $comment_id, $_SESSION['user_id']])) {
        header('Location: news_index.php'); // Redirect after successful update
        exit;
    } else {
        echo "Failed to update comment.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Comment</title>
</head>
<body>
    <h1>Edit Comment</h1>
    <?php if ($_SERVER['REQUEST_METHOD'] == 'GET'): ?>
    <form method="post" action="edit_comment.php">
        <textarea name="comment" required><?php echo htmlspecialchars($comment['comment']); ?></textarea>
        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment_id); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" value="Update Comment">
    </form>
    <?php endif; ?>
</body>
</html>
