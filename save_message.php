<?php
session_start();
include 'components/connect.php';

$friend_id = isset($_POST['friend_id']) ? $_POST['friend_id'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';
$date = date('Y-m-d H:i:s');

$id = isset($_SESSION['id']) ? $_SESSION['id'] : '';

$insert_message = $conn->prepare("INSERT INTO `chat` (user1_id, user2_id, message, date)
                                 VALUES (?, ?, ?, ?)");
$insert_message->execute([$id, $friend_id, $message, $date]);

// Kiểm tra xem tin nhắn đã được lưu thành công hay không
if ($insert_message) {
   echo "";
} else {
   echo "error";
}
?>