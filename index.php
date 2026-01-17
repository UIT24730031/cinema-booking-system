<?php include('src/includes/header.php'); ?>

<div class="hero-section">
    <div class="hero-content">
        <h1>Thế giới điện ảnh trong tầm tay</h1>
        <p>Đặt vé xem phim bom tấn mới nhất ngay hôm nay</p>
        <a href="#movies-list" class="btn-booking">ĐẶT VÉ NGAY</a>
    </div>
</div>

<div class="container" id="movies-list">
    <h3 class="section-title">🔥 Phim Đang Chiếu</h3>
    
    <div class="row">
        <?php
        // Lấy phim đang chiếu
        $qry = mysqli_query($con, "SELECT * FROM tbl_movie WHERE status='now_showing' ORDER BY release_date DESC LIMIT 8");
        
        if(mysqli_num_rows($qry) > 0) {
            while($movie = mysqli_fetch_array($qry)) {
        ?>
            <div class="col-md-3 col-sm-6">
                <div class="movie-card">
                    <div class="movie-img-wrap">
                        <img src="<?php echo $movie['image'];?>" alt="<?php echo htmlspecialchars($movie['movie_name']);?>" onerror="this.src='https://via.placeholder.com/300x450?text=No+Image';">
                        <div class="movie-overlay">
                            <a href="movie_details.php?id=<?php echo $movie['movie_id'];?>" class="btn-get-ticket">CHI TIẾT</a>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h4><?php echo htmlspecialchars($movie['movie_name']);?></h4>
                        <p><i class="fas fa-clock"></i> <?php echo $movie['duration'];?> phút</p>
                        <p><i class="fas fa-star" style="color:#ffd700"></i> <?php echo $movie['rating'];?>/10</p>
                        <p><i class="fas fa-film"></i> <?php echo htmlspecialchars($movie['genre']);?></p>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="col-md-12"><p class="text-center" style="color:#999;">Hiện chưa có phim nào đang chiếu.</p></div>';
        }
        ?>
    </div>

    <h3 class="section-title" style="margin-top: 60px;">⏳ Phim Sắp Chiếu</h3>
    <div class="row">
         <?php
            // Lấy phim sắp chiếu
            $qry2 = mysqli_query($con, "SELECT * FROM tbl_movie WHERE status='coming_soon' ORDER BY release_date DESC LIMIT 4");
            
            if(mysqli_num_rows($qry2) > 0) {
                while($movie = mysqli_fetch_array($qry2)) {
         ?>
            <div class="col-md-3 col-sm-6">
                <div class="movie-card">
                    <div class="movie-img-wrap">
                        <img src="<?php echo $movie['image'];?>" alt="<?php echo htmlspecialchars($movie['movie_name']);?>" onerror="this.src='https://via.placeholder.com/300x450?text=Coming+Soon';">
                        <div class="movie-overlay">
                            <?php if(!empty($movie['video_url'])) { ?>
                                <a href="<?php echo $movie['video_url'];?>" target="_blank" class="btn-get-ticket">
                                    <i class="fas fa-play"></i> TRAILER
                                </a>
                            <?php } else { ?>
                                <a href="src/pages/movie_details.php?id=<?php echo $movie['movie_id'];?>" class="btn-get-ticket">CHI TIẾT</a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h4><?php echo htmlspecialchars($movie['movie_name']);?></h4>
                        <p><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($movie['release_date']));?></p>
                        <p><i class="fas fa-film"></i> <?php echo htmlspecialchars($movie['genre']);?></p>
                    </div>
                </div>
            </div>
         <?php 
                }
            } else {
                echo '<div class="col-md-12"><p class="text-center" style="color:#999;">Hiện chưa có phim nào sắp chiếu.</p></div>';
            }
         ?>
    </div>

    <!-- Tin tức/Khuyến mãi -->
    <h3 class="section-title" style="margin-top: 60px;">📰 Tin Tức & Khuyến Mãi</h3>
    <div class="owl-carousel owl-theme news-slider">
        <?php
        $news_qry = mysqli_query($con, "SELECT * FROM tbl_news ORDER BY news_date DESC");
        if(mysqli_num_rows($news_qry) > 0) {
            while($news = mysqli_fetch_array($news_qry)) {
        ?>
            <div class="item">
                <div class="movie-card">
                    <div class="movie-img-wrap" style="height: 250px;">
                        <img src="<?php echo $news['attachment'];?>" alt="<?php echo htmlspecialchars($news['name']);?>" onerror="this.src='https://via.placeholder.com/400x250?text=News';">
                        <div class="movie-overlay">
                            <a href="#" class="btn-get-ticket">XEM THÊM</a>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h4><?php echo htmlspecialchars($news['name']);?></h4>
                        <p><i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($news['news_date']));?></p>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="item"><p class="text-center" style="color:#999;">Hiện chưa có tin tức nào.</p></div>';
        }
        ?>
    </div>
    
    <script>
    $(document).ready(function(){
        $('.news-slider').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                768: {
                    items: 3
                },
                992: {
                    items: 4
                }
            }
        });
    });
    </script>
</div>

<?php include('src/includes/footer.php'); ?>