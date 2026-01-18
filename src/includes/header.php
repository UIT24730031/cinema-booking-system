<?php
// Start output buffering to prevent "headers already sent" errors
ob_start();

if(session_status() == PHP_SESSION_NONE){
    session_start();
}
include(__DIR__ . '/../../config.php');

// Clear any accidental output from config
ob_end_clean();

// Suppress errors in production
ini_set('display_errors', 0);
error_reporting(0);
?><!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cinema Booking</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['PHP_SELF'], 2); ?>/css/style.css">
</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>                        
      </button>
      <a class="navbar-brand" href="/index.php"><i class="fas fa-film"></i> CINEMA STAR</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <?php
      // Get current page name
      $current_page = basename($_SERVER['PHP_SELF']);
      ?>
      <ul class="nav navbar-nav">
        <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><a href="/index.php">Trang Chủ</a></li>
        <li class="<?php echo ($current_page == 'movies_events.php') ? 'active' : ''; ?>"><a href="/src/pages/movies_events.php">Phim Đang Chiếu</a></li>
        <li class="<?php echo ($current_page == 'schedule.php') ? 'active' : ''; ?>"><a href="/src/pages/schedule.php">Lịch Chiếu</a></li>
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <?php if(isset($_SESSION['user'])){ ?>
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?> <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="/src/pages/profile.php">Hồ sơ cá nhân</a></li>
                    <li><a href="/src/pages/logout.php">Đăng xuất</a></li>
                </ul>
            </li>
        <?php } else { ?>
            <li><a href="/src/pages/registration.php"><span class="glyphicon glyphicon-user"></span> Đăng Ký</a></li>
            <li><a href="/src/pages/login.php"><span class="glyphicon glyphicon-log-in"></span> Đăng Nhập</a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>

<div style="height: 60px;"></div>

<div class="main-container">