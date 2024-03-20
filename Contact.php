<?php

include 'components/connect.php';
session_start();

if(isset($_SESSION['id'])){
   $id = $_SESSION['id'];
}else{
   $id = '';
};
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
</head>
<body>
<?php include 'components/header.php'; ?>

<?php include 'components/footer.php'; ?>
</body>
</html>