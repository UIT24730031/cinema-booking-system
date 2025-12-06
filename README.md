# 🎬 Cinema Booking System

Hệ thống đặt vé xem phim online với giao diện hiện đại, tính năng đầy đủ cho cả người dùng và quản trị viên.

## ✨ Tính năng

### 👤 Người dùng
- ✅ Xem danh sách phim (đang chiếu, sắp chiếu)
- ✅ Xem chi tiết phim & lịch chiếu
- ✅ Đặt vé online với sơ đồ ghế tương tác
- ✅ Quản lý lịch sử đặt vé
- ✅ Hủy vé
- ✅ Quản lý hồ sơ cá nhân

### 👨‍💼 Admin
- ✅ Dashboard tổng quan với thống kê
- ✅ Quản lý phim (CRUD)
- ✅ Quản lý lịch chiếu
- ✅ Quản lý đặt vé
- ✅ Quản lý người dùng
- ✅ Quản lý rạp chiếu
- ✅ Báo cáo doanh thu với biểu đồ

## 🛠️ Công nghệ sử dụng

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 3.4.1
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Libraries**: 
  - Font Awesome 5.15.4
  - jQuery 3.7.1
  - Chart.js (cho báo cáo)

## 📦 Cài đặt

### Yêu cầu hệ thống
- XAMPP/MAMP (Apache + MySQL + PHP)
- PHP >= 7.4
- MySQL >= 5.7

### Các bước cài đặt

1. **Clone repository**
\`\`\`bash
git clone https://github.com/your-username/cinema-booking.git
cd cinema-booking
\`\`\`

2. **Import database**
- Mở phpMyAdmin: \`http://localhost/phpmyadmin\`
- Tạo database mới tên \`cinema_booking\`
- Import file \`database.sql\`

3. **Cấu hình database**
- Copy file \`config.example.php\` thành \`config.php\`
- Chỉnh sửa thông tin kết nối:
\`\`\`php
$host = "localhost";
$user = "root";
$password = ""; // Mật khẩu MySQL của bạn
$database = "cinema_booking";
\`\`\`

4. **Truy cập website**
- Trang chủ: \`http://localhost/cinema\`
- Admin: \`http://localhost/cinema/admin\`

## 🔐 Tài khoản mặc định

**Admin:**
- Email: \`admin@cinema.com\`
- Password: \`admin123\`

## 📁 Cấu trúc thư mục

\`\`\`
cinema/
├── admin/              # Trang quản trị
├── css/
├── images/
├── config.php          # Cấu hình database
├── index.php           # Trang chủ
├── schedule.php        # Lịch chiếu
├── booking.php         # Đặt vé
└── database.sql        # File SQL
\`\`\`

## 🎨 Tính năng nổi bật

- 🌑 Dark theme hiện đại (Netflix-inspired)
- 📱 Responsive trên mọi thiết bị
- 🎫 Sơ đồ ghế tương tác real-time
- 📊 Dashboard admin với biểu đồ thống kê
- 🔒 Bảo mật SQL Injection
- ✨ Animation mượt mà

## 📧 Liên hệ

Project Link: [https://github.com/your-username/cinema-booking](https://github.com/your-username/cinema-booking)

---

⭐ Nếu thấy hữu ích, hãy cho project một star nhé!
