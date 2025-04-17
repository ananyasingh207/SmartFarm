<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

session_start();

// Database connection (update credentials as needed)
$conn = new mysqli("localhost", "root", "", "irrigation");

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error); // Log error to server logs
    header("Location: login.php?form=login&error=Something+went+wrong&type=login");
    exit();
}

// Get form type
$type = $_POST['type'];

if ($type === 'register') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $role = $_POST['role'];

    // Simple validations
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
    $checkUser = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($checkUser->num_rows > 0) {
        header("Location: login.php?form=register&error=Email+already+registered&type=register");
        exit();
    }

    // Insert new user
    $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();                                  
        $mail->Host       = 'smtp.gmail.com';            
        $mail->SMTPAuth   = true;                        
        $mail->Username   = 'singhananyaa1518@gmail.com';  
        $mail->Password   = 'bnld osjh bdww chye';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
        $mail->Port       = 587;                        

        $mail->setFrom('singhananyaa1518@gmail.com', 'Smart Irrigation');
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
    $_SESSION['name'] = $name;
    $_SESSION['role'] = $role;

    // Redirect to role dashboard
    if ($role === 'farmer') {
        header("Location: farmer.php");
    } elseif ($role === 'manufacturer') {
        header("Location: manufacturer.php");
    } else {
        header("Location: service.php");
    }    
    exit();
}

if ($type === 'login') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Basic checks
    if (empty($email) || empty($password)) {
        header("Location: login.php?form=login&error=Please+enter+email+and+password&type=login");
        exit();
    }

    // Check user
    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$password'");
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
    
        // Send the login email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();                                  
            $mail->Host       = 'smtp.gmail.com';            
            $mail->SMTPAuth   = true;                        
            $mail->Username   = 'singhananyaa1518@gmail.com';  
            $mail->Password   = 'bnld osjh bdww chye';    
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;                        
    
            $mail->setFrom('singhananyaa1518l@gmail.com', 'Smart Irrigation');
            $mail->addAddress($email, $user['name']); 
    
            $mail->isHTML(true);                             
            $mail->Subject = 'Login Notification';
            $mail->Body    = 'Hello, ' . $user['name'] . ',<br><br>You have successfully logged in to Smart Irrigation.<br><br>Best regards,<br>Smart Irrigation Team';
            $mail->AltBody = 'Hello, ' . $user['name'] . ',\n\nYou have successfully logged in to Smart Irrigation.\n\nBest regards,\nSmart Irrigation Team';
    
            $mail->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    
        // Redirect to role dashboard
        if ($user['role'] === 'farmer') {
            header("Location: farmer.php");
        } elseif ($user['role'] === 'manufacturer') {
            header("Location: manufacturer.php");
        } else {
            header("Location: service.php");
        }        
        exit();
    }    
}
?>
