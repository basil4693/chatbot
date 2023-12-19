<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// ... rest of your code ...


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

include_once dirname(__FILE__) . "/../classes/DBConnection.php";

$database = new DBConnection();
$conn = $database->conn;

$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT id FROM users1 WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $sql = "SELECT id FROM users1 WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = trim($_POST["email"]);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"]))<8) {
        $password_err = "Password must have at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm the password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    if (empty($username_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
        $sql = "INSERT INTO users1 (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_email, $param_password);
            $param_username = $username;
            $param_email = $email;
            $param_password = md5($password);
            if (mysqli_stmt_execute($stmt)) {
                header("location: login.php");
                exit; // Terminate the script after redirection
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// No need to close the connection explicitly here

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-control {
            width: 300px;
        }
        .error {
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <h1>Register</h1>
                <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                        <span class="error"><?php echo $username_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                        <span class="error"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                        <span class="error"><?php echo $password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                        <span class="error"><?php echo $confirm_password_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <input type="reset" class="btn btn-default" value="Reset">
                    </div>
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
