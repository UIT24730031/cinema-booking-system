<?php include('header.php'); ?>
<div class="row">
    <div class="col-md-6 col-md-offset-3" style="margin-top: 30px;">
        <div class="panel panel-default">
            <div class="panel-heading text-center">ĐĂNG KÝ TÀI KHOẢN</div>
            <div class="panel-body">
                <form action="process_registration.php" method="post">
                    <div class="form-group">
                        <label>Họ tên:</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Tuổi:</label>
                        <input type="number" name="age" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Giới tính:</label>
                        <select name="gender" class="form-control">
                            <option value="Male">Nam</option>
                            <option value="Female">Nữ</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại:</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu:</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>