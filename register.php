<?php
require 'db.php';
require 'session_manager.php'; // The file where you've defined session start and CSRF functions

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        $message = 'CSRF token mismatch.';
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        if ($stmt->execute([$username, $passwordHash])) {
            $message = 'User registered successfully!';
            header('Location: login.php');
        } else {
            $message = 'Error registering user.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <h2>Register</h2>
    <form action="register.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Register">
    </form>
    <p><?php echo $message; ?></p>
    
</body>
</html>
