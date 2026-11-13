<?php
$message = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm  = $_POST["confirm"] ?? "";

    if ($fullname === "" || $username === "" || $email === "" || $password === "") {
        $message = "Please fill in all fields.";
        $type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $type = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $type = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
        $type = "error";
    } else {
        try {
            require "db.php";

            // Check if the username or email is already used
            $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $check->bind_param("ss", $username, $email);
            $check->execute();
            $check->store_result();

            if ($check->num_rows > 0) {
                $message = "Username or email already exists.";
                $type = "error";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, fullname, password) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $fullname, $hash);
                $stmt->execute();
                $message = "Signup successful. You can now log in.";
                $type = "success";
            }
        } catch (Throwable $e) {
            $message = "Database error: " . $e->getMessage();
            $type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CAT 2 Signup</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="card">
        <h2>CAT 2 Signup</h2>

        <?php if ($message !== ""): ?>
            <div class="msg <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <label for="fullname">Full name</label>
            <input type="text" id="fullname" name="fullname" required>

            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm">Confirm password</label>
            <input type="password" id="confirm" name="confirm" required>

            <button type="submit">Sign up</button>
        </form>

        <div class="link">Already have an account? <a href="login.php">Log in</a></div>
    </div>
</body>
</html>
