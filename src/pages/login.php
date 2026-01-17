<?php include(__DIR__ . '/../includes/header.php'); ?>

<div class="container" style="padding: 80px 0 50px 0;">
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div style="background: #1f1f1f; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                <h2 class="text-center" style="color: #e50914; font-weight: bold; margin-bottom: 30px;">ĐĂNG NHẬP</h2>
                
                <!-- Alert messages -->
                <div id="login-alert" style="display: none; margin-bottom: 20px;"></div>
                
                <form id="loginForm">
                    <div class="form-group">
                        <label style="color: #aaa;">Email</label>
                        <input name="Email" type="email" id="loginEmail" class="form-control" style="background: #333; border: none; color: white; height: 45px;" required/>
                    </div>
                    <div class="form-group">
                        <label style="color: #aaa;">Mật khẩu</label>
                        <input name="Password" type="password" id="loginPassword" class="form-control" style="background: #333; border: none; color: white; height: 45px;" required />
                    </div>
                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" id="loginBtn" class="btn btn-danger btn-block" style="background: #e50914; height: 45px; font-weight: bold; font-size: 16px;">
                            Đăng Nhập
                        </button>
                    </div>
                    <p class="text-center" style="color: #777;">Chưa có tài khoản? <a href="registration.php" style="color: #fff;">Đăng ký ngay</a></p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('loginBtn');
    const alert = document.getElementById('login-alert');
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;
    
    // Disable button
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
    
    // Hide previous alerts
    alert.style.display = 'none';
    
    // Send AJAX request
    const formData = new FormData();
    formData.append('Email', email);
    formData.append('Password', password);
    
    fetch('../process/process_login.php', {
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
            
            // Redirect after 1 second
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1000);
        } else {
            // Show error message
            alert.className = 'alert alert-danger';
            alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
            alert.style.display = 'block';
            
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = 'Đăng Nhập';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert.className = 'alert alert-danger';
        alert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Đã xảy ra lỗi! Vui lòng thử lại.';
        alert.style.display = 'block';
        
        btn.disabled = false;
        btn.innerHTML = 'Đăng Nhập';
    });
});
</script>

<?php include(__DIR__ . '/../includes/footer.php'); ?>
