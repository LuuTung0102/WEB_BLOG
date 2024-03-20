<?php
session_start();
include 'components/connect.php';

$friend_id = isset($_GET['friend_id']) ? $_GET['friend_id'] : '';
$id = isset($_SESSION['id']) ? $_SESSION['id'] : '';

$select_messages = $conn->prepare("SELECT chat.message, chat.date, users.username, chat.user1_id, chat.user2_id
                                  FROM `chat`
                                  JOIN `users` ON chat.user1_id = users.id
                                  WHERE (chat.user1_id = ? AND chat.user2_id = ?) OR (chat.user1_id = ? AND chat.user2_id = ?)
                                  ORDER BY chat.date ASC");
$select_messages->execute([$friend_id, $id, $id, $friend_id]);
$messages = $select_messages->fetchAll(PDO::FETCH_ASSOC);

$output = '';

foreach ($messages as $message) {
   $sender_id = $message['user1_id'];
   // Tin nhắn từ bạn bè (hiển thị từ bên phải)
   $output .= '<div class="message friend-message">';
   
   $output .= '<p class="sender">'.$message['username'].'</p>';
   $output .= '<p class="content">'.$message['message'].'</p>';
   $output .= '<p class="date">'.$message['date'].'</p>';
   $output .= '</div>';
}

echo $output;
?>
