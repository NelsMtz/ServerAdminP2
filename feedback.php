<?php
session_start();
$message = "";
$type = "";
$rows = [];

try {
    require "db.php";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = trim($_POST["name"] ?? "");
        $text = trim($_POST["message"] ?? "");

        if ($name === "" || $text === "") {
            $message = "Please fill in all fields.";
            $type = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO feedback (name, message) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $text);
            $stmt->execute();
            $message = "Thanks. Your feedback was saved.";
            $type = "success";
        }
    }

    // Retrieve all saved feedback
    $result = $conn->query("SELECT id, name, message, submitted_at FROM feedback ORDER BY id DESC");
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
} catch (Throwable $e) {
    $message = "Database error: " . $e->getMessage();
    $type = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { align-items: flex-start; padding: 30px 0; }
        .card { max-width: 520px; }
        .entry { border-top: 1px solid #dde3ea; padding: 10px 0; font-size: 14px; }
        .entry small { color: #667085; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Feedback form</h2>

        <?php if ($message !== ""): ?>
            <div class="msg <?php echo $type; ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="feedback.php">
            <label for="name">Your name</label>
            <input type="text" id="name" name="name" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="4" required></textarea>

            <button type="submit">Send feedback</button>
        </form>

        <h3>Saved feedback (from the database)</h3>
        <?php if (count($rows) === 0): ?>
            <p>No feedback yet.</p>
        <?php endif; ?>
        <?php foreach ($rows as $r): ?>
            <div class="entry">
                <strong><?php echo htmlspecialchars($r["name"]); ?></strong>:
                <?php echo htmlspecialchars($r["message"]); ?><br>
                <small>#<?php echo (int)$r["id"]; ?> &middot; <?php echo htmlspecialchars($r["submitted_at"]); ?></small>
            </div>
        <?php endforeach; ?>

        <div class="link"><a href="welcome.php">Back</a> | <a href="login.php">Login</a></div>
    </div>
</body>
</html>
