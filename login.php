<?php include('header.php'); ?>

<div class="container" style="padding: 50px 0;">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div style="background: #1f1f1f; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                <h2 class="text-center" style="color: #e50914; font-weight: bold; margin-bottom: 30px;">ĐĂNG NHẬP</h2>
                <form action="process_login.php" method="post">
                    <div class="form-group">
                        <label style="color: #aaa;">Email</label>
                        <input name="Email" type="text" class="form-control" style="background: #333; border: none; color: white; height: 45px;" required/>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Mật khẩu</label>
                        <input name="Password" type="password" class="form-control" style="background: #333; border: none; color: white; height: 45px;" required />
                    </div>
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn btn-danger btn-block" style="background: #e50914; height: 45px; font-weight: bold; font-size: 16px;">Đăng Nhập</button>
                    </div>
                    <p class="text-center" style="color: #777;">Chưa có tài khoản? <a href="registration.php" style="color: #fff;">Đăng ký ngay</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>