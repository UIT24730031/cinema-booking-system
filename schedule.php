<?php include('header.php'); ?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <h2 class="section-title">📅 Lịch Chiếu Phim</h2>
    
    <!-- Bộ lọc -->
    <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
        <form method="GET" action="" class="form-inline">
            <div class="form-group" style="margin-right: 15px;">
                <label style="color: #aaa; margin-right: 10px;">Ngày chiếu:</label>
                <input type="date" name="date" class="form-control" value="<?php echo isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); ?>" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
            </div>
            <div class="form-group" style="margin-right: 15px;">
                <label style="color: #aaa; margin-right: 10px;">Rạp:</label>
                <select name="theatre" class="form-control" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                    <option value="">Tất cả rạp</option>
                    <?php
                    $theatres = mysqli_query($con, "SELECT * FROM tbl_theatre ORDER BY name");
                    while($t = mysqli_fetch_array($theatres)) {
                        $selected = (isset($_GET['theatre']) && $_GET['theatre'] == $t['id']) ? 'selected' : '';
                        echo '<option value="'.$t['id'].'" '.$selected.'>'.htmlspecialchars($t['name']).'</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin-right: 15px;">
                <label style="color: #aaa; margin-right: 10px;">Phim:</label>
                <select name="movie" class="form-control" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                    <option value="">Tất cả phim</option>
                    <?php
                    $movies = mysqli_query($con, "SELECT movie_id, movie_name FROM tbl_movie WHERE status='now_showing' ORDER BY movie_name");
                    while($m = mysqli_fetch_array($movies)) {
                        $selected = (isset($_GET['movie']) && $_GET['movie'] == $m['movie_id']) ? 'selected' : '';
                        echo '<option value="'.$m['movie_id'].'" '.$selected.'>'.htmlspecialchars($m['movie_name']).'</option>';
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-danger" style="background: #e50914; height: 45px; padding: 0 30px;">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
            <a href="schedule.php" class="btn btn-default" style="height: 45px; padding: 0 20px; line-height: 45px;">
                <i class="fas fa-redo"></i> Reset
            </a>
        </form>
    </div>

    <!-- Lịch nhanh 7 ngày -->
    <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 30px; overflow-x: auto;">
        <div style="display: flex; gap: 10px; min-width: max-content;">
            <?php
            for($i = 0; $i < 7; $i++) {
                $day = date('Y-m-d', strtotime("+$i days"));
                $day_name = date('l', strtotime($day));
                $day_names = [
                    'Monday' => 'Thứ 2',
                    'Tuesday' => 'Thứ 3',
                    'Wednesday' => 'Thứ 4',
                    'Thursday' => 'Thứ 5',
                    'Friday' => 'Thứ 6',
                    'Saturday' => 'Thứ 7',
                    'Sunday' => 'Chủ nhật'
                ];
                $is_active = (isset($_GET['date']) && $_GET['date'] == $day) || (!isset($_GET['date']) && $i == 0);
                $active_style = $is_active ? 'background: #e50914; transform: scale(1.05);' : 'background: #2a2a2a;';
            ?>
            <a href="?date=<?php echo $day; ?><?php echo isset($_GET['theatre']) ? '&theatre='.$_GET['theatre'] : ''; ?><?php echo isset($_GET['movie']) ? '&movie='.$_GET['movie'] : ''; ?>" 
               style="flex: 0 0 150px; <?php echo $active_style; ?> padding: 15px; border-radius: 8px; text-align: center; text-decoration: none; transition: all 0.3s;">
                <div style="color: #fff; font-weight: bold; font-size: 16px; margin-bottom: 5px;">
                    <?php echo $day_names[$day_name]; ?>
                </div>
                <div style="color: #ddd; font-size: 14px;">
                    <?php echo date('d/m', strtotime($day)); ?>
                </div>
            </a>
            <?php } ?>
        </div>
    </div>

    <?php
    // Xây dựng query
    $where = "WHERE s.show_date >= CURDATE()";
    
    $selected_date = isset($_GET['date']) ? mysqli_real_escape_string($con, $_GET['date']) : date('Y-m-d');
    $where .= " AND s.show_date = '$selected_date'";
    
    if(isset($_GET['theatre']) && !empty($_GET['theatre'])) {
        $theatre = mysqli_real_escape_string($con, $_GET['theatre']);
        $where .= " AND s.theatre_id = '$theatre'";
    }
    
    if(isset($_GET['movie']) && !empty($_GET['movie'])) {
        $movie = mysqli_real_escape_string($con, $_GET['movie']);
        $where .= " AND s.movie_id = '$movie'";
    }
    
    // Lấy danh sách phim có lịch chiếu
    $movies_with_schedule = mysqli_query($con, "
        SELECT DISTINCT m.*, 
               (SELECT COUNT(*) FROM tbl_screenings WHERE movie_id = m.movie_id AND show_date = '$selected_date') as screening_count
        FROM tbl_movie m
        JOIN tbl_screenings s ON m.movie_id = s.movie_id
        $where
        ORDER BY m.movie_name
    ");
    
    if(mysqli_num_rows($movies_with_schedule) > 0) {
        while($movie = mysqli_fetch_array($movies_with_schedule)) {
    ?>
    
    <!-- Card phim -->
    <div style="background: #1a1a1a; border-radius: 10px; margin-bottom: 30px; overflow: hidden;">
        <div class="row" style="margin: 0;">
            <!-- Poster phim -->
            <div class="col-md-2" style="padding: 0;">
                <img src="<?php echo $movie['image']; ?>" style="width: 100%; height: 100%; object-fit: cover; min-height: 300px;" onerror="this.src='https://via.placeholder.com/200x300?text=No+Image';">
            </div>
            
            <!-- Thông tin phim -->
            <div class="col-md-10" style="padding: 25px;">
                <h3 style="color: #fff; margin-top: 0; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($movie['movie_name']); ?>
                    <span class="label label-danger" style="background: #e50914; font-size: 12px; margin-left: 10px;">
                        <?php echo $movie['screening_count']; ?> suất chiếu
                    </span>
                </h3>
                
                <div style="margin-bottom: 20px;">
                    <span style="color: #aaa; margin-right: 20px;">
                        <i class="fas fa-clock" style="color: #e50914;"></i> 
                        <?php echo $movie['duration']; ?> phút
                    </span>
                    <span style="color: #aaa; margin-right: 20px;">
                        <i class="fas fa-film" style="color: #e50914;"></i> 
                        <?php echo htmlspecialchars($movie['genre']); ?>
                    </span>
                    <span style="color: #aaa;">
                        <i class="fas fa-star" style="color: #ffd700;"></i> 
                        <?php echo $movie['rating']; ?>/10
                    </span>
                </div>
                
                <?php
                // Lấy lịch chiếu theo rạp
                $screenings_by_theatre = mysqli_query($con, "
                    SELECT s.*, t.name as theatre_name, t.address
                    FROM tbl_screenings s
                    JOIN tbl_theatre t ON s.theatre_id = t.id
                    WHERE s.movie_id = '{$movie['movie_id']}' AND s.show_date = '$selected_date'
                    " . (isset($_GET['theatre']) && !empty($_GET['theatre']) ? "AND s.theatre_id = '{$_GET['theatre']}'" : "") . "
                    ORDER BY t.name, s.show_time
                ");
                
                $current_theatre = '';
                while($screening = mysqli_fetch_array($screenings_by_theatre)) {
                    if($current_theatre != $screening['theatre_name']) {
                        if($current_theatre != '') echo '</div></div>'; // Đóng rạp trước
                        $current_theatre = $screening['theatre_name'];
                ?>
                
                <!-- Rạp chiếu -->
                <div style="border-top: 1px solid #333; padding-top: 20px; margin-top: 20px;">
                    <h5 style="color: #e50914; margin-bottom: 10px;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($screening['theatre_name']); ?>
                    </h5>
                    <p style="color: #999; font-size: 13px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($screening['address']); ?>
                    </p>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                
                <?php } ?>
                
                <!-- Suất chiếu -->
                <div style="background: #2a2a2a; border-radius: 8px; padding: 12px 20px; min-width: 180px; transition: all 0.3s;" 
                     onmouseover="this.style.background='#e50914'; this.style.transform='translateY(-3px)';" 
                     onmouseout="this.style.background='#2a2a2a'; this.style.transform='translateY(0)';">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="color: #fff; font-size: 18px; font-weight: bold;">
                            <?php echo date('H:i', strtotime($screening['show_time'])); ?>
                        </span>
                        <span style="color: <?php echo $screening['available_seats'] > 50 ? '#4CAF50' : ($screening['available_seats'] > 20 ? '#FF9800' : '#f44336'); ?>; font-size: 12px;">
                            <i class="fas fa-couch"></i> <?php echo $screening['available_seats']; ?>
                        </span>
                    </div>
                    <div style="font-size: 12px; color: #aaa; margin-bottom: 8px;">
                        <?php echo htmlspecialchars($screening['screen_name']); ?>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #fff; font-weight: bold;">
                            <?php echo number_format($screening['price'], 0, ',', '.'); ?>đ
                        </span>
                        <?php if(isset($_SESSION['user'])) { ?>
                            <a href="booking.php?screening_id=<?php echo $screening['screening_id']; ?>" 
                               style="background: rgba(255,255,255,0.2); color: #fff; padding: 4px 12px; border-radius: 5px; font-size: 12px; text-decoration: none; font-weight: bold;">
                                ĐẶT VÉ
                            </a>
                        <?php } else { ?>
                            <a href="login.php" 
                               style="background: rgba(255,255,255,0.2); color: #fff; padding: 4px 12px; border-radius: 5px; font-size: 12px; text-decoration: none;">
                                Đăng nhập
                            </a>
                        <?php } ?>
                    </div>
                </div>
                
                <?php 
                }
                echo '</div></div>'; // Đóng rạp cuối cùng
                ?>
                
            </div>
        </div>
    </div>
    
    <?php 
        }
    } else {
    ?>
    
    <!-- Không có lịch chiếu -->
    <div style="text-align: center; padding: 80px 20px; background: #1a1a1a; border-radius: 10px;">
        <i class="fas fa-calendar-times" style="font-size: 80px; color: #333; margin-bottom: 20px;"></i>
        <h3 style="color: #aaa; margin-bottom: 15px;">Không có lịch chiếu</h3>
        <p style="color: #777; margin-bottom: 25px;">
            <?php 
            if(isset($_GET['date'])) {
                echo 'Không có suất chiếu nào vào ngày ' . date('d/m/Y', strtotime($_GET['date']));
            } else {
                echo 'Hiện tại chưa có lịch chiếu nào phù hợp với bộ lọc của bạn';
            }
            ?>
        </p>
        <a href="schedule.php" class="btn btn-danger" style="background: #e50914; padding: 12px 30px;">
            <i class="fas fa-redo"></i> Xem tất cả lịch chiếu
        </a>
    </div>
    
    <?php } ?>
</div>

<style>
/* Smooth scrolling cho thanh ngày */
div[style*="overflow-x: auto"]::-webkit-scrollbar {
    height: 8px;
}

div[style*="overflow-x: auto"]::-webkit-scrollbar-track {
    background: #1a1a1a;
    border-radius: 10px;
}

div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb {
    background: #e50914;
    border-radius: 10px;
}

div[style*="overflow-x: auto"]::-webkit-scrollbar-thumb:hover {
    background: #b20710;
}

/* Responsive */
@media (max-width: 768px) {
    .form-inline .form-group {
        display: block;
        width: 100%;
        margin-bottom: 15px;
        margin-right: 0 !important;
    }
    
    .form-inline .form-control,
    .form-inline .btn {
        width: 100%;
    }
}
</style>

<?php include('footer.php'); ?>