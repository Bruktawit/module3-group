<?php
require_once 'db.php';
require_once 'session_manager.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}



$comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : '';

// Verify ownership and then delete the comment
$stmt = $pdo->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
$success = $stmt->execute([$comment_id, $_SESSION['user_id']]);

if ($success) {
    echo "Comment deleted successfully.";
    header('Location: news_index.php'); // Redirect after successful deletion
    exit;
} else {
    echo "Failed to delete comment or you don't have permission.";
}
