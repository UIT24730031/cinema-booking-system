<?php include('header.php'); ?>

<div class="row">
    <div class="col-md-6 col-md-offset-3" style="margin-top: 80px;">
        <div class="panel panel-default" style="background: #1a1a1a; border: none;">
            <div class="panel-heading text-center" style="background: #2a2a2a; color: #e50914; border: none; font-size: 20px; font-weight: bold;">
                ĐĂNG KÝ TÀI KHOẢN
            </div>
            <div class="panel-body" style="background: #1a1a1a; padding: 30px;">
                
                <!-- Alert messages -->
                <div id="register-alert" style="display: none; margin-bottom: 20px;"></div>
                
                <form id="registerForm">
                    <div class="form-group">
                        <label style="color: #aaa;">Họ tên:</label>
                        <input type="text" name="name" id="regName" class="form-control" required style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Tuổi:</label>
                        <input type="number" name="age" id="regAge" class="form-control" required min="1" max="120" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Giới tính:</label>
                        <select name="gender" id="regGender" class="form-control" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                            <option value="Male">Nam</option>
                            <option value="Female">Nữ</option>
                            <option value="Other">Khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Số điện thoại:</label>
                        <input type="text" name="phone" id="regPhone" class="form-control" required pattern="[0-9]{10}" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                        <small style="color: #777;">Ví dụ: 0123456789</small>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Email:</label>
                        <input type="email" name="email" id="regEmail" class="form-control" required style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Mật khẩu:</label>
                        <input type="password" name="password" id="regPassword" class="form-control" required minlength="6" style="background: #2a2a2a; color: #fff; border: none; height: 45px;">
                        <small style="color: #777;">Tối thiểu 6 ký tự</small>
                    </div>
                    <button type="submit" id="registerBtn" class="btn btn-primary btn-block" style="background: #e50914; border: none; height: 45px; font-weight: bold; margin-top: 20px;">
                        Đăng Ký
                    </button>
                    <p class="text-center" style="color: #777; margin-top: 15px;">
                        Đã có tài khoản? <a href="login.php" style="color: #fff;">Đăng nhập ngay</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('registerBtn');
    const alert = document.getElementById('register-alert');
    
    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    // Hide previous alerts
    alert.style.display = 'none';
    
    // Get form data
    const formData = new FormData(this);
    
    // Send AJAX request
    fetch('process_registration.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            // Show success message
            alert.className = 'alert alert-success';
            alert.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            alert.style.display = 'block';
            
            // Reset form
            document.getElementById('registerForm').reset();
            
            // Redirect to login after 2 seconds
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            // Show error message
            alert.className = 'alert alert-danger';
            alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            alert.style.display = 'block';
            
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = 'Đăng Ký';
            
            // Scroll to alert
            alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert.className = 'alert alert-danger';
        alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Đã xảy ra lỗi! Vui lòng thử lại.';
        alert.style.display = 'block';
        
        btn.disabled = false;
        btn.innerHTML = 'Đăng Ký';
    });
});
</script>

<?php include('footer.php'); ?>
