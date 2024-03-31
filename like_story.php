<?php
require_once 'db.php';
require_once 'session_manager.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$story_id = $_POST['story_id'] ?? '';
$user_id = $_SESSION['user_id'];

// Attempt to insert the like
$stmt = $pdo->prepare("INSERT IGNORE INTO story_likes (story_id, user_id) VALUES (?, ?)");
$stmt->execute([$story_id, $user_id]);

if ($stmt->rowCount() > 0) {
    // Liked successfully, update the likes count
    $pdo->prepare("UPDATE stories SET likes = likes + 1 WHERE id = ?")->execute([$story_id]);

    // Fetch the new like count
    $likeCount = $pdo->prepare("SELECT likes FROM stories WHERE id = ?");
    $likeCount->execute([$story_id]);
    $newLikesCount = $likeCount->fetchColumn();

    echo json_encode(['success' => true, 'message' => 'Liked successfully', 'newLikesCount' => $newLikesCount]);
} else {
    // The like was ignored, indicating the user has already liked this story
    echo json_encode(['success' => false, 'message' => 'Already liked this story']);
}
?>
