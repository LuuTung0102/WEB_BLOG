<?php

include 'components/connect.php';

session_start();


if(isset($_POST['submit'])){

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $password = sha1($_POST['password']);
   $password = filter_var($password, FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
   $select_user->execute([$email, $password]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if($select_user->rowCount() > 0){
      if($row['active'] == 'yes'){
         $_SESSION['id'] = $row['id'];
         header('location:Home.php');
      }else{
         $message[] = 'Tài khoản của bạn đã bị khóa! Vui lòng liên hệ quản lý viên để được tư vấn';
      }
      if(isset($_SESSION['id'])){
         header('location: Home.php');
         exit();
      }
   }else{
      $message[] = 'Tên đăng nhập hoặc mật khẩu không chính xác!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   <link rel="stylesheet" href="./style.css">
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
<?php include 'components/header.php'; ?>
<?php if(isset($_SESSION['id']) && is_array($_SESSION['id'])){
         header('location: Home.php');
         exit();
      } ?>
    <section class="form_section">             
            <form action=""  method="post">
                <h3>Đăng nhập ngay</h3>
                <?php 
               if(isset($message)){ 
               if(!is_array($message)){
               $message = array($message);}
               ?>
               <div class="error">
               <?php foreach($message as $msg){ echo $msg; } ?>
               </div>
               <?php } ?>
                <input type="email" name="email" required placeholder="Nhập email của bạn" class="box"  maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                <input type="password" name="password" required placeholder="Nhập mật khẩu của bạn" class="box"  maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
                <input type="submit" value="Đăng nhập ngay" name="submit" class="btn">
                <p>Chưa có tài khoản? <a href="Register.php">Đăng ký ngay</a></p>
            </form>
    </section>
</body>
</html>