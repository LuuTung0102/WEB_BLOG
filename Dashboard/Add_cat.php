<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   header('location:../Login.php');
};
if (isset($_POST['submit'])) {
    $catname = $_POST['catname'];
    $catname = filter_var($catname, FILTER_SANITIZE_STRING);

    $stmt = $conn->prepare("INSERT INTO `category` (`catname`) VALUES (?)");
    $stmt->execute([$catname]);

    header('location:View_cat.php');
}
?>
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
<h1 class="heading">Thêm mới danh mục</h1>
<form action="" method="post">
        <h1>Thêm danh mục</h1>
        <input type="text" name="catname" required placeholder="Nhập tên danh mục" class="box" maxlength="50">
        <input type="submit" value="Thêm danh mục" name="submit" class="btn" >
        <a href="View_cat.php" class="btn">Xem danh mục</a>
</form>
</section>
</body>
</html>