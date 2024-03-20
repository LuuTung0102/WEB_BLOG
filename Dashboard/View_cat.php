<?php

include '../components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
    header('location:../Login.php');
};

$limit = 4;
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM `category`");
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total = $row['total'];
$pages = ceil($total / $limit);

// Xác định trang hiện tại
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

// Tính offset
$offset = ($page - 1) * $limit;

// Lấy danh sách danh mục từ cơ sở dữ liệu
$stmt = $conn->prepare("SELECT * FROM `category` LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(isset($_POST['delete'])){
    $cat_id = $_POST['cat_id'];
    $cat_id = filter_var($cat_id, FILTER_SANITIZE_STRING);
    $delete_cat = $conn->prepare("DELETE FROM `category` WHERE cat_id = ?");
    $delete_cat->execute([$cat_id]);
    header('Location: View_cat.php');
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
<h1 class="heading">Danh mục</h1>
    <section class="category_section">
        <h3>Danh mục</h3>
        <form method="post" class="flex-btn" >
        
        <ul>
            <?php foreach($rows as $row){ ?>
            <li>
                <?php echo $row['catname']; ?>
                <form method="post" class="flex-btn">
                <input type="hidden" name="cat_id" value="<?= $row['cat_id']; ?>">
                <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</button>
        </form>
            </li>
            <?php } ?>
        </ul>
        </form>
        <div class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($page == $i) { echo 'class="active"'; } ?>><?php echo $i; ?></a>
            <?php } ?>
        </div>
        <a href="Add_cat.php" class="btn">Thêm danh mục</a>
    </section>    
</body>
</html>