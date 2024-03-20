<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
} else {
    header('location:Login.php');
};

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);
    $password = sha1($_POST['password']);
    $password = filter_var($password, FILTER_SANITIZE_STRING);
    $cpassword = sha1($_POST['cpassword']);
    $cpassword = filter_var($cpassword, FILTER_SANITIZE_STRING);
    $old_password = sha1($_POST['old_password']);
    $old_password = filter_var($old_password, FILTER_SANITIZE_STRING);

    $avatar = $_FILES['avatar']['name'];
    $avatar = filter_var($avatar, FILTER_SANITIZE_STRING);
    $avatar_size = $_FILES['avatar']['size'];
    $avatar_tmp_name = $_FILES['avatar']['tmp_name'];
    $avatar_folder = './avatar/'.$avatar;

    $select_avatar = $conn->prepare("SELECT * FROM `users` WHERE avatar = ? AND id != ?");
    $select_avatar->execute([$avatar, $id]);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $select_user->execute([$id]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($old_password != $row['password']) {
        $message[] = 'Mật khẩu cũ không đúng!';
    } else {
        if (isset($avatar)) {
            if ($select_avatar->rowCount() > 0 AND $avatar != '') {
                $message[] = 'Tên avatar đã tồn tại!';
            } else if ($avatar_size > 2000000) {
                $message[] = 'Kích thước avatar quá lớn!';
            } else {
                move_uploaded_file($avatar_tmp_name, $avatar_folder); 
            }
        } else {
            $avatar = $row['avatar'];
        }

        if ($password != $cpassword) {
            $message[] = 'Mật khẩu không khớp!';
        } else {
            $stmt = $conn->prepare("UPDATE `users` SET `username` = ?, `password` = ?, `avatar` = ? WHERE `id` = ?");
            $stmt->execute([$username, $password, $avatar, $id]);
            $_SESSION['username'] = $username;
            $_SESSION['avatar'] = $avatar;
            header('location:Home.php');
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
   <title>Blog Website</title>
   <link rel="stylesheet" href="./style.css">
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
<?php include 'components/header.php'; ?>
<section class="form_section">
    <form action="Update.php" method="post" enctype="multipart/form-data">
        <h3>Chỉnh sửa tài khoản</h3>
        <?php 
            if(isset($message)){ 
                if(!is_array($message)){
                    $message = array($message);
                }
        ?>
        <div class="error">
            <?php foreach($message as $msg){ echo $msg; } ?>
        </div>
        <?php } ?>
        <input type="text" name="username" required placeholder="Nhập họ và tên" class="box" maxlength="50" value="<?php echo $row['username']; ?>">
        <input type="email" name="email" required placeholder="Nhập gmail" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')" value="<?php echo $row['email']; ?>">
        <input type="password" name="old_password" placeholder="Nhập mật khẩu cũ" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="password"  placeholder="Nhập mật khẩu mới" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="password" name="cpassword" placeholder="Nhập lại mật khẩu mới" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">

        <input type="file" name="avatar" class="box" accept="avatar/jpg, avatar/jpeg, avatar/png, avatar/webp">
        <input type="submit" value="Cập nhật" name="submit" class="btn">
    </form>     
</section>

</body>
</html>