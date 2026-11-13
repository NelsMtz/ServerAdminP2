<?php
session_start();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $message = "Please enter your username and password.";
    } else {
        try {
            require "db.php";

            $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($password, $user["password"])) {
                session_regenerate_id(true);
                $_SESSION["user_id"]  = $user["id"];
                $_SESSION["username"] = $username;
                $_SESSION["fullname"] = $user["fullname"];
                header("Location: welcome.php");
                exit;
            } else {
                $message = "Invalid username or password.";
            }
        } catch (Throwable $e) {
            $message = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAT 2 Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h2>CAT 2 Login</h2>

        <?php if ($message !== ""): ?>
            <div class="msg error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Log in</button>
        </form>

        <div class="link">No account yet? <a href="signup.php">Sign up</a></div>
    </div>
</body>
</html>
