<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = '';
};

if(isset($_GET['author_id'])){
    $author_id = $_GET['author_id'];
 }else{
    $author_id = '';
 }

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


$select_author_info = $conn->prepare("SELECT username, email FROM `users` WHERE id = ?");
$select_author_info->execute([$author_id]);
$author_details = $select_author_info->fetch(PDO::FETCH_ASSOC);

$count_author_likes = $conn->prepare("SELECT * FROM `post_like` WHERE author_id = ?");
$count_author_likes->execute([$author_id]);
$total_author_likes = $count_author_likes->rowCount();

$count_friends = $conn->prepare("SELECT COUNT(*) AS total_friends FROM `friend` WHERE Agree = true AND (user1_id = ? OR user2_id = ?)");
$count_friends->execute([$author_id, $author_id]);
$result_friends = $count_friends->fetch(PDO::FETCH_ASSOC);
$total_friends = $result_friends['total_friends'];

$is_friend = false;

$select_friends = $conn->prepare("SELECT * FROM `friend` WHERE Agree = 'true' AND ((user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?))");
$select_friends->execute([$id, $author_id, $author_id, $id]);

if($select_friends->rowCount() > 0){
   $is_friend = true;
}
if (isset($_POST['add_friend']) && $id != '') {
   $friend_id = $_GET['author_id'];
   $check_friend = $conn->prepare("SELECT * FROM `friend` WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
   $check_friend->execute([$id, $friend_id, $friend_id, $id]);
   if ($check_friend->rowCount() == 0) {
      $add_friend = $conn->prepare("INSERT INTO `friend` (user1_id, user2_id, Agree) VALUES (?, ?, 'false')");
      $add_friend->execute([$id, $friend_id]);
   }
}

if(isset($_POST['remove_friend']) && $id != ''){
   $friend_id = $_GET['author_id'];
   $remove_friend = $conn->prepare("DELETE FROM `friend` WHERE (user1_id = ? AND user2_id = ?) OR (user1_id = ? AND user2_id = ?)");
   $remove_friend->execute([$id, $friend_id, $friend_id, $id]);
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
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
<?php include 'components/header.php'; ?>
<?php if ($id != $author_id) { ?>
   <section class="friend-container">
      <div class="box-container">
         <div class="box">
            <p>Chào bạn đã đến với trang cá nhân của <?= $author_details['username']; ?></p>
            <p>Email: <?= $author_details['email']; ?></p>
            <p>Số lượt like: <?= $total_author_likes; ?></p>
            <p>Số người kết bạn: <?= $total_friends; ?></p>
            <?php if ($id != '') : ?>
                  <form method="post" class="box">
                     <?php if (!$is_friend) : ?>
                        <button type="submit" name="add_friend" class="add-friend-btn">Thêm bạn</button>
                     <?php else : ?>
                        <button type="submit" name="remove_friend" class="remove-friend-btn">Hủy kết bạn</button>
                     <?php endif; ?>
                  </form>
               <?php endif; ?>
         </div>
      </div>
   </section>
<?php } ?>
<section class="posts-container">
   <div class="box-container">
      <?php
         $select_posts = $conn->prepare("SELECT post.*, users.username, users.avatar, category.catname FROM `post` INNER JOIN `users` ON post.author_id = users.id INNER JOIN `category` ON post.cat_id = category.cat_id WHERE post.status = 'active' AND post.author_id = ? ORDER BY post.date DESC");
         $select_posts->execute([$author_id]);
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

               $select_author = $conn->prepare("SELECT username, email FROM `users` WHERE id = ?");
               $select_author->execute([$author_id]);
               $author_details = $select_author->fetch(PDO::FETCH_ASSOC);

               // Count total likes for the author
               $count_author_likes = $conn->prepare("SELECT * FROM `post_like` WHERE author_id = ?");
               $count_author_likes->execute([$author_id]);
               $total_author_likes = $count_author_likes->rowCount();

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
                      <?php if($id != $author_id){ ?>
                           <a href="author_post.php?author_id=<?= $fetch_posts['author_id']; ?>"><?= $fetch_posts['username']; ?></a>
                        <?php } ?>
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
                        <button type="submit" name="like" class="post_like" <?php if($id == ''){ echo 'disabled'; } ?>>
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
</section>

<div id="chatBubble" class="chat-bubble">
   <i class="fas fa-comments"></i>
   <div class="chat-box">
      <div class="chat-header">
         <h2>Bạn bè</h2>
         <i class="fas fa-times"></i>
      </div>
      <div class="chat-body">
         <ul id="friendList" class="friends-list">
         </ul>
      </div>
      <div id="message-container" class="message-container"></div>
      <div class="message-input">
         <input type="text" id="messageInput" placeholder="Nhập tin nhắn...">
         <button id="sendButton">Gửi</button>
      </div>
   </div>
</div>
<?php include 'components/footer.php'; ?>
<script>
const chatBubble = document.getElementById("chatBubble");
const chatBox = chatBubble.querySelector(".chat-box");
const friendList = chatBox.querySelector("#friendList");
const messageContainer = chatBox.querySelector("#message-container");
const messageInput = chatBox.querySelector("#messageInput");
const sendButton = chatBox.querySelector("#sendButton");
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
         console.error("Lỗi lấy danh sách bạn bè", error);
      });
}

function openChatBox(friendId) {
   friendList.innerHTML = ""; // Xóa toàn bộ danh sách bạn bè
   messageContainer.innerHTML = ""; 
   if (friendId) { 
      fetch(`get_messages.php?friend_id=${friendId}`)
         .then((response) => response.text())
         .then((data) => {
            messageContainer.innerHTML = data;
         })
         .catch((error) => {
            console.error("Lỗi lấy tin nhắn", error);
         });
      messageInput.style.display = "block";
      sendButton.style.display = "block";
      messageContainer.style.display = "block";
   } else { 
      messageInput.style.display = "none";
      sendButton.style.display = "none";
      messageContainer.style.display = "none";
   }
}
sendButton.addEventListener("click", (event) => {
   event.preventDefault(); 

   const friendId = friendList.querySelector(".active").getAttribute("data-friend-id");
   const message = messageInput.value;
   fetch(`save_message.php?friend_id=${friendId}&message=${message}`)
      .then(() => {
         messageInput.value = ""; 
         openChatBox(friendId); 
      })
      .catch((error) => {
         console.error("Lỗi gửi tin nhắn", error);
      });
});

friendList.addEventListener("click", (event) => {
   event.stopPropagation();
});

messageContainer.addEventListener("click", (event) => {
   event.stopPropagation();
});

messageInput.addEventListener("click", (event) => {
   event.stopPropagation();
});
</script>
</body>
</html>