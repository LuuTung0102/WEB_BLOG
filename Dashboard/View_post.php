<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   header('location:../Login.php');
};

if(isset($_POST['delete'])){
   $post_id = $_POST['post_id'];
   $stmt = $conn->prepare("DELETE FROM `comment` WHERE `post_id`=?");
   $stmt->execute([$post_id]);

   $stmt = $conn->prepare("DELETE FROM `post_like` WHERE `post_id`=?");
   $stmt->execute([$post_id]);

   $stmt = $conn->prepare("DELETE FROM `post` WHERE `post_id`=?");
   $stmt->execute([$post_id]);
   
   header('Location: Dashboard.php');
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
<?php if ($fetch_profile['role'] == 'user'): ?>
<section class="show-posts">
   <h1 class="heading">Bài đang hoạt động</h1>
   <div class="box-container">
      <?php
        $select_posts = $conn->prepare("SELECT post.*, users.username, users.avatar, category.catname FROM `post` INNER JOIN `users` ON post.author_id = users.id INNER JOIN `category` ON post.cat_id = category.cat_id WHERE post.status = 'active' AND post.author_id = ? ORDER BY post.date DESC");
        $select_posts->execute([$id]);        
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               $post_id = $fetch_posts['post_id'];
               $author_id = $fetch_posts['author_id'];
               $username = $fetch_posts['username'];
               $title = $fetch_posts['title'];
               $content = substr($fetch_posts['content'], 0, 150);
               $image = explode(',', $fetch_posts['image'])[0];
               $date = date('F j, Y', strtotime($fetch_posts['date']));
               $avatar = $fetch_posts['avatar'];
               $catname = $fetch_posts['catname'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comment` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `post_like` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               ?>
               <form method="post" class="box">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <div class="author-info">
                   <a class="username"><?= $username; ?></a>
                  </div>
                  <div class="date"><?= $date; ?></div>
                  <?php if($image != ''){ ?>
                     <img src="../images/<?= $image; ?>" class="image">
                  <?php } ?> 
                  <a  class="title" >Danh mục : <?= $catname; ?></a>  
                  <div class="title"><?= $title; ?></div>    
                  <div class="posts-content"><?= $content; ?></div>
                  <div class="icons">
                  <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                 <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                </div>
                  <div class="flex-btn">
                   <a href="Edit_post.php?id=<?= $post_id; ?>" class="option-btn">Edit</a>
                  <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Bạn có chắc xóa?');">Xóa</button>
                  </div>
                  <a href="Read_post.php?post_id=<?= $post_id; ?>" class="btn">Xem chi tiết</a>
               </form>
               <?php
            }
         }else{
            echo '<p class="empty">Chưa có bài nào được đăng!</p>';
         }
      ?>
   </div>
</section>
<?php endif; ?>







<?php if ($fetch_profile['role'] == ' staff' || $fetch_profile['role'] == 'admin'): ?>
<section class="show-posts">
   <h1 class="heading">Bài đang hoạt động</h1>
   <div class="box-container">
      <?php
         $select_posts = $conn->prepare("SELECT post.*, users.username, users.avatar, category.catname FROM `post` INNER JOIN `users` ON post.author_id = users.id INNER JOIN `category` ON post.cat_id = category.cat_id WHERE post.status = 'active' ORDER BY post.date DESC");
         $select_posts->execute();
         if($select_posts->rowCount() > 0){
            while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
               $post_id = $fetch_posts['post_id'];
               $author_id = $fetch_posts['author_id'];
               $username = $fetch_posts['username'];
               $title = $fetch_posts['title'];
               $content = substr($fetch_posts['content'], 0, 150);
               $image = explode(',', $fetch_posts['image'])[0];
               $date = date('F j, Y', strtotime($fetch_posts['date']));
               $avatar = $fetch_posts['avatar'];
               $catname = $fetch_posts['catname'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comment` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `post_like` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               ?>
               <form method="post" class="box">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <div class="author-info">
                     <?php if($avatar != ''){ ?>
                        <img src="../avatar/<?= $avatar; ?>" class="avatar">
                     <?php } ?> 
                   <a class="username"><?= $username; ?></a>
                  </div>
                  <div class="date"><?= $date; ?></div>
                  <?php if($image != ''){ ?>
                     <img src="../images/<?= $image; ?>" class="image">
                  <?php } ?> 
                  <a  class="title" >Danh mục : <?= $catname; ?></a>  
                  <div class="title"><?= $title; ?></div>    
                  <div class="posts-content"><?= $content; ?></div>
                  <div class="icons">
                  <div class="likes"><i class="fas fa-heart"></i><span><?= $total_post_likes; ?></span></div>
                 <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
                </div>
                  <div class="flex-btn">
                   <a href="edit_post.php?id=<?= $post_id; ?>" class="option-btn">Edit</a>
                  <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Bạn có chắc xóa?');">Xóa</button>
                  </div>
                  <a href="read_post.php?post_id=<?= $post_id; ?>" class="btn">Xem chi tiết</a>
               </form>
               <?php
            }
         }else{
            echo '<p class="empty">Chưa có bài nào được đăng!</p>';
         }
      ?>
   </div>
</section>
<?php endif; ?>
</body>
</html>