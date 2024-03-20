<?php
include 'components/connect.php';

if(isset($_GET['id'])){
   $id = $_GET['id'];
}else{
   $id = '';
}

$select_friends = $conn->prepare("SELECT users.id, users.username, users.avatar FROM `friend` 
                                 JOIN `users` ON (friend.user1_id = users.id OR friend.user2_id = users.id) 
                                 WHERE ((friend.user1_id = ? AND friend.user2_id <> ?) OR (friend.user2_id = ? AND friend.user1_id <> ?))
                                 AND friend.Agree = 'true' AND users.id <> ?");
$select_friends->execute([$id, $id, $id, $id, $id]);
$friends_list = $select_friends->fetchAll(PDO::FETCH_ASSOC);

$output = '';

// Hiển thị danh sách bạn bè
$output .= '<ul class="friend-list">';
foreach ($friends_list as $friend) {
   $output .= '<li>';
   $output .= '<a href="#" class="friend-link" data-friend-id="'.$friend['id'].'" onclick="openChatBox('.$friend['id'].')">';
   $output .= '<img src="avatar/'.$friend['avatar'].'" alt="'.$friend['username'].'" class="avatar">';
   $output .= '<p>'.$friend['username'].'</p>';
   $output .= '</a>';
   $output .= '</li>';
}
$output .= '</ul>';
// Hiển thị hộp thoại chat
$output .= '<div id="chat-box"></div>';

echo $output;
?>
<script>
function openChatBox(friendId) {
   const chatBox = document.getElementById("chat-box");

   // Xóa các tin nhắn trước đó
   chatBox.innerHTML = "";

   // Tải tin nhắn từ máy chủ
   fetch(`get_messages.php?friend_id=${friendId}`)
      .then(response => response.text())
      .then(data => {
         chatBox.innerHTML = data;
      })
      .catch(error => {
         console.error("Lỗi khi tải tin nhắn", error);
      });
}

</script>
