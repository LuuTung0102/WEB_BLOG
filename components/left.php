<header class="header">

   <a href="Dashboard.php" class="logo">TUNGVINH - <span>Panel</span></a>

   <div class="profile">
      <?php
         $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
         $select_profile->execute([$id]);
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
       <h1>Xin chào!</h1>
      <p><?= $fetch_profile['username']; ?></p>
      <a href="/Đồ án/Update.php" class="btn">Cập nhật</a>
   </div>

   <nav class="navbar">
   <a href="Dashboard.php"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a>
   <a href="Add_post.php"><i class="fas fa-pen"></i> <span>Thêm bài</span></a>
   <?php if ($fetch_profile['role'] == ' staff' || $fetch_profile['role'] == 'admin'): ?>
      <a href="Add_cat.php"><i class="fas fa-pen"></i> <span>Thêm danh mục</span></a>
      <a href="User.php"><i class="fas fa-user"></i> <span>Acc User</span></a>
   <?php endif; ?>
   <?php if ($fetch_profile['role'] == 'admin'): ?>
      <a href="Staff.php"><i class="fas fa-user"></i> <span>Acc Staff</span></a>
   <?php endif; ?>
   <a href="/Đồ án/Home.php" style="color:var(--red);" onclick="return confirm('Bạn có muốn về trang chủ ?');"><i class="fas fa-home"></i><span>Trang chủ </span></a>
   <a href="../components/logout.php" style="color:var(--red);" onclick="return confirm('Bạn chắc chắn đăng xuất  ?');"><i class="fas fa-right-from-bracket"></i><span>Đăng xuất</span></a>
</nav>
</header>

 <!--==============================Kết thúc dashboard========================-->
