<!-- Chi tiết phim và đặt vé -->
<?php 
include(__DIR__ . '/../includes/header.php'); 

if(!isset($_GET['id'])) {
    header('location: /index.php');
    exit;
}

$movie_id = mysqli_real_escape_string($con, $_GET['id']);
$movie_qry = mysqli_query($con, "SELECT * FROM tbl_movie WHERE movie_id='$movie_id'");

if(mysqli_num_rows($movie_qry) == 0) {
    echo "<script>alert('Phim không tồn tại!'); window.location='index.php';</script>";
    exit;
}

$movie = mysqli_fetch_array($movie_qry);
?>

<div class="container" style="margin-top: 30px;">
    <div class="row">
        <!-- Poster phim -->
        <div class="col-md-4">
            <div style="position: sticky; top: 80px;">
                <img src="<?php echo $movie['image'];?>" class="img-responsive" style="width: 100%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.7);" onerror="this.src='https://via.placeholder.com/400x600?text=No+Image';">
            </div>
        </div>
        
        <!-- Thông tin phim -->
        <div class="col-md-8">
            <h1 style="color: #fff; font-weight: 700; margin-bottom: 20px;"><?php echo htmlspecialchars($movie['movie_name']);?></h1>
            
            <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-star" style="color: #ffd700;"></i> 
                            <strong style="color: #fff;">Đánh giá:</strong> <?php echo $movie['rating'];?>/10
                        </p>
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-clock" style="color: #e50914;"></i> 
                            <strong style="color: #fff;">Thời lượng:</strong> <?php echo $movie['duration'];?> phút
                        </p>
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-film" style="color: #e50914;"></i> 
                            <strong style="color: #fff;">Thể loại:</strong> <?php echo htmlspecialchars($movie['genre']);?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-calendar-alt" style="color: #e50914;"></i> 
                            <strong style="color: #fff;">Khởi chiếu:</strong> <?php echo date('d/m/Y', strtotime($movie['release_date']));?>
                        </p>
                        <p style="color: #aaa; margin-bottom: 15px;">
                            <i class="fas fa-user-tie" style="color: #e50914;"></i> 
                            <strong style="color: #fff;">Đạo diễn:</strong> <?php echo htmlspecialchars($movie['director']);?>
                        </p>
                    </div>
                </div>
            </div>
            
            <h3 style="color: #fff; margin-bottom: 15px;">Diễn viên</h3>
            <p style="color: #ccc; line-height: 1.8; margin-bottom: 25px;"><?php echo htmlspecialchars($movie['cast']);?></p>
            
            <h3 style="color: #fff; margin-bottom: 15px;">Nội dung phim</h3>
            <p style="color: #ccc; line-height: 1.8; text-align: justify; margin-bottom: 25px;">
                <?php echo nl2br(htmlspecialchars($movie['description']));?>
            </p>
            
            <?php if(!empty($movie['video_url'])) { ?>
            <a href="<?php echo $movie['video_url'];?>" target="_blank" class="btn btn-warning" style="margin-right: 10px;">
                <i class="fas fa-play"></i> Xem Trailer
            </a>
            <?php } ?>
        </div>
    </div>
    
    <!-- Lịch chiếu -->
    <div class="row" style="margin-top: 50px;">
        <div class="col-md-12">
            <h2 class="section-title">📅 Lịch Chiếu</h2>
            
            <?php
            $screening_qry = mysqli_query($con, "
                SELECT s.*, t.name as theatre_name, t.address 
                FROM tbl_screenings s
                JOIN tbl_theatre t ON s.theatre_id = t.id
                WHERE s.movie_id = '$movie_id' AND s.show_date >= CURDATE()
                ORDER BY s.show_date, s.show_time
            ");
            
            if(mysqli_num_rows($screening_qry) > 0) {
                $current_date = '';
                while($screen = mysqli_fetch_array($screening_qry)) {
                    if($current_date != $screen['show_date']) {
                        if($current_date != '') echo '</div></div>';
                        $current_date = $screen['show_date'];
                        echo '<div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 20px;">';
                        echo '<h4 style="color: #e50914; margin-bottom: 20px;"><i class="fas fa-calendar"></i> ' . date('d/m/Y - l', strtotime($current_date)) . '</h4>';
                        echo '<div class="row">';
                    }
            ?>
                    <div class="col-md-6" style="margin-bottom: 15px;">
                        <div style="background: #2a2a2a; padding: 15px; border-radius: 8px;">
                            <h5 style="color: #fff; margin-bottom: 10px;">
                                <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($screen['theatre_name']);?>
                            </h5>
                            <p style="color: #999; font-size: 13px; margin-bottom: 10px;">
                                <?php echo htmlspecialchars($screen['address']);?>
                            </p>
                            <div style="border-top: 1px solid #444; padding-top: 10px; margin-top: 10px;">
                                <span style="color: #aaa; margin-right: 15px;">
                                    <i class="fas fa-clock"></i> <?php echo date('H:i', strtotime($screen['show_time']));?>
                                </span>
                                <span style="color: #aaa; margin-right: 15px;">
                                    <i class="fas fa-desktop"></i> <?php echo htmlspecialchars($screen['screen_name']);?>
                                </span>
                                <span style="color: #4CAF50;">
                                    <i class="fas fa-couch"></i> <?php echo $screen['available_seats'];?> ghế
                                </span>
                            </div>
                            <div style="margin-top: 15px;">
                                <span style="color: #fff; font-size: 18px; font-weight: bold;">
                                    <?php echo number_format($screen['price'], 0, ',', '.');?>đ
                                </span>
                                <?php if(isset($_SESSION['user'])) { ?>
                                    <a href="booking.php?screening_id=<?php echo $screen['screening_id'];?>" class="btn btn-danger pull-right" style="background: #e50914;">
                                        <i class="fas fa-ticket-alt"></i> Đặt vé
                                    </a>
                                <?php } else { ?>
                                    <a href="login.php" class="btn btn-warning pull-right">
                                        Đăng nhập để đặt vé
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
            <?php
                }
                echo '</div></div>';
            } else {
                echo '<p style="text-align: center; color: #999; padding: 30px;">Hiện tại chưa có lịch chiếu cho phim này.</p>';
            }
            ?>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>