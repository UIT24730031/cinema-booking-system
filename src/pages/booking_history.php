<?php 
include(__DIR__ . '/../includes/header.php');
if(!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập!'); window.location='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user'];
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <h2 class="section-title">🎫 Lịch Sử Đặt Vé</h2>
    
    <div class="row">
        <div class="col-md-12">
            <?php
            $qry = mysqli_query($con, "
                SELECT b.*, m.movie_name, m.image, s.show_date, s.show_time, s.screen_name, 
                       t.name as theatre_name, t.address
                FROM tbl_bookings b
                JOIN tbl_screenings s ON b.screening_id = s.screening_id
                JOIN tbl_movie m ON s.movie_id = m.movie_id
                JOIN tbl_theatre t ON s.theatre_id = t.id
                WHERE b.user_id = '$user_id'
                ORDER BY b.booking_date DESC
            ");
            
            if(mysqli_num_rows($qry) > 0) {
                while($booking = mysqli_fetch_array($qry)) {
                    $status_color = $booking['status'] == 'confirmed' ? '#4CAF50' : ($booking['status'] == 'cancelled' ? '#f44336' : '#ff9800');
                    $status_text = $booking['status'] == 'confirmed' ? 'Đã xác nhận' : ($booking['status'] == 'cancelled' ? 'Đã hủy' : 'Chờ xác nhận');
            ?>
                <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid <?php echo $status_color;?>;">
                    <div class="row">
                        <div class="col-md-2">
                            <img src="<?php echo $booking['image'];?>" class="img-responsive" style="border-radius: 8px;" onerror="this.src='https://via.placeholder.com/150x225?text=No+Image';">
                        </div>
                        <div class="col-md-7">
                            <h4 style="color: #fff; margin-top: 0; margin-bottom: 15px;">
                                <?php echo htmlspecialchars($booking['movie_name']);?>
                            </h4>
                            <p style="color: #aaa; margin-bottom: 8px;">
                                <i class="fas fa-barcode" style="color: #e50914;"></i> 
                                <strong>Mã đặt vé:</strong> #<?php echo $booking['booking_id'];?>
                            </p>
                            <p style="color: #aaa; margin-bottom: 8px;">
                                <i class="fas fa-map-marker-alt" style="color: #e50914;"></i> 
                                <strong><?php echo htmlspecialchars($booking['theatre_name']);?></strong>
                            </p>
                            <p style="color: #999; font-size: 13px; margin-bottom: 8px; margin-left: 23px;">
                                <?php echo htmlspecialchars($booking['address']);?>
                            </p>
                            <p style="color: #aaa; margin-bottom: 8px;">
                                <i class="fas fa-calendar" style="color: #e50914;"></i> 
                                <?php echo date('d/m/Y', strtotime($booking['show_date']));?> - 
                                <i class="fas fa-clock" style="color: #e50914;"></i> 
                                <?php echo date('H:i', strtotime($booking['show_time']));?>
                            </p>
                            <p style="color: #aaa; margin-bottom: 8px;">
                                <i class="fas fa-desktop" style="color: #e50914;"></i> 
                                <?php echo htmlspecialchars($booking['screen_name']);?>
                            </p>
                            <p style="color: #aaa; margin-bottom: 8px;">
                                <i class="fas fa-couch" style="color: #e50914;"></i> 
                                <strong>Ghế:</strong> <?php echo htmlspecialchars($booking['seats']);?>
                            </p>
                            <p style="color: #aaa; margin-bottom: 0;">
                                <i class="fas fa-clock" style="color: #e50914;"></i> 
                                <strong>Thời gian đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($booking['booking_date']));?>
                            </p>
                        </div>
                        <div class="col-md-3 text-right">
                            <div style="background: #2a2a2a; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <p style="color: #aaa; margin-bottom: 5px; font-size: 13px;">Tổng tiền</p>
                                <p style="color: #e50914; font-size: 24px; font-weight: bold; margin: 0;">
                                    <?php echo number_format($booking['total_amount'], 0, ',', '.');?>đ
                                </p>
                            </div>
                            <div style="background: <?php echo $status_color;?>; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                                <p style="color: #fff; margin: 0; font-weight: bold; text-align: center;">
                                    <?php echo $status_text;?>
                                </p>
                            </div>
                            <?php if($booking['status'] == 'confirmed' && strtotime($booking['show_date']) > time()) { ?>
                                <a href="cancel_booking.php?id=<?php echo $booking['booking_id'];?>" 
                                   class="btn btn-danger btn-block" 
                                   onclick="return confirm('Bạn có chắc muốn hủy vé này?');"
                                   style="background: #f44336;">
                                    <i class="fas fa-times"></i> Hủy vé
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>
                <div style="text-align: center; padding: 80px 20px; background: #1a1a1a; border-radius: 10px;">
                    <i class="fas fa-ticket-alt" style="font-size: 80px; color: #333; margin-bottom: 20px;"></i>
                    <h3 style="color: #aaa; margin-bottom: 15px;">Chưa có vé nào</h3>
                    <p style="color: #777; margin-bottom: 25px;">Hãy đặt vé xem phim yêu thích của bạn ngay!</p>
                    <a href="/index.php" class="btn btn-danger" style="background: #e50914; padding: 12px 30px;">
                        <i class="fas fa-search"></i> Khám phá phim
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>