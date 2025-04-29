<?php
   session_start();
   require 'vendor/autoload.php';
   require 'config.php';
   use PHPMailer\PHPMailer\PHPMailer;
   use PHPMailer\PHPMailer\Exception;

   header('Content-Type: application/json');

   if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
       echo json_encode(['success' => false, 'error' => 'Invalid request method']);
       exit;
   }

   // Sanitize input
   $email = filter_var($_POST['reset-email'] ?? '', FILTER_SANITIZE_EMAIL);
   if (!$email) {
       echo json_encode(['success' => false, 'error' => 'Email is required']);
       exit;
   }

   // Check if email exists
   $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
   $stmt->execute([$email]);
   $user = $stmt->fetch(PDO::FETCH_ASSOC);

   if (!$user) {
       echo json_encode(['success' => false, 'error' => 'No account found with that email']);
       exit;
   }

   // Generate reset token
   $token = bin2hex(random_bytes(32));
   $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

   // Store token in database
   try {
       $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
       $stmt->execute([$user['id'], $token, $expires]);
   } catch (PDOException $e) {
       echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
       exit;
   }

   // Send reset email
   $reset_link = "http://localhost/SystemDevelopmentProject/reset_password.php?token=$token";
   $mail = new PHPMailer(true);
   try {
       $mail->isSMTP();
       $mail->Host = 'localhost';
       $mail->Port = 1025;
       $mail->SMTPAuth = false;
       $mail->setFrom('noreply@magicsole.com', 'Magic Sole');
       $mail->addAddress($email);
       $mail->isHTML(true);
       $mail->Subject = 'Password Reset Request';
       $mail->Body = "Click this link to reset your password: <a href='$reset_link'>$reset_link</a><br>This link expires in 1 hour.";
       $mail->send();
       echo json_encode(['success' => true]);
   } catch (Exception $e) {
       echo json_encode(['success' => false, 'error' => "Failed to send reset link: {$mail->ErrorInfo}"]);
   }

   exit;
   ?>