<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   header('location:../Login.php');
};

if(isset($_GET['post_id'])){
    $post_id = $_GET['post_id'];
 }else{
    header('location:../Dashboard.php');
 };
 if(isset($_POST['delete'])){
    $post_id = $_POST['post_id'];
    $delete_post = $conn->prepare("DELETE FROM `post` WHERE post_id = ?");
    $delete_post->execute([$post_id]);
    header('Location: View_post.php');
    exit();
 }

if(isset($_POST['updatestatus'])){
   $post_id = $_POST['post_id'];
   $update_status = $conn->prepare("UPDATE `post` SET `status`='active' WHERE `post_id`=?");
   $update_status->execute([$post_id]);
   header('Location: View_post.php');
   exit();
}
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
<section class="read-post">

   <?php
      $select_posts = $conn->prepare("SELECT * FROM `post` WHERE post_id = ?");
      $select_posts->execute([$post_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
            $post_id = $fetch_posts['post_id'];

            $count_post_comments = $conn->prepare("SELECT * FROM `comment` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount();

            $count_post_likes = $conn->prepare("SELECT * FROM `post_like` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();

            $author_id = $fetch_posts['author_id'];
            $select_author = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_author->execute([$author_id]);
            $fetch_author = $select_author->fetch(PDO::FETCH_ASSOC);

            $cat_id = $fetch_posts['cat_id'];
            $select_cat = $conn->prepare("SELECT * FROM `category` WHERE cat_id = ?");
            $select_cat->execute([$cat_id]);
            $fetch_cat = $select_cat->fetch(PDO::FETCH_ASSOC);

   ?>
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post_id; ?>">   
      <div class="author-info">
         <a class="username">Tác giả: <?= $fetch_author['username']; ?></a>
         <div class="date">Ngày sáng tác: <?= $fetch_posts['date']; ?></div>
     </div>
     <div class="image-container">
        <?php if($fetch_posts['image'] != ''){
            $images = explode(',', $fetch_posts['image']);
            foreach($images as $image){ ?>
                <a href="../images/<?= $image; ?>" class="image-link" data-lightbox="image-set" data-title="Mô tả hình ảnh">
                    <img src="../images/<?= $image; ?>" class="image" alt="">
                </a>
            <?php } 
        } ?>
    </div>
      <div class="status" style="background-color:<?php if($fetch_posts['status'] == 'active'){echo 'limegreen'; }else{echo 'coral';}; ?>;">
        <?php if($fetch_posts['status'] == 'active'){echo 'Đã duyệt'; }else{echo 'Chưa duyệt';}; ?>
    </div>
      <div class="category">Danh mục: <?= $fetch_cat['catname']; ?></div>
      <div class="title">Tiêu đề: <?= $fetch_posts['title']; ?></div>
      <div class="content"><?= $fetch_posts['content']; ?></div>
      <div class="icons">
         <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
         <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
      </div>
      <div class="flex-btn">
        <a href="View_post.php" class="inline-option-btn">Quay lại</a>
         <a href="Edit_post.php?id=<?= $post_id; ?>" class="inline-option-btn">Chỉnh sửa</a>
         <?php
            $status = $fetch_posts['status'];
            if($status == 'deactive'){
                echo '<button type="submit" name="updatestatus" class="inline-option-btn">Duyệt bài</button>';
            }
         ?> 
         <button type="submit" name="delete" class="inline-delete-btn" onclick="return confirm('Bạn có chắc xóa?');">Xóa</button> 
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Chưa có bài đăng nào cả!</p>';
      }
   ?>
</section>

</body>
</html>