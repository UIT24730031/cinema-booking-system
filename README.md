# Cinema Booking System

Hệ thống đặt vé xem phim trực tuyến được xây dựng bằng PHP và MySQL, cung cấp đầy đủ tính năng dành cho người dùng và quản trị viên. 

---

## Tính năng chính

### Người dùng

* Xem danh sách phim (đang chiếu / sắp chiếu)
* Xem thông tin chi tiết phim và lịch chiếu
* Đặt vé với sơ đồ ghế tương tác
* Quản lý lịch sử đặt vé
* Hủy vé trực tuyến
* Cập nhật thông tin hồ sơ cá nhân

### Quản trị viên (Admin)

* Dashboard tổng quan với biểu đồ thống kê
* Quản lý phim (CRUD)
* Quản lý lịch chiếu
* Quản lý đặt vé
* Quản lý người dùng
* Quản lý rạp chiếu
* Báo cáo doanh thu theo thời gian

---

## Công nghệ sử dụng

**Frontend:**

* HTML5, CSS3, JavaScript
* Bootstrap 3.4.1

**Backend:**

* PHP 7.4+

**Database:**

* MySQL 5.7+

**Thư viện hỗ trợ:**

* Font Awesome 5.15.4
* jQuery 3.7.1
* Chart.js (phục vụ biểu đồ thống kê)

---

## Hướng dẫn cài đặt

### Yêu cầu hệ thống

* XAMPP hoặc MAMP (Apache + MySQL + PHP)
* PHP >= 7.4
* MySQL >= 5.7

### Quy trình cài đặt

**1. Clone repository**

```bash
git clone https://github.com/your-username/cinema-booking.git
cd cinema-booking
```

**2. Import cơ sở dữ liệu**

* Truy cập phpMyAdmin: `http://localhost/phpmyadmin`
* Tạo database mới: `cinema_booking`
* Import file `database.sql`

**3. Cấu hình kết nối database**

* Sao chép file `config.example.php` thành `config.php`
* Chỉnh sửa thông tin:

```php
$host = "localhost";
$user = "root";
$password = "";  // Mật khẩu MySQL của bạn
$database = "cinema_booking";
```

**4. Khởi chạy hệ thống**

* Giao diện người dùng: `http://localhost/cinema`
* Trang quản trị: `http://localhost/cinema/admin`

---

## Tài khoản mặc định

**Admin:**
Email: `admin@cinema.com`
Password: `admin123`

---

## Cấu trúc thư mục

```
cinema/
├── admin/           # Giao diện & chức năng quản trị
├── css/
├── images/
├── config.php       # File cấu hình kết nối database
├── index.php        # Trang chủ
├── schedule.php     # Lịch chiếu
├── booking.php      # Đặt vé
└── database.sql     # Cấu trúc và dữ liệu mẫu của hệ thống

```

---

## Tính năng nổi bật

* Giao diện dark mode hiện đại
* Thiết kế responsive tương thích mobile
* Sơ đồ ghế tương tác theo thời gian thực
* Dashboard quản trị với biểu đồ doanh thu

