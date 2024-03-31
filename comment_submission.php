<?php
require_once 'session_manager.php'; // Manages session start and CSRF token
require_once 'db.php'; // Database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$story_id = isset($_GET['story_id']) ? $_GET['story_id'] : die('Story ID not specified.');

// Include a form for submitting comments
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Comment</title>
</head>
<body>
    <h1>Submit a Comment</h1>
    <form action="process_comment_submission.php" method="post">
        <textarea name="comment" required></textarea>
        <input type="hidden" name="story_id" value="<?php echo htmlspecialchars($story_id); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" value="Submit Comment">
    </form>
</body>
</html>
