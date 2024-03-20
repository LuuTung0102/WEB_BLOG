<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   header('location:../Login.php');
};
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../style2.css">
</head>
<body>
<?php include '../components/left.php'; ?>
<section class="dashboard">

   <h1 class="heading">dashboard</h1>

   <div class="box-container">

<?php if ($fetch_profile['role'] == 'user'): ?>
   <div class="box">
      <?php
         $select_post = $conn->prepare("SELECT * FROM `post` WHERE author_id = ?");
         $select_post->execute([$id]);
         $numbers_of_post = $select_post->rowCount();
      ?>
      <h3><?= $numbers_of_post; ?></h3>
      <p>Bài đăng cá nhân</p>
      <a href="Add_post.php" class="btn">Đăng bài</a>
   </div>

   <div class="box">
      <?php
         $select_active_post = $conn->prepare("SELECT * FROM `post` WHERE author_id = ? AND status = ?");
         $select_active_post->execute([$id, 'active']);
         $numbers_of_active_post = $select_active_post->rowCount();
      ?>
      <h3><?= $numbers_of_active_post; ?></h3>
      <p>Bài viết được duyệt</p>
      <a href="View_post.php" class="btn">Xem bài</a>
   </div>


   <div class="box">
      <?php
         $select_deactive_post = $conn->prepare("SELECT * FROM `post` WHERE author_id = ? AND status = ?");
         $select_deactive_post->execute([$id,'deactive']);
         $numbers_of_deactive_post = $select_deactive_post->rowCount();
      ?>
      <h3><?= $numbers_of_deactive_post; ?></h3>
      <p>Bài viết chưa được duyệt</p>
      <a href="View_post_2.php" class="btn">Xem Bài</a>
   </div>
<?php endif; ?>


<?php if ($fetch_profile['role'] == ' staff' || $fetch_profile['role'] == 'admin'): ?>
      <div class="box">
      <?php
         $select_post = $conn->prepare("SELECT * FROM `post`");
         $select_post->execute();
         $numbers_of_post = $select_post->rowCount();
      ?>
      <h3><?= $numbers_of_post; ?></h3>
      <p>Bài đăng</p>
      <a href="Add_post.php" class="btn">Đăng bài</a>
    </div>

   <div class="box">
      <?php
         $select_active_post = $conn->prepare("SELECT * FROM `post` WHERE status = ?");
         $select_active_post->execute(['active']);
         $numbers_of_active_post = $select_active_post->rowCount();
      ?>
      <h3><?= $numbers_of_active_post; ?></h3>
      <p>Bài viết được duyệt</p>
      <a href="View_post.php" class="btn">Xem bài</a>
   </div>


      <div class="box">
      <?php
         $select_deactive_post = $conn->prepare("SELECT * FROM `post` WHERE status = ?");
         $select_deactive_post->execute(['deactive']);
         $numbers_of_deactive_post = $select_deactive_post->rowCount();
      ?>
      <h3><?= $numbers_of_deactive_post; ?></h3>
      <p>Bài viết chưa được duyệt</p>
      <a href="View_post_2.php" class="btn">Xem Bài</a>
   </div>

    <div class="box">
    <?php
         $select_cat = $conn->prepare("SELECT * FROM `category`");
         $select_cat->execute();
         $numbers_of_cat = $select_cat->rowCount();
      ?>
    <h3><?= $numbers_of_cat; ?></h3>
    <p>Số danh mục</p>
    <a href="View_cat.php" class="btn">Xem danh mục</a>
    </div>

    <div class="box">
      <?php
         $select_users = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
         $select_users->execute(['user']);
         $numbers_of_users = $select_users->rowCount();
      ?>
      <h3><?= $numbers_of_users; ?></h3>
      <p>Số tài khoản khách hàng</p>
      <a href="User.php" class="btn">Xem tài khoản</a>
   </div>
   <?php endif; ?>

   <?php if ($fetch_profile['role'] == 'admin'): ?>
   <div class="box">
      <?php
         $select_admins = $conn->prepare("SELECT * FROM `users` WHERE role = ?");
         $select_admins->execute([' staff']);
         $numbers_of_admins = $select_admins->rowCount();
      ?>
      <h3><?= $numbers_of_admins; ?></h3>
      <p>Số tài khoản nhân viên</p>
      <a href="Staff.php" class="btn">Xem tài khoản</a>
   </div>
   <?php endif; ?>
   </div>

</section>

</body>
</html>
