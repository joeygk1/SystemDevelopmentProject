<?php
   session_start();
   require 'config.php';

   $error = '';
   $message = '';

   if (isset($_GET['token'])) {
       $token = $_GET['token'];
       $stmt = $db->prepare("SELECT user_id, expires FROM password_resets WHERE token = ?");
       $stmt->execute([$token]);
       $reset = $stmt->fetch(PDO::FETCH_ASSOC);

       if ($reset && strtotime($reset['expires']) > time()) {
           if ($_SERVER['REQUEST_METHOD'] === 'POST') {
               $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
               $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
               $stmt->execute([$password, $reset['user_id']]);
               $stmt = $db->prepare("DELETE FROM password_resets WHERE token = ?");
               $stmt->execute([$token]);
               $message = 'Password updated successfully. <a href="login.php">Login</a>';
           }
       } else {
           $error = 'Invalid or expired token';
       }
   } else {
       $error = 'No token provided';
   }
   ?>
   <!DOCTYPE html>
   <html lang="en">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Reset Password - Magic Sole</title>
       <style>
           body {
               font-family: 'Poppins', sans-serif;
               background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
               display: flex;
               justify-content: center;
               align-items: center;
               min-height: 100vh;
               margin: 0;
           }
           .container {
               background: #fff;
               padding: 30px;
               border-radius: 15px;
               box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
               max-width: 400px;
               width: 90%;
               text-align: center;
           }
           h2 {
               font-size: 2rem;
               margin-bottom: 20px;
               color: #1a1a1a;
           }
           label {
               display: block;
               font-size: 1.1rem;
               color: #333;
               margin-bottom: 5px;
           }
           input {
               width: 100%;
               padding: 10px;
               font-size: 1rem;
               border: 1px solid #ccc;
               border-radius: 5px;
               margin-bottom: 15px;
           }
           input:focus {
               border: 1px solid #d4af37;
               outline: none;
           }
           button {
               background: #f9c303;
               color: #1a1a1a;
               padding: 10px;
               border: none;
               border-radius: 5px;
               font-size: 1rem;
               cursor: pointer;
               width: 100%;
           }
           button:hover {
               background: #d4af37;
           }
           .error {
               color: #e74c3c;
               font-size: 0.9rem;
               margin-bottom: 15px;
           }
           .success {
               color: #2ecc71;
               font-size: 0.9rem;
               margin-bottom: 15px;
           }
           a {
               color: #d4af37;
               text-decoration: none;
           }
           a:hover {
               text-decoration: underline;
           }
       </style>
   </head>
   <body>
       <div class="container">
           <h2>Reset Password</h2>
           <?php if ($error) { ?>
               <p class="error"><?php echo htmlspecialchars($error); ?></p>
           <?php } ?>
           <?php if ($message) { ?>
               <p class="success"><?php echo $message; ?></p>
           <?php } ?>
           <?php if (!$error && !$message) { ?>
               <form method="POST">
                   <label for="password">New Password</label>
                   <input type="password" id="password" name="password" placeholder="Enter new password" required>
                   <button type="submit">Reset Password</button>
               </form>
           <?php } ?>
       </div>
   </body>
   </html>