<?php
include 'connect.php';

if (isset($_GET['author_id'])) {
   $author_id = $_GET['author_id'];

   $select_friends = $conn->prepare("SELECT u.id, u.username, u.avatar FROM `friend` f JOIN `users` u ON f.user1_id = u.id OR f.user2_id =u.id WHERE f.Agree = 'true' AND (f.user1_id = ? OR f.user2_id = ?) AND u.id != ?");
   $select_friends->execute([$author_id, $author_id, $author_id]);
   $friends = $select_friends->fetchAll(PDO::FETCH_ASSOC);

   foreach ($friends as $friend) {
      echo '
         <li class="friend">
            <img src="' . $friend['avatar'] . '" alt="' . $friend['username'] . '">
            <p>' . $friend['username'] . '</p>
         </li>
      ';
   }
}
?>