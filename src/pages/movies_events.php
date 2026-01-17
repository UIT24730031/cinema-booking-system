<!-- Trang phim đang chiếu -->
<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container" style="margin-top: 30px;">
    <h2 class="section-title">🎬 Phim Đang Chiếu</h2>
    
    <div class="row" style="margin-bottom: 30px;">
        <div class="col-md-12">
            <div style="background: #1a1a1a; padding: 20px; border-radius: 10px;">
                <form method="GET" action="" class="form-inline">
                    <div class="form-group" style="margin-right: 15px;">
                        <label style="color: #aaa; margin-right: 10px;">Thể loại:</label>
                        <select name="genre" class="form-control" style="background: #2a2a2a; color: #fff; border: none;">
                            <option value="">Tất cả</option>
                            <option value="Hành động">Hành động</option>
                            <option value="Phiêu lưu">Phiêu lưu</option>
                            <option value="Khoa học viễn tưởng">Khoa học viễn tưởng</option>
                            <option value="Tội phạm">Tội phạm</option>
                            <option value="Drama">Drama</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-danger" style="background: #e50914;">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php
        $where = "WHERE status='now_showing'";
        
        if(isset($_GET['genre']) && !empty($_GET['genre'])) {
            $genre = mysqli_real_escape_string($con, $_GET['genre']);
            $where .= " AND genre LIKE '%$genre%'";
        }
        
        $qry = mysqli_query($con, "SELECT * FROM tbl_movie $where ORDER BY release_date DESC");
        
        if(mysqli_num_rows($qry) > 0) {
            while($movie = mysqli_fetch_array($qry)) {
        ?>
            <div class="col-md-3 col-sm-6">
                <div class="movie-card">
                    <div class="movie-img-wrap">
                        <img src="<?php echo $movie['image'];?>" alt="<?php echo htmlspecialchars($movie['movie_name']);?>" onerror="this.src='https://via.placeholder.com/300x450?text=No+Image';">
                        <div class="movie-overlay">
                            <a href="movie_details.php?id=<?php echo $movie['movie_id'];?>" class="btn-get-ticket">ĐẶT VÉ</a>
                        </div>
                    </div>
                    <div class="movie-info">
                        <h4><?php echo htmlspecialchars($movie['movie_name']);?></h4>
                        <p><i class="fas fa-user"></i> <?php echo htmlspecialchars(substr($movie['director'], 0, 25));?></p>
                        <p><i class="fas fa-clock"></i> <?php echo $movie['duration'];?> phút</p>
                        <p><i class="fas fa-star" style="color:#ffd700"></i> <?php echo $movie['rating'];?>/10</p>
                    </div>
                </div>
            </div>
        <?php 
            }
        } else {
            echo '<div class="col-md-12"><p class="text-center" style="color:#999; padding: 50px;">Không tìm thấy phim nào phù hợp.</p></div>';
        }
        ?>
    </div>
</div>

<?php include(__DIR__ . '/../includes/footer.php'); ?>