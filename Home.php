<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = array();
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
<section class="posts-container">
   <div class="box-container">
      <?php
         $select_posts = $conn->prepare("SELECT post.*, users.username, users.avatar, category.catname FROM `post` INNER JOIN `users` ON post.author_id = users.id INNER JOIN `category` ON post.cat_id = category.cat_id WHERE post.status = 'active' ORDER BY post.date DESC LIMIT 6 ");
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

      <!--==============================Kết thúc phần bài đăng========================-->
<section class="category_buttons">
   <div class="container category_buttons-container">
      <a href="category.php?cat_id=2" class="category_button">Du lịch</a>
      <a href="category.php?cat_id=1" class="category_button">Gaming</a>
      <a href="category.php?cat_id=6" class="category_button">Cuộc sống</a>
      <a href="category.php?cat_id=6" class="category_button">Nghệ thuật</a>
      <a href="category.php?cat_id=4" class="category_button">Giáo dục</a>
      <a href="category.php?cat_id=5" class="category_button">Ẩm thực</a>
   </div>
</section>

      <!--==============================Kết thúc phần danh mục========================-->
      
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
      <div class="chat-footer">
         <textarea id="messageInput" placeholder="Nhập tin nhắn"></textarea>
         <button id="sendButton">Gửi</button>
      </div>
   </div>
</div>
<?php include 'components/footer.php'; ?>
<script>
const id = <?php echo $id; ?>;
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
         const friends = friendList.querySelectorAll(".friend");
         friends.forEach((friend) => {
            friend.addEventListener("click", () => {
               const friendId = friend.getAttribute("data-id");
               openChatBox(friendId);
               chatBox.style.display = "none";
            });
         });
      })
      .catch((error) => {
         console.error("Lỗi lấy danh sách bạn bè", error);
      });
}

function openChatBox(friendId) {
   messageContainer.innerHTML = ""; // Xóa toàn bộ tin nhắn
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
      sendButton.addEventListener("click", () => {
         const message = messageInput.value.trim();
         if (message !== "") {
            sendMessage(friendId, message);
            messageInput.value = "";
         }
      });
   } else { 
      messageInput.style.display = "none";
      sendButton.style.display = "none";
      messageContainer.style.display = "none";
   }
}

function sendMessage(friendId, message) {
   const formData = new FormData();
   formData.append("friend_id", friendId);
   formData.append("message", message);
   fetch("send_message.php", {
      method: "POST",
      body: formData,
   })
      .then(() => {
         fetch(`get_messages.php?friend_id=${friendId}`)
            .then((response) => response.text())
            .then((data) => {
               messageContainer.innerHTML = data;
            })
            .catch((error) => {
               console.error("Lỗi lấy tin nhắn", error);
            });
      })
      .catch((error) => {
         console.error("Lỗi gửi tin nhắn", error);
      });
}
</script>

</script>
</body>
</html>
