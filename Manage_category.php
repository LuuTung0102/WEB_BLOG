<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
  ['id'];
}else{
   $id = '';
};

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
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
<section class="posts-container">
<div class="box-container">
<?php
 
$select_cats = $conn->prepare("SELECT * FROM `category` LIMIT $start, $perPage");
$select_cats->execute();
if($select_cats->rowCount() > 0){
   while($fetch_cats = $select_cats->fetch(PDO::FETCH_ASSOC)){
      $cat_id = $fetch_cats['cat_id'];
      $catname = $fetch_cats['catname'];
      ?>
        <h1><a href="category.php?cat_id=<?= $cat_id; ?>"><i class="fas fa-tag"></i><?= '-'.$catname; ?></a></h1>
      <?php
   }
}else{
   echo '<p class="empty">Không có danh mục nào được tìm thấy!</p>';
}

// Pagination
$select_count = $conn->prepare("SELECT COUNT(*) AS total FROM `category`");
$select_count->execute();
$total = $select_count->fetch(PDO::FETCH_ASSOC)['total'];
$pages = ceil($total / $perPage);
?>
</div>
</section>
<div class="pagination">
   <?php for($i = 1; $i <= $pages; $i++) : ?>
      <a href="?page=<?= $i; ?>" <?= ($page === $i) ? 'class="active"' : ''; ?>><?= $i; ?></a>
   <?php endfor; ?>
</div>
</body>
</html>