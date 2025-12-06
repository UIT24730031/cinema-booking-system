<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #1a1a1a 0%, #0a0a0a 100%);
    padding: 20px 0;
    border-right: 1px solid #333;
    z-index: 1000;
}
.sidebar-brand {
    padding: 20px;
    text-align: center;
    color: #e50914;
    font-size: 24px;
    font-weight: bold;
    border-bottom: 1px solid #333;
    margin-bottom: 20px;
}
.sidebar-menu {
    list-style: none;
    padding: 0;
}
.sidebar-menu li a {
    display: block;
    padding: 15px 25px;
    color: #aaa;
    text-decoration: none;
    transition: all 0.3s;
}
.sidebar-menu li a:hover,
.sidebar-menu li a.active {
    background: rgba(229, 9, 20, 0.1);
    color: #e50914;
    border-left: 3px solid #e50914;
}
.sidebar-menu li a i {
    margin-right: 10px;
    width: 20px;
}
</style>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-film"></i> ADMIN PANEL
    </div>
    <ul class="sidebar-menu">
        <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="manage_movies.php"><i class="fas fa-film"></i> Quản lý phim</a></li>
        <li><a href="manage_screenings.php"><i class="fas fa-calendar-alt"></i> Lịch chiếu</a></li>
        <li><a href="manage_bookings.php"><i class="fas fa-ticket-alt"></i> Đặt vé</a></li>
        <li><a href="manage_users.php"><i class="fas fa-users"></i> Người dùng</a></li>
        <li><a href="manage_theatres.php"><i class="fas fa-building"></i> Rạp chiếu</a></li>
        <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Báo cáo</a></li>
        <li><a href="../index.php" target="_blank"><i class="fas fa-home"></i> Xem trang chủ</a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
    </ul>
</div>

<script>
// Highlight active menu
const currentPage = window.location.pathname.split('/').pop();
document.querySelectorAll('.sidebar-menu a').forEach(link => {
    if(link.getAttribute('href') === currentPage) {
        link.classList.add('active');
    }
});
</script>