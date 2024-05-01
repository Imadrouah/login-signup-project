<?php
include "db.php";
session_start();
if (!empty($_SESSION["user"])) {
    exit(header("Location: index.php"));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/second.css?v=<?php echo time(); ?>">
    <title>Sign in</title>
</head>

<body>
    <form method="post">
        <div class="container">
            <label for="user">Name:</label>
            <input type="text" name="user" required id="user">
            <label for="email">Email:</label>
            <input type="text" name="email" required id="email">
            <label for="pass">Password: </label>
            <input type="text" name="pass" required id="pass">
            <label for="cpass">Confirm password: </label>
            <input type="text" name="cpass" required id="cpass">
            <input type="submit" name="log" value="Sign in" id="sub">
            <div class="option">Already have an account? <a href="login.php">Log in</a></div>
        </div>
    </form>
    <script>
        function shake(tag) {
            tag.classList.add("shake");
            setTimeout(() => {
                tag.classList.remove("shake");
            }, 1000);
        }
    </script>
</body>

</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_REQUEST["log"])) {
        $sql = "SELECT *
                        FROM users 
                        WHERE email = \"$_REQUEST[email]\"";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) === 0) {
            $pass = filter_input(INPUT_POST, "pass", FILTER_SANITIZE_SPECIAL_CHARS);
            $cpass = filter_input(INPUT_POST, "cpass", FILTER_SANITIZE_SPECIAL_CHARS);
            if ($pass === $cpass) {
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
                $hashed = password_hash($pass, PASSWORD_DEFAULT);
                $user = filter_input(INPUT_POST, "user", FILTER_SANITIZE_SPECIAL_CHARS);
                $sql = "INSERT INTO users (user, email, password) VALUES ('$user', '$email', '$hashed')";
                try {
                    $res = mysqli_query($conn, $sql);
                } catch (mysqli_sql_exception) {
                }
                $sql = "SELECT *
                        FROM users 
                        WHERE email = \"$_REQUEST[email]\"";
                $res = mysqli_query($conn, $sql);
                if (mysqli_num_rows($res) === 1) {
                    $row_data = mysqli_fetch_assoc($res);
                    $_SESSION["user"] = $row_data["user"];
                    $_SESSION["email"] = $row_data["email"];
                    $_SESSION["password"] = $row_data["password"];
                    $_SESSION["reg"] = $row_data["reg"];
                    $_SESSION["id"] = $row_data["id"];
                    $sql = "INSERT INTO images (userid, status) VALUES ($_SESSION[id], 1)";
                    mysqli_query($conn, $sql);
                    exit(header("Location: index.php"));
                }
            } else {
?>
                <script>
                    shake(document.getElementById("cpass"));
                    document.getElementById("user").value = `<?php echo $_REQUEST["user"]; ?>`;
                    document.getElementById("email").value = `<?php echo $_REQUEST["email"]; ?>`;
                    document.getElementById("pass").value = `<?php echo $_REQUEST["pass"]; ?>`;
                    document.getElementById("cpass").value = `<?php echo $_REQUEST["cpass"]; ?>`;
                </script>
            <?php
            }
        } else {
            ?>
            <script>
                document.getElementById("user").value = `<?php echo $_REQUEST["user"]; ?>`;
                document.getElementById("email").value = `<?php echo $_REQUEST["email"]; ?>`;
                document.getElementById("pass").value = `<?php echo $_REQUEST["pass"]; ?>`;
                document.getElementById("cpass").value = `<?php echo $_REQUEST["cpass"]; ?>`;
                shake(document.getElementById("email"));
            </script>
        <?php
        }
        ?>
<?php
    }
}
?>