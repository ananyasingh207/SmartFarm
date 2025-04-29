<?php
session_start();
if (isset($_POST["submit"])) {
    $targetDir = "uploads/";
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    $fileName = basename($_FILES["file"]["name"]);
    $fileSize = $_FILES["file"]["size"];
    $fileTmpName = $_FILES["file"]["tmp_name"];
    $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedTypes = ["jpg", "pdf", "jpeg", "png"];
    
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = "Invalid File Type!";
        header("Location: chatbot.html");
        exit;
    }

    if ($fileSize > $maxFileSize) {
        $_SESSION['error'] = "File size exceeds 2MB!";
        header("Location: chatbot.html");
        exit;
    }

    $newFileName = uniqid("file_", true) . "." . $fileType;
    $targetFilePath = $targetDir . $newFileName;

    if (move_uploaded_file($fileTmpName, $targetFilePath)) {
        $_SESSION['success'] = "File uploaded successfully as $newFileName";
    } else {
        $_SESSION['error'] = "File not uploaded successfully.";
    }
    header("Location: chatbot.html");
    exit;
} else {
    $_SESSION['error'] = "No file chosen.";
    header("Location: chatbot.html");
    exit;
}
?>
