<?php
include "db.php";
session_start();
if (empty($_SESSION["user"])) {
    exit(header("Location: login.php"));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/home.css?v=<?php echo time(); ?>">
    <title>Home</title>
</head>

<body>

    <div class="nav">
        <span>Max speed</span>
        <form method="post">
            <input type="submit" name="logout" value="logout">
        </form>

    </div>
    <div class="container">
        <div class="box">
            <div class="info">
                <?php
                $sql = "SELECT * FROM images WHERE id = $_SESSION[id]";
                $res = mysqli_query($conn, $sql);
                if (mysqli_num_rows($res) > 0) {
                    $row = mysqli_fetch_assoc($res);
                    if ($row["status"] == 0) {
                ?>
                        <img class="profile" src="imgs/profile<?php echo $row['id'] . '.' . $row['type']; ?>" alt="profile">
                    <?php
                    } else {
                    ?>
                        <img class="profile" src="imgs/profileDefault.png" alt="profile">
                <?php
                    }
                }
                ?>
                <div>
                    <p>Hello <?php echo "<span>$_SESSION[user]</span>" ?>, you are Logged in.</p>
                    <p>Created on <?php echo "<span>$_SESSION[reg]</span>" ?>.</p>
                </div>
            </div>
            <?php
            if ($_SESSION["id"] == 1) {
                $sql = "SELECT COUNT(*) AS count FROM users WHERE id > 1";
                $res = mysqli_query($conn, $sql);
                $row_data = mysqli_fetch_assoc($res);
                $count = $row_data["count"];
            ?>
                <form method="post">
                    <span>Clear users (Total <?php echo $count ?>): </span>
                    <button name="clear">Clear</button>
                </form>
                <span class="admin">Admin</span>
            <?php
            }
            ?>
        </div>
        <div class="box">
            <label for="new">Change username: </label>
            <div>
                <form method="post">
                    <input type="text" name="newuser" id="new">
                    <button name="changeus">Change</button>
                </form>
            </div>
        </div>
        <div class="box">
            <label for="gm">Change email: </label>
            <div>
                <form method="post">
                    <input type="email" name="newemail" id="gm" value="<?php echo $_SESSION['email'] ?>">
                    <button name="changeem">Change</button>
                </form>
            </div>
        </div>
        <?php
        if ($_SESSION["id"] == 1 && $count > 0) {
            $sql = "SELECT user, reg AS oldus 
                FROM users WHERE id > 1 ORDER BY reg ASC LIMIT 1;";
            $res = mysqli_query($conn, $sql);
            $row_data = mysqli_fetch_assoc($res);
            $oldreg = $row_data["oldus"];
            $oldus = $row_data["user"];

            $sql = "SELECT user, reg AS newus 
                FROM users WHERE id > 1 ORDER BY reg DESC LIMIT 1;";
            $res = mysqli_query($conn, $sql);
            $row_data = mysqli_fetch_assoc($res);
            $newreg = $row_data["newus"];
            $newus = $row_data["user"];
        ?>
            <div class="box">
                <div>Newest user: <span><?php echo $newus; ?></span>, Joined on <span><?php echo $newreg; ?></span></div>
                <br>
                <div>Oldest user: <span><?php echo $oldus; ?></span>, Joined on <span><?php echo $oldreg; ?></span></div>
            </div>
        <?php
        }
        ?>
        <div class="box">
            <form method="post" action="upload.php" enctype="multipart/form-data">
                <label for="img">upload your profile picture:</label>
                <div>
                    <input type="file" accept="image/*" name="image" id="img" required>
                    <button type="submit" name="submit">upload</button>
                </div>
                <div>

                </div>
            </form>
        </div>
    </div>
    <script>
        function shake(tag) {
            tag.classList.add("shake");
            setTimeout(() => {
                tag.classList.remove("shake");
            }, 1000);
        }
        let imginpt = document.getElementById("img")
        imginpt.onchange = function(e) {
            let img = document.createElement("img");
            let file = e.target.files[0];
            let url = URL.createObjectURL(file);
            img.src = url;
            img.alt = "profile";
            img.classList.add("preview");
            imginpt.parentElement.nextElementSibling.innerHTML = "<h2>Preview:</h2>";
            imginpt.parentElement.nextElementSibling.appendChild(img);
        };
    </script>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_REQUEST["logout"])) {
        session_unset();
        session_destroy();
        exit(header("Location: login.php"));
    }
    if (isset($_REQUEST["changeus"]) && !empty($_REQUEST["newuser"])) {
        $sql = "UPDATE users SET user = '$_REQUEST[newuser]' WHERE id = $_SESSION[id]";
        mysqli_query($conn, $sql);
        $sql = "SELECT *
                FROM users 
                WHERE id = \"$_SESSION[id]\"";
        $res = mysqli_query($conn, $sql);
        $row_data = mysqli_fetch_assoc($res);
        $_SESSION["user"] = $row_data["user"];
        $_SESSION["email"] = $row_data["email"];
        $_SESSION["password"] = $row_data["password"];
        $_SESSION["reg"] = $row_data["reg"];
        $_SESSION["id"] = $row_data["id"];
        echo "<script>window.location.reload();</script>";
    }
    if (isset($_REQUEST["changeem"]) && !empty($_REQUEST["newemail"])) {
        $sql = "SELECT id
                        FROM users 
                        WHERE email = \"$_REQUEST[newemail]\"";
        $res = mysqli_query($conn, $sql);
        if (mysqli_num_rows($res) === 0) {
            $sql = "UPDATE users SET email = '$_REQUEST[newemail]' WHERE id = $_SESSION[id]";
            mysqli_query($conn, $sql);
            $sql = "SELECT *
                FROM users 
                WHERE id = \"$_SESSION[id]\"";
            $res = mysqli_query($conn, $sql);
            $row_data = mysqli_fetch_assoc($res);
            $_SESSION["user"] = $row_data["user"];
            $_SESSION["email"] = $row_data["email"];
            $_SESSION["password"] = $row_data["password"];
            $_SESSION["reg"] = $row_data["reg"];
            $_SESSION["id"] = $row_data["id"];
            echo "<script>window.location.reload();</script>";
        } else {
?>
            <script>
                shake(document.getElementById("gm"));
            </script>
<?php
        }
    }
    if (isset($_REQUEST["clear"]) && $count >= 1) {
        $user = $_SESSION["user"];
        $email = $_SESSION["email"];
        $password = $_SESSION["password"];
        $reg = $_SESSION["reg"];

        $sql = "TRUNCATE TABLE users;
                TRUNCATE TABLE images;";
        mysqli_query($conn, $sql);
        $sql = "INSERT INTO users VALUES (1, '$user', '$email', '$password', '$reg')";
        $res = mysqli_query($conn, $sql);

        $sql = "INSERT INTO images (userid, status) VALUES (1,1)";
        $res = mysqli_query($conn, $sql);
        echo "<script>window.location.reload();</script>";
    }
}
?>