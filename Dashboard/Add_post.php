<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   header('location:../Login.php');
};

$stmt = $conn->prepare("SELECT * FROM `category`");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$stmt->execute([$id]);
$fetch_profile = $stmt->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['add-post'])){
   $title = $_POST['title'];
   $content = $_POST['content'];
   $cat_id = $_POST['cat_id'];
   $images = '';
   $date = date('Y-m-d H:i:s');
   $status = 'deactive';
   foreach($_FILES['image']['tmp_name'] as $key => $tmp_name){
      $image = $_FILES['image']['name'][$key];
      move_uploaded_file($tmp_name, "../images/$image");
      $images .= $image . ',';
   }
   $images = rtrim($images, ',');
   $stmt = $conn->prepare("INSERT INTO `post` (post_id, author_id, username, title, content, cat_id, image, date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
   $stmt->execute([null, $id, $fetch_profile['username'], $title, $content, $cat_id, $images, $date, $status]);


   header('Location: Dashboard.php');
   exit();
}

if(isset($_POST['add-post-2'])){
   $title = $_POST['title'];
   $content = $_POST['content'];
   $cat_id = $_POST['cat_id'];
   $images = '';
   $date = date('Y-m-d H:i:s');
   $status = 'active';
   foreach($_FILES['image']['tmp_name'] as $key => $tmp_name){
      $image = $_FILES['image']['name'][$key];
      move_uploaded_file($tmp_name, "../images/$image");
      $images .= $image . ',';
   }
   $images = rtrim($images, ',');
   $stmt = $conn->prepare("INSERT INTO `post` (post_id, author_id, username, title, content, cat_id, image, date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
   $stmt->execute([null, $id, $fetch_profile['username'], $title, $content, $cat_id, $images, $date, $status]);
   header('Location: Dashboard.php');
   exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../style2.css">

</head>
<body>
<?php include '../components/left.php'; ?>
<section class="post-editor">

<h1 class="heading">Thêm mới bài viết</h1>
<?php if ($fetch_profile['role'] == 'user'): ?>
<form action="" method="post" enctype="multipart/form-data">
   <input type="hidden" name="name" value="<?= $fetch_profile['username']; ?>">
   <p>Tiêu đề</p>
   <input type="text" name="title" maxlength="100" required placeholder="Hãy viết tiêu đề bài viết" class="box">
   <p>Nội dung</p>
   <textarea name="content" class="box" required maxlength="10000" placeholder="Nơi nội dung được viết..." cols="30" rows="10"></textarea>
   <p>Danh mục</p>
   <select name="cat_id" class="box" required>
   <option value="">Chọn danh mục</option>
   <?php foreach($categories as $category){ ?>
      <option value="<?= $category['cat_id']; ?>"><?= $category['catname']; ?></option>
   <?php } ?>
   </select>
   <p>Hình ảnh</p>
   <input type="file" name="image[]" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" multiple>
      <div class="flex-btn">
         <input type="submit" value="Xóa tất cả" name="delete" class="btn">
         <input type="submit" value="Đăng bài" name="add-post" class="option-btn">
      </div>
</form>
<?php endif; ?>

<?php if ($fetch_profile['role'] == ' staff' || $fetch_profile['role'] == 'admin'): ?>
<form action="" method="post" enctype="multipart/form-data">
   <input type="hidden" name="name" value="<?= $fetch_profile['username']; ?>">
   <p>Tiêu đề</p>
   <input type="text" name="title" maxlength="100" required placeholder="Hãy viết tiêu đề bài viết" class="box">
   <p>Nội dung</p>
   <textarea name="content" class="box" required maxlength="10000" placeholder="Nơi nội dung được viết..." cols="30" rows="10"></textarea>
   <p>Danh mục</p>
   <select name="cat_id" class="box" required>
   <option value="">Chọn danh mục</option>
   <?php foreach($categories as $category){ ?>
      <option value="<?= $category['cat_id']; ?>"><?= $category['catname']; ?></option>
   <?php } ?>
   </select>
   <p>Hình ảnh</p>
   <input type="file" name="image[]" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" multiple>
      <div class="flex-btn">
         <input type="submit" value="Xóa tất cả" name="delete" class="btn">
         <input type="submit" value="Đăng bài" name="add-post-2" class="option-btn">
      </div>
</form>
<?php endif; ?>
</section>
</body>
</html>