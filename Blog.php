<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = '';
};
if(isset($_POST['like']) && $id != ''){
   $post_id = $_POST['post_id'];
   $author_id = $_POST['author_id'];
   $id = $_SESSION['id'];

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


$search_query = '';
if(isset($_GET['search'])){
   $search_query = $_GET['search'];
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$start = ($page > 1) ? ($page * $perPage) - $perPage : 0;

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
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
<?php include 'components/header.php'; ?>

    <section class="search_bar">
        <form  class="container search_bar-container" action="">
           <a><i class="uil uil-search"></i></a>       
            <input type="search" name="search"  placeholder="Tìm kiếm theo tên BLOG" value="<?= $search_query ?>">
            <button type="submit" class="btn">GO</button>
         </form>
    </section> 
    <section class="posts-container">
   <div class="box-container">
      <?php
         $select_posts = $conn->prepare("SELECT post.*, users.username, users.avatar, category.catname FROM `post` INNER JOIN `users` ON post.author_id = users.id INNER JOIN `category` ON post.cat_id = category.cat_id WHERE post.status = 'active' AND title LIKE ? ORDER BY post.date DESC LIMIT {$start}, {$perPage}");
         $select_posts->execute(["%$search_query%"]);
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

               $liked = in_array($post_id, $liked_posts);
               ?>
               <form method="post" class="box">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <input type="hidden" name="author_id" value="<?= $fetch_posts['author_id']; ?>">
                  <div class="post-admin">
                     <?php if($avatar != ''){ ?>
                        <img src="./avatar/<?= $avatar; ?>" class="avatar">
                     <?php } ?> 
                      <div>
                        <a href="author_post.php?author_id=<?= $fetch_posts['author_id']; ?>"><?= $fetch_posts['username']; ?></a>
                        <div class="date"><?= $date; ?></div>
                     </div>
                  </div>
                  <?php if($image != ''){ ?>
                     <img src="./images/<?= $image; ?>" class="post-image">
                  <?php } ?> 
                  <a class="post-cat" href="Category.php?cat_id=<?= $fetch_posts['cat_id']; ?>" ><i class="fas fa-tag"></i> <?= $catname; ?></a>  
                  <div class="post-title"><?= $title; ?></div>    
                  <div class="posts-content"><?= $content; ?></div>
                  <div class="icons">
                     <div class="likes">
                        <button type="submit" name="like" class="post_like <?php if($liked){ echo 'style="color: red;"'; } ?>" <?php if($id == ''){ echo 'disabled'; } ?>>
                           <i class="fas fa-heart" <?php if($liked){ echo 'style="color: red;"'; } ?>></i>
                           <span><?= $total_post_likes; ?></span>
                         </button>
                     </div>
                     <a href="Read_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
                  </div>
                  <a href="Read_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Đọc ngay</a>
               </form>
               <?php
            }
         }else{
            echo '<p class="empty">Không tìm thấy bài viết nào!</p>';
         }
      ?>
   </div>
   <?php
      $select_posts = $conn->prepare("SELECT * FROM `post` WHERE status = 'active' AND title LIKE ?");
      $select_posts->execute(["%$search_query%"]);
      $totalPosts = $select_posts->rowCount();
      $totalPages = ceil($totalPosts / $perPage);

      if($totalPosts > $perPage){
         echo '<div class="pagination">';
         for($i = 1; $i <= $totalPages; $i++){
            if($i == $page){
               echo '<a class="active">'.$i.'</a>';
            }else{
               echo '<a href="?search='.$search_query.'&page='.$i.'">'.$i.'</a>';
            }
         }
         echo '</div>';
      }
   ?>
</section>

    
      <!--==============================Kết thúc phần tìm kiếm========================-->
<?php include 'components/footer.php'; ?>
<script>
const chatBubble = document.getElementById("chatBubble");
const chatBox = chatBubble.querySelector(".chat-box");
const friendList = chatBox.querySelector("#friendList");

chatBubble.addEventListener("click", () => {
   chatBox.style.display === "none"
      ? (chatBox.style.display = "block")
      : (chatBox.style.display = "none");
   if (chatBox.style.display === "block") {
      fetchFriendsList();
   }
});

const closeButton = chatBox.querySelector(".fa-times");
closeButton.addEventListener("click", () => {
   chatBox.style.display = "none";
});

function fetchFriendsList() {
   const id = <?php echo json_encode($id); ?>;
   fetch(`friend_list.php?id=${id}`)
      .then((response) => response.text())
      .then((data) => {
         friendList.innerHTML = data;
      })
      .catch((error) => {
         console.error("Error fetching friend list", error);
      });
}
</script>
</body>
</html>