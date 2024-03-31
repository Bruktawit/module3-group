<?php
require_once 'db.php';
require_once 'session_manager.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$story_id = $_GET['story_id'] ?? ''; // Get story ID from query string

// Verify that the logged-in user is the author of the story
$stmt = $pdo->prepare("DELETE FROM stories WHERE id = ? AND user_id = ?");
$success = $stmt->execute([$story_id, $_SESSION['user_id']]);

if ($success) {
    echo "Story deleted successfully.";
    header('Location: news_index.php'); // Redirect after deletion
    exit;
} else {
    echo "Failed to delete story or you don't have permission.";
}

header('Location: news_index.php'); // Redirect to the stories list
exit;

?>
