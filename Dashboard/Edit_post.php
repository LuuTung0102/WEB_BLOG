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

if(isset($_GET['id'])){
    $post_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM `post` WHERE post_id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
 }else{
    header('Location: Dashboard.php');
    exit();
 }

 if(isset($_POST['edit-post'])){
    $title = $_POST['title'];
    $content = $_POST['content'];
    $cat_id = $_POST['cat_id'];
    $post_id = $_POST['post_id'];
    $author_id = $_POST['author'];
    $status = $_POST['status'];
    $date = $_POST['date'];
    $username = $_POST['name'];

    if(isset($_FILES['image']) && $_FILES['image']['error'][0] == 0){
        $images = array();
        $total = count($_FILES['image']['name']);
        for($i=0; $i<$total; $i++) {
            $tmpFilePath = $_FILES['image']['tmp_name'][$i];
            if($tmpFilePath != ""){
                $newFilePath = "../images/" . $_FILES['image']['name'][$i];
                if(move_uploaded_file($tmpFilePath, $newFilePath)) {
                    $images[] = $_FILES['image']['name'][$i];
                }
            }
        }
        $images_str = implode(',', $images);
    }else{
        $images_str = $post['image'];
    }

    $stmt = $conn->prepare("UPDATE `post` SET `title`=?,`content`=?,`cat_id`=?,`image`=?,`author_id`=?,`status`=?,`date`=?,`username`=? WHERE `post_id`=?");
    $stmt->execute([$title, $content, $cat_id, $images_str, $author_id, $status, $date, $username, $post_id]);

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
   <h1 class="heading">edit post</h1>
   <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="name" value="<?= $post['username']; ?>">
        <input type="hidden" name="post_id" value="<?= $post['post_id']; ?>">
        <input type="hidden" name="date" value="<?= $post['date']; ?>">
        <input type="hidden" name="author" value="<?= $post['author_id']; ?>">
        <input type="hidden" name="status" value="<?= $post['status']; ?>">
        <p>Tiêu đề</p>
        <input type="text" name="title" maxlength="100" required placeholder="Hãy viết tiêu đề bài viết" class="box" value="<?= $post['title']; ?>">
        <p>Nội dung</p>
        <textarea name="content" class="box" required maxlength="10000" placeholder="Nơi nội dung được viết..." cols="30" rows="10"><?= $post['content']; ?></textarea>
        <div class="image-container">
        <?php if($post['image'] != ''){
            $images = explode(',', $post['image']);
            foreach($images as $image){ ?>
                <a href="../images/<?= $image; ?>" class="image-link" data-lightbox="image-set" data-title="Mô tả hình ảnh">
                    <img src="../images/<?= $image; ?>" class="image" alt="">
                </a>
            <?php } 
        } ?>
        </div>
        <p>Danh mục</p>
        <select name="cat_id" class="box" required>
        <option value="">Chọn danh mục</option>
        <?php foreach($categories as $category){ ?>
        <option value="<?= $category['cat_id']; ?>" <?php if($category['cat_id'] == $post['cat_id']){ echo 'selected'; } ?>><?= $category['catname']; ?></option>
        <?php } ?>
        </select>
        <p>Hình ảnh</p>
        <input type="file"="image[]" class="box" accept="image/jpg, image/jpeg, image/png, image/webp" multiple>
        <div class="flex-btn">
            <a href="View_post.php" class="option-btn">Quay lại</a>
            <input type="submit" value="Lưu" name="edit-post" class="option-btn">
        </div>
    </form>
</section>

</body>
</html>