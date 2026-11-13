<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h2>Login successful</h2>
        <div class="msg success">
            Welcome, <?php echo htmlspecialchars($_SESSION["fullname"]); ?>
            (<?php echo htmlspecialchars($_SESSION["username"]); ?>).
        </div>
        <div class="link"><a href="feedback.php">Go to feedback form</a></div>
        <div class="link"><a href="logout.php">Log out</a></div>
    </div>
</body>
</html>
