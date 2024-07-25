<?php
session_start();
require_once 'log.php';
require_once 'db.php';

$error = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    ServerLogger::log($_POST);
    $username = $_POST["username"];
    $password = $_POST["password"];
    ServerLogger::log($username, $password);

    if (login($username, $password)) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="global.css" rel="stylesheet" />
    <link href="login.css" rel="stylesheet" />
</head>

<body>
    <nav>
        <a href="/index.html">Back to Home</a>
    </nav>
    <div class="login-container">
        <h2>Login</h2>

        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </div>
</body>

</html>