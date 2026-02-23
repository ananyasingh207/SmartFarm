<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../src/Exception.php';
require __DIR__ . '/../src/PHPMailer.php';
require __DIR__ . '/../src/SMTP.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/env_loader.php';
loadEnv(__DIR__ . '/../.env');

session_start();

$conn = Database::getInstance()->getConnection();

// Get form type
$type = $_POST['type'];

if ($type === 'register') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    $name = htmlspecialchars($name);
    if (!preg_match("/^[a-zA-Z\s'-]+$/", $name)) {
    header("Location: login.php?form=register&error=Invalid+name+format&type=register");
    exit();
    }

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword) || empty($role)) {
        header("Location: login.php?form=register&error=Please+fill+all+fields&type=register");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: login.php?form=register&error=Invalid+email+format&type=register");
        exit();
    }

    if ($password !== $confirmPassword) {
        header("Location: login.php?form=register&error=Passwords+do+not+match&type=register");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: login.php?form=register&error=Password+must+be+at+least+6+characters&type=register");
        exit();
    }

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkUser = $stmt->get_result();

    if ($checkUser->num_rows > 0) {
        header("Location: login.php?form=register&error=Email+already+registered&type=register");
        exit();
    }

    //$conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);
    $stmt->execute();
    $userId = $stmt->insert_id;
    $stmt->close();

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['MAIL_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['MAIL_USERNAME'];
            $mail->Password   = $_ENV['MAIL_PASSWORD'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = intval($_ENV['MAIL_PORT']);

            $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME'] ?? 'Smart Irrigation');
            $mail->addAddress($email, $name); 

            $mail->isHTML(true);                             
            $mail->Subject = 'Welcome to Smart Irrigation';
            $mail->Body    = 'Hello, ' . $name . ',<br><br>Thank you for registering with Smart Irrigation. Your account has been successfully created.<br><br>Best regards,<br>Smart Irrigation Team';
            $mail->AltBody = 'Hello, ' . $name . ',\n\nThank you for registering with Smart Irrigation. Your account has been successfully created.\n\nBest regards,\nSmart Irrigation Team';

            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;

        // Redirect to role dashboard
        if ($role === 'farmer') {
            header("Location: ../farmer/farmer.php");
        } elseif ($role === 'manufacturer') {
            header("Location: ../manufacturer/manufacturer.php");
        } else {
            header("Location: ../service/service.php");
        }    
        exit();
}

if ($type === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.php?form=login&error=Please+enter+email+and+password&type=login");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Send the login email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->SMTPAuth = true;
                $mail->Username = $_ENV['MAIL_USERNAME'];
                $mail->Password = $_ENV['MAIL_PASSWORD'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = intval($_ENV['MAIL_PORT']);

                $mail->setFrom($_ENV['MAIL_USERNAME'], $_ENV['MAIL_FROM_NAME'] ?? 'Smart Irrigation');
                $mail->addAddress($email, $user['name']);

                $mail->isHTML(true);
                $mail->Subject = 'Login Notification';
                $mail->Body = 'Hello, ' . $user['name'] . ',<br><br>You have successfully logged in to Smart Irrigation.<br><br>Best regards,<br>Smart Irrigation Team';
                $mail->AltBody = 'Hello, ' . $user['name'] . ',\n\nYou have successfully logged in to Smart Irrigation.\n\nBest regards,\nSmart Irrigation Team';

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

            // Redirect to role dashboard
            if ($user['role'] === 'farmer') {
                header("Location: ../farmer/farmer.php");
            } elseif ($user['role'] === 'manufacturer') {
                header("Location: ../manufacturer/manufacturer.php");
            } else {
                header("Location: ../service/service.php");
            }
            exit();
        } else {
            header("Location: login.php?form=login&error=Wrong+password&type=login");
            exit();
        }
    } else {
        header("Location: login.php?form=login&error=No+user+found+with+that+email&type=login");
        exit();
    }
}
?>


