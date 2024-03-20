
<nav>
		<div class="container nav_container">
			<li><a href="Home.php" class="nav_logo">TUNGVINH</a></li>
			<ul class="nav_items">
				<li><a href="Blog.php">Blog</a></li>
				<li><a href="Manage_category.php">Danh mục</a></li>
				<li><a href="Contact.php">Điều khoản</a></li>
				<?php
				if(isset($_SESSION['id'])) {
					// Người dùng đã đăng nhập, hiển thị liên kết Dashboard và Logout
					echo '<li class="nav_profile">';
					echo '<div class="avatar">';
					$select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
					$select_user->execute([$_SESSION['id']]);
					$row = $select_user->fetch(PDO::FETCH_ASSOC);
					echo '<img src="./avatar/'.$row['avatar'].'">';
					echo '</div>';
					echo '<ul>';
					echo '<li><a href="Dashboard/Dashboard.php">Dashboard</a></li>';
					echo '<li><a href="Notification.php">Thông báo kết bạn</a></li>';
					echo '<li><a href="Update.php">Chỉnh sửa tài khoản</a></li>';
					echo '<li><a href="components/logout.php" onclick="return confirm(\'Bạn có chắc là đăng xuất không?\');" class="delete-btn">Đăng xuất</a></li>';
					echo '</ul>';
					echo '</li>';
				} else {
					// Người dùng chưa đăng nhập, hiển thị liên kết Đăng nhập và Đăng ký
					echo '<li><a href="Login.php">Đăng nhập</a></li>';
					echo '<li><a href="Register.php">Đăng ký</a></li>';
				}
				?>
			</ul>
		</div>
	</nav>

    <!--==============================Kết thúc phần đầu trang========================-->