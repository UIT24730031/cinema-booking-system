<?php
ob_start();

session_start();
include('../config.php');

ob_end_clean();

if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}

// Xử lý xóa phim
if(isset($_GET['delete'])) {
    $movie_id = mysqli_real_escape_string($con, $_GET['delete']);
    mysqli_query($con, "DELETE FROM tbl_movie WHERE movie_id='$movie_id'");
    echo "<script>alert('Xóa phim thành công!'); window.location='manage_movies.php';</script>";
}

// Xử lý thêm/sửa phim
if(isset($_POST['submit'])) {
    $movie_id = isset($_POST['movie_id']) ? mysqli_real_escape_string($con, $_POST['movie_id']) : '';
    $movie_name = mysqli_real_escape_string($con, $_POST['movie_name']);
    $director = mysqli_real_escape_string($con, $_POST['director']);
    $cast = mysqli_real_escape_string($con, $_POST['cast']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $image = mysqli_real_escape_string($con, $_POST['image']);
    $video_url = mysqli_real_escape_string($con, $_POST['video_url']);
    $duration = mysqli_real_escape_string($con, $_POST['duration']);
    $genre = mysqli_real_escape_string($con, $_POST['genre']);
    $release_date = mysqli_real_escape_string($con, $_POST['release_date']);
    $rating = mysqli_real_escape_string($con, $_POST['rating']);
    $status = mysqli_real_escape_string($con, $_POST['status']);
    
    if(empty($movie_id)) {
        $sql = "INSERT INTO tbl_movie (movie_name, director, cast, description, image, video_url, duration, genre, release_date, rating, status) 
                VALUES ('$movie_name', '$director', '$cast', '$description', '$image', '$video_url', '$duration', '$genre', '$release_date', '$rating', '$status')";
    } else {
        $sql = "UPDATE tbl_movie SET movie_name='$movie_name', director='$director', cast='$cast', description='$description', 
                image='$image', video_url='$video_url', duration='$duration', genre='$genre', release_date='$release_date', 
                rating='$rating', status='$status' WHERE movie_id='$movie_id'";
    }
    
    if(mysqli_query($con, $sql)) {
        echo "<script>alert('Lưu thành công!'); window.location='manage_movies.php';</script>";
    } else {
        echo "<script>alert('Lỗi: ".mysqli_error($con)."');</script>";
    }
}

// Lấy thông tin phim nếu đang sửa
$edit_movie = null;
if(isset($_GET['edit'])) {
    $movie_id = mysqli_real_escape_string($con, $_GET['edit']);
    $edit_qry = mysqli_query($con, "SELECT * FROM tbl_movie WHERE movie_id='$movie_id'");
    $edit_movie = mysqli_fetch_array($edit_qry);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý phim - Admin</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body style="background: #0a0a0a; color: #e0e0e0;">
    <?php include('admin_sidebar.php'); ?>
    
    <div class="main-content" style="margin-left: 250px; padding: 30px;">
        <h2 style="color: #fff; margin-bottom: 30px;">
            <i class="fas fa-film"></i> Quản Lý Phim
        </h2>

        <!-- Form thêm/sửa -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px; margin-bottom: 30px;">
            <h4 style="color: #e50914; margin-bottom: 20px;">
                <?php echo $edit_movie ? 'Sửa phim' : 'Thêm phim mới'; ?>
            </h4>
            <form method="POST" action="">
                <input type="hidden" name="movie_id" value="<?php echo $edit_movie ? $edit_movie['movie_id'] : ''; ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên phim *</label>
                            <input type="text" name="movie_name" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['movie_name']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Đạo diễn</label>
                            <input type="text" name="director" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['director']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Diễn viên</label>
                    <input type="text" name="cast" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['cast']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" class="form-control" rows="3" style="background: #2a2a2a; color: #fff; border: none;"><?php echo $edit_movie ? htmlspecialchars($edit_movie['description']) : ''; ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL hình ảnh *</label>
                            <input type="text" name="image" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['image']) : ''; ?>" required style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>URL trailer</label>
                            <input type="text" name="video_url" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['video_url']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Thời lượng (phút)</label>
                            <input type="number" name="duration" class="form-control" value="<?php echo $edit_movie ? $edit_movie['duration'] : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Thể loại</label>
                            <input type="text" name="genre" class="form-control" value="<?php echo $edit_movie ? htmlspecialchars($edit_movie['genre']) : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Ngày khởi chiếu</label>
                            <input type="date" name="release_date" class="form-control" value="<?php echo $edit_movie ? $edit_movie['release_date'] : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Đánh giá (0-10)</label>
                            <input type="number" step="0.1" name="rating" class="form-control" value="<?php echo $edit_movie ? $edit_movie['rating'] : ''; ?>" style="background: #2a2a2a; color: #fff; border: none;">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control" style="background: #2a2a2a; color: #fff; border: none;">
                        <option value="now_showing" <?php echo ($edit_movie && $edit_movie['status']=='now_showing') ? 'selected' : ''; ?>>Đang chiếu</option>
                        <option value="coming_soon" <?php echo ($edit_movie && $edit_movie['status']=='coming_soon') ? 'selected' : ''; ?>>Sắp chiếu</option>
                        <option value="ended" <?php echo ($edit_movie && $edit_movie['status']=='ended') ? 'selected' : ''; ?>>Đã kết thúc</option>
                    </select>
                </div>
                
                <button type="submit" name="submit" class="btn btn-danger" style="background: #e50914;">
                    <i class="fas fa-save"></i> Lưu
                </button>
                <?php if($edit_movie) { ?>
                    <a href="manage_movies.php" class="btn btn-default">Hủy</a>
                <?php } ?>
            </form>
        </div>

        <!-- Danh sách phim -->
        <div style="background: #1a1a1a; padding: 25px; border-radius: 10px;">
            <h4 style="color: #fff; margin-bottom: 20px;">Danh sách phim</h4>
            <table class="table table-hover" style="color: #e0e0e0;">
                <thead style="background: #2a2a2a;">
                    <tr>
                        <th>ID</th>
                        <th>Hình</th>
                        <th>Tên phim</th>
                        <th>Đạo diễn</th>
                        <th>Thể loại</th>
                        <th>Thời lượng</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $qry = mysqli_query($con, "SELECT * FROM tbl_movie ORDER BY movie_id DESC");
                    while($row = mysqli_fetch_array($qry)) {
                        $status_text = $row['status'] == 'now_showing' ? 'Đang chiếu' : ($row['status'] == 'coming_soon' ? 'Sắp chiếu' : 'Đã kết thúc');
                        $status_class = $row['status'] == 'now_showing' ? 'success' : ($row['status'] == 'coming_soon' ? 'warning' : 'default');
                    ?>
                    <tr>
                        <td><?php echo $row['movie_id']; ?></td>
                        <td><img src="<?php echo $row['image']; ?>" style="width: 50px; height: 75px; object-fit: cover; border-radius: 5px;"></td>
                        <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['director']); ?></td>
                        <td><?php echo htmlspecialchars($row['genre']); ?></td>
                        <td><?php echo $row['duration']; ?> phút</td>
                        <td><span class="label label-<?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                        <td>
                            <a href="?edit=<?php echo $row['movie_id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $row['movie_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>