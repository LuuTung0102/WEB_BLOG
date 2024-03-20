<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = '';
};
if(isset($_GET['post_id'])){
    $post_id = $_GET['post_id'];
 }else{
    header('location:Home.php');
 };

 if(isset($_POST['like']) && $id != ''){
   $author_id = $_POST['author_id'];
   $check_like = $conn->prepare("SELECT * FROM `post_like` WHERE post_id = ? AND id = ?");
   $check_like->execute([$post_id, $id]);
   if($check_like->rowCount() > 0){
      $delete_like = $conn->prepare("DELETE FROM `post_like` WHERE post_id = ? AND id = ?");
      $delete_like->execute([$post_id, $id]);
   }else{
      $insert_like = $conn->prepare("INSERT INTO `post_like` (post_id, author_id, id) VALUES (?, ?, ?)");
      $insert_like->execute([$post_id, $author_id, $id]);
   }
}
$liked_posts = array(); 

if ($id != '') {
   $select_liked_posts = $conn->prepare("SELECT post_id FROM `post_like` WHERE id = ?");
   $select_liked_posts->execute([$id]);
   while ($liked_post = $select_liked_posts->fetch(PDO::FETCH_ASSOC)) {
      $liked_posts[] = $liked_post['post_id']; 
   }
}

if(isset($_POST['delete_comment'])){
   $delete_comment_id = $_POST['comment_id'];
   $delete_comment = $conn->prepare("DELETE FROM `comment` WHERE comment_id = ?");
   $delete_comment->execute([$delete_comment_id]);
}


if (isset($_POST['edit_comment'])) {
    $edit_comment_id = $_POST['edit_comment_id'];
    $comment_edit_box = $_POST['comment_edit_box'];

    $update_comment = $conn->prepare("UPDATE `comment` SET comment = ? WHERE comment_id = ?");
    $update_comment->execute([$comment_edit_box, $edit_comment_id]);

    header("Location: Read_post.php?post_id=" . $post_id);
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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
<?php include 'components/header.php'; ?>
<div class="post-container">
<?php
if(isset($_POST['open_edit_box'])){
    $comment_id = $_POST['comment_id'];
    $select_edit_comment = $conn->prepare("SELECT * FROM `comment` WHERE comment_id = ?");
    $select_edit_comment->execute([$comment_id]);
    $fetch_edit_comment = $select_edit_comment->fetch(PDO::FETCH_ASSOC);
?>
   <section class="comment-edit-form">
   <p>Chỉnh sửa commment</p>
   <form action="" method="POST">
      <input type="hidden" name="edit_comment_id" value="<?= $comment_id; ?>">
      <textarea name="comment_edit_box" required cols="30" rows="10" placeholder="Hãy nhập mới hoặc chỉnh sửa vào đây"><?= $fetch_edit_comment['comment']; ?></textarea>
      <button type="submit" class="inline-btn" name="edit_comment">Chỉnh sửa comment</button>
      <div class="inline-btn" onclick="window.location.href = 'Read_post.php?post_id=<?= $post_id; ?>';">Đóng chỉnh sửa</div>
      </form>
   </section>
<?php
}
?>
<section class="read-post">
   <?php
      $select_posts = $conn->prepare("SELECT * FROM `post` WHERE post_id = ?");
      $select_posts->execute([$post_id]);
      if($select_posts->rowCount() > 0){
         while($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)){
            $post_id = $fetch_posts['post_id'];
            $author_id = $fetch_posts['author_id'];
            $count_post_comments = $conn->prepare("SELECT * FROM `comment` WHERE post_id = ?");
            $count_post_comments->execute([$post_id]);
            $total_post_comments = $count_post_comments->rowCount();

            $count_post_likes = $conn->prepare("SELECT * FROM `post_like` WHERE post_id = ?");
            $count_post_likes->execute([$post_id]);
            $total_post_likes = $count_post_likes->rowCount();

            $select_author = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_author->execute([$author_id]);
            $fetch_author = $select_author->fetch(PDO::FETCH_ASSOC);

            $cat_id = $fetch_posts['cat_id'];
            $select_cat = $conn->prepare("SELECT * FROM `category` WHERE cat_id = ?");
            $select_cat->execute([$cat_id]);
            $fetch_cat = $select_cat->fetch(PDO::FETCH_ASSOC);

            $liked = in_array($post_id, $liked_posts);
   ?>
   <form method="post">
      <input type="hidden" name="post_id" value="<?= $post_id; ?>"> 
      <input type="hidden" name="author_id" value="<?= $fetch_posts['author_id']; ?>">  
      <div class="author-info">
         <a class="username">Tác giả: <?= $fetch_author['username']; ?></a>
         <div class="date">Ngày sáng tác: <?= $fetch_posts['date']; ?></div>
     </div>
     <div class="image-container">
        <?php if($fetch_posts['image'] != ''){
            $images = explode(',', $fetch_posts['image']);
            foreach($images as $image){ ?>
                <a href="./images/<?= $image; ?>" class="image-link" data-lightbox="image-set" data-title="Mô tả hình ảnh">
                    <img src="./images/<?= $image; ?>" class="image" alt="">
                </a>
            <?php } 
        } ?>
    </div>
      <a class="category" href="Category.php?cat_id=<?= $fetch_posts['cat_id']; ?>" ><i class="fas fa-tag"></i> <?= $fetch_cat['catname']; ?></a>  
      <div class="title">Tiêu đề: <?= $fetch_posts['title']; ?></div>
      <div class="content"><?= $fetch_posts['content']; ?></div>
      <div class="icons">
        <div class="likes">
            <button type="submit"name="like" class="post_like <?php if($liked){ echo 'style="color: red;"'; } ?>" <?php if($id == ''){ echo 'disabled'; } ?>>
                <i class="fas fa-heart" <?php if($liked){ echo 'style="color: red;"'; } ?>></i>
                    <span><?= $total_post_likes; ?></span>
            </button>
        </div>
         <div class="comments"><i class="fas fa-comment"></i><span><?= $total_post_comments; ?></span></div>
      </div>
   </form>
   <?php
         }
      }else{
         echo '<p class="empty">Đã xảy ra lỗi!</p>';
      }
   ?>
</section>



<section class="comments-container">

<p class="comment-title">Add comment</p>
   <?php
      if($id != ''){  
         $select_id = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_id->execute([$id]);
         $fetch_id = $select_id->fetch(PDO::FETCH_ASSOC);

         if(isset($_POST['add_comment'])){
            $comment = $_POST['comment'];
            $date = date('Y-m-d');
         
            $insert_comment = $conn->prepare("INSERT INTO `comment` (post_id, author_id, id, username, comment, date) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_comment->execute([$post_id, $author_id, $id, $fetch_id['username'], $comment, $date]);
      
         }
   ?>
   <form action="" method="post" class="add-comment">
      <input type="hidden" name="author_id" value="<?= $fetch_id['id']; ?>">  
      <div class="user">
         <img src="./avatar/<?= $fetch_id['avatar'];?>" class="avatar">
         <div>
            <a class="username"><?= $fetch_id['username'];?></a>
         </div>
      </div>
      <textarea name="comment" maxlength="1000" class="comment-box" cols="30" rows="10" placeholder="Hãy nhập comment vào đây!" required></textarea>
      <input type="submit" value="add comment" class="inline-btn" name="add_comment">
   </form>
   <?php
   }else{
   ?>
   <div class="add-comment">
      <p>Hãy đăng nhập để comment hoặc để chỉnh sửa comment</p>
      <a href="Login.php" class="inline-btn">Đăng nhập ngay</a>
   </div>
   <?php
      }
   ?>
   
<p class="comment-title">Comments</p>
   <div class="user-comments-container">
      <?php
         $select_comments = $conn->prepare("SELECT comment.*, users.avatar FROM `comment` JOIN `users` ON comment.id = users.id WHERE post_id = ?");
         $select_comments->execute([$post_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comments = $select_comments->fetch(PDO::FETCH_ASSOC)){
      ?>
      <div class="show-comments" style="<?php if($fetch_comments['id'] == $author_id){echo 'order:-1;'; } ?>">
         <div class="comment-user">
         <img src="./avatar/<?= $fetch_comments['avatar']; ?>" class="avatar">
            <div>
               <span><?= $fetch_comments['username']; ?></span>
               <div><?= $fetch_comments['date']; ?></div>
            </div>
         </div>
         <div class="comment-box" style="<?php if($fetch_comments['id'] == $author_id){echo 'color:var(--white); background:var(--black);'; } ?>"><?= $fetch_comments['comment']; ?></div>
         <?php
            if($fetch_comments['id'] == $id){  
         ?>
         <form action="" method="POST">
            <input type="hidden" name="comment_id" value="<?= $fetch_comments['comment_id']; ?>">
            <button type="submit" class="inline-option-btn" name="open_edit_box">Chỉnh sửa</button>
            <button type="submit" class="inline-delete-btn" name="delete_comment" onclick="return confirm('Bạn có chắc muốn xóa?');">Xóa</button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty">Chưa có comment nào!</p>';
         }
      ?>
   </div>
</section>
</div>
</body>
</html>