<?php
require_once 'db.php';
require_once 'session_manager.php';

echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>News Site</title>";
echo "<style>";
echo "    .story, .comment {";
echo "        border: 1px solid #ccc;";
echo "        padding: 10px;";
echo "        margin-bottom: 20px;";
echo "        border-radius: 5px;";
echo "    }";
echo "    .story h2, .story button {";
echo "        display: inline-block;";
echo "    }";
echo "    .story button {";
echo "        margin-left: 10px;";
echo "    }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>Welcome to Our News Site</h1>";

if (isset($_SESSION['user_id'])) {
    echo "<p>Hello, " . htmlspecialchars($_SESSION['username']) . "!</p>";
    echo "<p>[<a href='story_submission.php'>Submit a New Story</a>]   [<a href='logout.php'>Logout</a>]</p>";
} else {
    echo "<p><a href='login.php'>Login</a> or <a href='register.php'>Register</a></p>";
}

$stmt = $pdo->query("SELECT stories.id, stories.title, stories.likes, stories.body, stories.link, stories.user_id, users.username FROM stories JOIN users ON stories.user_id = users.id ORDER BY stories.id DESC");
while ($story = $stmt->fetch()) {
    echo "<div class='story'>";
    echo "<h2>" . htmlspecialchars($story['title']) . " (<span id='likes-count-" . $story['id'] . "'>" . htmlspecialchars($story['likes']) . "</span> likes)</h2>";
    echo "<button onclick='likeStory(" . $story['id'] . ")'>Like</button>";
    echo "<p>" . nl2br(htmlspecialchars($story['body'])) . "</p>";
    if (!empty($story['link'])) {
        echo "<p><a href='" . htmlspecialchars($story['link']) . "' target='_blank'>Read more</a></p>";
    }
    echo "<p>Posted by: " . htmlspecialchars($story['username']) . "</p>";

    if (isset($_SESSION['user_id'])) {
        echo "<p><a href='comment_submission.php?story_id=" . $story['id'] . "'>Comment</a></p>";
    }

    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $story['user_id']) {
        echo "<p><a href='edit_story.php?story_id=" . $story['id'] . "'>Edit</a> | <a href='delete_story.php?story_id=" . $story['id'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a></p>";
    }

    $commentStmt = $pdo->prepare("SELECT comments.id, comments.comment, comments.user_id, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE story_id = ? ORDER BY comments.id DESC");
    $commentStmt->execute([$story['id']]);
    while ($comment = $commentStmt->fetch()) {
        echo "<div class='comment'>";
        echo "<strong>" . htmlspecialchars($comment['username']) . " says:</strong> " . nl2br(htmlspecialchars($comment['comment']));
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
            echo " <a href='edit_comment.php?comment_id=" . $comment['id'] . "'>Edit</a> | <a href='delete_comment.php?comment_id=" . $comment['id'] . "' onclick='return confirm(\"Are you sure?\");'>Delete</a>";
        }
        echo "</div>"; // Close comment div
    }

    echo "</div>"; // Close story div
}

echo "<script>";
echo "function likeStory(storyId) {";
echo "    const formData = new FormData();";
echo "    formData.append('story_id', storyId);";
echo "    fetch('like_story.php', {";
echo "        method: 'POST',";
echo "        body: formData";
echo "    })";
echo "    .then(response => response.json())";
echo "    .then(data => {";
echo "        if(data.success) {";
echo "            document.getElementById('likes-count-' + storyId).textContent = data.newLikesCount;";
echo "            alert('Story liked!');";
echo "        } else {";
echo "            alert(data.message);";
echo "        }";
echo "    })";
echo "    .catch(error => console.error('Error:', error));";
echo "}";
echo "</script>";
echo "</body>";
echo "</html>";
