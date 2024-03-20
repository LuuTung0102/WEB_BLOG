<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = array();
};

function getUserDetails($user_id) {
    $query = $conn->prepare("SELECT * FROM `users` WHERE user_id = ?");
    $query->execute([$user_id]);
    $user_details = $query->fetch(PDO::FETCH_ASSOC);
    return $user_details;
 }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Friend Requests</title>
   <link rel="stylesheet" href="./style.css">
   <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
</head>
<body>
   <?php include 'components/header.php'; ?>
   <section class="notification-container">
      <h2>Thông báo kết bạn</h2>
      <?php
         $friend_requests = $conn->prepare("SELECT * FROM `friend` WHERE user2_id = ? AND Agree = 'false'");
         $friend_requests->execute([$id]);

         if ($friend_requests->rowCount() > 0) {
            while ($row = $friend_requests->fetch(PDO::FETCH_ASSOC)) {
               $requester_id = $row['user1_id'];
               $requester_details = getUserDetails($requester_id); 
               echo '<div class="friend-request">';
               echo '<p>' . $requester_details['username'] . ' sent you a friend request</p>';
               echo '<form method="post">';
               echo '<input type="hidden" name="friend_id" value="' . $requester_id . '">';
               echo '<button type="submit" name="accept_request">Accept</button>';
               echo '<button type="submit" name="reject_request">Reject</button>';
               echo '</form>';
               echo '</div>';
            }
         } else {
            echo '<p>Chưa có kết bạn nào mới</p>';
         }
      ?>
   </section>
</body>
</html>
