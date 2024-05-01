<?php
include("db.php");
session_start();
if (isset($_POST["submit"])) {
    $file = $_FILES["image"];
    $fileName = $file["name"];
    $fileSize = $file["size"];
    $fileTemp = $file["tmp_name"];
    $fileErr = $file["error"];
    $fileTypetmp = explode(".", $fileName);
    $fileType = strtolower(end($fileTypetmp));

    if ($fileErr === 0) {
        if ($fileSize < 1500000) {
            $_SESSION["type"] = $fileType;
            $fileDest =  "profile" . $_SESSION['id'] . '.' . $fileType;
            move_uploaded_file($fileTemp, "imgs/" . $fileDest);
            $sql =  "UPDATE images SET status = 0, type = '$fileType' WHERE userid = $_SESSION[id]";
            mysqli_query($conn, $sql);
            header("Location: index.php?success");
            exit();
        } else {
            echo "The image is too big! ";
        }
    } else {
        echo "There was an error uploading your Image!";
    }
}
