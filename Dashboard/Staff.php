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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../style2.css">

</head>
<body>
<?php include '../components/left.php'; ?>
<section class="accounts">

   <h1 class="heading">Tài khoản nhân viên</h1>

   <div class="box-container">

   <?php
      $select_account = $conn->prepare("SELECT * FROM `users` WHERE role = ' staff' ");
      $select_account->execute();
      if($select_account->rowCount() > 0){
         while($fetch_accounts = $select_account->fetch(PDO::FETCH_ASSOC)){ 
            $user_id = $fetch_accounts['id']; 
            $count_user_comments = $conn->prepare("SELECT * FROM `comment` WHERE id = ?");
            $count_user_comments->execute([$user_id]);
            $total_user_comments = $count_user_comments->rowCount();
            $count_user_likes = $conn->prepare("SELECT * FROM `post_like` WHERE id = ?");
            $count_user_likes->execute([$user_id]);
            $total_user_likes = $count_user_likes->rowCount();
            
            if(isset($_POST['updatetk']) && isset($_POST['user_id']) && $_POST['user_id'] == $user_id){
                $update_role = $conn->prepare("UPDATE `users` SET role = 'admin' WHERE id = ?");
                $update_role->execute([$user_id]);
                header('location:Staff.php');
             }
 
             if(isset($_POST['locktk']) && isset($_POST['user_id']) && $_POST['user_id'] == $user_id ){
                if($fetch_accounts['active'] == 'yes'){
                   $update_active = $conn->prepare("UPDATE `users` SET active = 'no' WHERE id = ?");
                   $update_active->execute([$user_id]);
                }else{
                   $update_active = $conn->prepare("UPDATE `users` SET active = 'yes' WHERE id = ?");
                   $update_active->execute([$user_id]);
                }
                header('location:Staff.php');
             }
             if(isset($_POST['delete']) && isset($_POST['user_id']) && $_POST['user_id'] == $user_id){
               // Xóa tất cả các comment của user
               $delete_comments = $conn->prepare("DELETE FROM `comment` WHERE id = ?");
               $delete_comments->execute([$user_id]);
            
               // Xóa tất cả các like của user
               $delete_likes = $conn->prepare("DELETE FROM `post_like` WHERE id = ?");
               $delete_likes->execute([$user_id]);
            
               // Xóa tất cả các post của user
               $select_posts = $conn->prepare("SELECT * FROM `post` WHERE author_id = ?");
               $select_posts->execute([$user_id]);
               while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){ 
                  // Xóa tất cả các comment của post
                  $delete_post_comments = $conn->prepare("DELETE FROM `comment` WHERE author_id = ?");
                  $delete_post_comments->execute([$user_id]);
            
                  // Xóa tất cả các like của post
                  $delete_post_likes = $conn->prepare("DELETE FROM `post_like` WHERE author_id = ?");
                  $delete_post_likes->execute([$user_id]);
            
                  // Xóa post
                  $delete_post = $conn->prepare("DELETE FROM `post` WHERE author_id = ?");
                  $delete_post->execute([$user_id]);
               }
            
               // Xóa tài khoản
               $delete_account = $conn->prepare("DELETE FROM `users` WHERE id = ?");
               $delete_account->execute([$user_id]);
            
               header('location:Staff.php');
            }
        
   ?>
   <div class="box">
      <p> Tên : <span><?= $fetch_accounts['username']; ?></span> </p>
      <p> Số comment : <span><?= $total_user_comments; ?></span> </p>
      <p> Số tim : <span><?= $total_user_likes; ?></span> </p>
      <div class="flex-btn">
            <form action="" method="POST">
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
               <div class="button-container">
                    <button type="submit" name="updatetk" class="updatetk-btn">Cấp quyền QTV</button>
                    <?php if($fetch_accounts['active'] == 'yes'){ ?>
                        <button type="submit" name="locktk" onclick="return confirm('Có chắc chắn muốn khóa?');" class="lock-tk">Khóa tài khoản</button>
                    <?php }else{ ?>
                        <button type="submit" name="locktk" onclick="return confirm('Có chắc chắn muốn mở khóa?');" class="lock-tk">Mở khóa tài khoản</button>
                    <?php } ?>
                </div>
               <button type="submit" name="delete"onclick="return confirm('Có chắc chắn muốn xóa tài khoản?');" class="delete-btn">Xóa</button>
            </form>
       </div>
   </div>
   <?php
      }
   }else{
      echo '<p class="empty">Chưa có tài khoản nhân viên nào nào</p>';
   }
   ?>

   </div>
</section>
</body>
</html>
