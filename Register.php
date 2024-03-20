<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
} else {
    $id = '';
};
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
   
    $password = $_POST['password'];
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', $password)) {
        $message[] = 'Mật khẩu phải có ít nhất 1 chữ hoa, 1 chữ thường, 1 số và ít nhất 8 ký tự!';
    } else {
        $password = sha1($password);
        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $cpassword = sha1($_POST['cpassword']);
        $cpassword = filter_var($cpassword, FILTER_SANITIZE_STRING);

        $avatar = $_FILES['avatar']['name'];
        $avatar = filter_var($avatar, FILTER_SANITIZE_STRING);
        $avatar_size = $_FILES['avatar']['size'];
        $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
        $avatar_folder = './avatar/'.$avatar;

        $select_avatar = $conn->prepare("SELECT * FROM `users` WHERE avatar = ? AND id = ?");
        $select_avatar->execute([$avatar, $id]);

        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if (isset($avatar)) {
            if ($select_avatar->rowCount() > 0 AND $avatar != '') {
                $message[] = 'Tên avatar đã tồn tại!';
            } else if ($avatar_size > 2000000) {
                $message[] = 'Kích thước avatar quá lớn!';
            } else {
                move_uploaded_file($avatar_tmp_name, $avatar_folder); 
            }
        } else {
            $avatar = '';
        }

        if($select_avatar->rowCount() > 0 AND $avatar != ''){
            $message[] = 'Vui lòng đổi tên hình ảnh của bạn!';
        }else if($select_user->rowCount() > 0) {
            $message[] = 'Email đã được sử dụng!';
        } else {
            if ($password != $cpassword) {
                $message[] = 'Mật khẩu không khớp!';
            } else {
                $stmt = $conn->prepare("INSERT INTO `users` (`username`, `email`, `password`, `avatar`, `role`, `active`) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password, $avatar, 'user', 'yes']);
                $stmt = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ?");
                $stmt->execute([$email, $password]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {
                    $_SESSION['id'] = $row['id'];
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['avatar'] = $row['avatar'];
                    header('location:Home.php');
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <link rel="stylesheet" href="./style.css">
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
<?php include 'components/header.php'; ?>
    <section class="form_section">
        <form action="Register.php" method="post" enctype="multipart/form-data">
           <h3>Đăng ký ngay</h3>
           <?php 
               if(isset($message)){ 
               if(!is_array($message)){
               $message = array($message);}
               ?>
               <div class="error">
               <?php foreach($message as $msg){ echo $msg; } ?>
               </div>
               <?php } ?>
           <input type="text" name="username" required placeholder="Nhập họ và tên" class="box" maxlength="50">
           <input type="email" name="email" required placeholder="Nhập gmail" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
           <input type="password" name="password" required placeholder="Nhập mật khẩu" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
           <input type="password" name="cpassword" required placeholder="Nhập lại mật khẩu" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
           <input type="file" name="avatar" class="box" accept="avatar/jpg, avatar/jpeg, avatar/png, avatar/webp">
          <input type="submit" value="Đăng ký ngay" name="submit" class="btn" >
           <p>Đã sở hữu tài khoản <a href="Login.php">Đăng nhập</a></p>
        </form>     
     </section>    
</body>
</html> 