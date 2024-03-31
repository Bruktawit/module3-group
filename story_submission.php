  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit New Story</title>
</head>
<body>
    <h1>Submit a New Story</h1>
    <form action="process_story_submission.php" method="post">
        <div>
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
        </div>
        <div>
            <label for="body">Body:</label>
            <textarea id="body" name="body" required></textarea>
        </div>
        <div>
            <label for="link">Link (optional):</label>
            <input type="url" id="link" name="link">
        </div>
        <!-- CSRF Token for security -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <input type="submit" value="Submit Story">
    </form>
</body>
</html>
