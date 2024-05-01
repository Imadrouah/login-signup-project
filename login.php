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
    <title>Log in</title>
</head>

<body>
    <form method="post">
        <div class="container">
            <label for="name">Email:</label>
            <input type="text" name="email" required id="email">
            <label for="pass">Password: </label>
            <input type="text" name="pass" required id="pass">
            <input type="submit" name="log" value="Log in" id="sub">
            <div class="option">Don't have an account? <a href="signin.php">Create one</a></div>
        </div>
    </form>
    <script>
        let sub = document.getElementById("sub");
        let submitForm = true;

        function shake(tag) {
            tag.classList.add("shake");
            setTimeout(() => {
                tag.classList.remove("shake");
            }, 1000);


            sub.addEventListener("click", function() {
                submitForm = true;
            });

            document.forms[0].addEventListener("submit", (event) => {
                if (!submitForm) {
                    event.preventDefault();
                }
            });
        }
    </script>
</body>

</html>
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_REQUEST["log"])) {
?>
        <script>
            document.getElementById("email").value = `<?php echo $_REQUEST["email"]; ?>`;
            document.getElementById("pass").value = `<?php echo $_REQUEST["pass"]; ?>`;
        </script>
        <?php
        $sql = "SELECT *
                FROM users 
                WHERE email = \"$_REQUEST[email]\"";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) === 1) {
            $row_data = mysqli_fetch_assoc($res);
            if (password_verify($_REQUEST["pass"], $row_data["password"])) {
                $_SESSION["user"] = $row_data["user"];
                $_SESSION["email"] = $row_data["email"];
                $_SESSION["password"] = $row_data["password"];
                $_SESSION["reg"] = $row_data["reg"];
                $_SESSION["id"] = $row_data["id"];
                exit(header("Location: index.php"));
            } else {
        ?>
                <script>
                    submitForm = false;
                    shake(document.getElementById("pass"));
                </script>;
            <?php
            }
        } else {
            ?>
            <script>
                submitForm = false;
                shake(document.getElementById("email"));
            </script>
<?php
        }
    }
}
?>