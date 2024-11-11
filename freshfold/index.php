<?php
session_start();
include('config.php');

$errors = [
    "loginfailed" => "Please check your username and password.",
];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT userID, password, role FROM User WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userID, $hashedPassword, $userRole);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION["userID"] = $userID;
        $_SESSION["role"] = $userRole;

        $stmt->close();

        switch ($userRole) {
            case "admin":
                header("Location: admin_dashboard.php");
                break;
            case "employee":
                header("Location: employee_dashboard.php");
                break;
            default:
                header("Location: user_dashboard.php");
                break;
        }

        exit();
    } else {
        $errorCode = "loginfailed";
        header("Location: index.php?error=$errorCode");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WashFold | Login</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <header>Login</header>
        </div>

        <form action="index.php" method="post">
            <div class="input-container">
                <div class="input-field">
                    <label for="user">Username</label>
                    <input type="text" name="username" id="user" required>
                </div>

                <div class="input-field">
                    <label for="pass">Password</label>
                    <input type="password" name="password" id="pass" required>
                </div>

                <?php
                if (isset($_GET['error']) && isset($errors[$_GET['error']])) {
                    echo '<div class="error-message">' . $errors[$_GET['error']] . '</div>';
                }
                ?>

                <div class="input-field">
                    <input type="submit" class="submit" name="submit" value="Login">
                </div>
                <span>
                    <a href="registration.php">Register</a>
                </span>
            </div>
        </form>
    </div>
</body>

</html>