# Cinema Booking System

Hệ thống đặt vé xem phim trực tuyến được xây dựng bằng PHP và MySQL, cung cấp đầy đủ tính năng dành cho người dùng và quản trị viên. 

---

## Tính năng chính

### Người dùng

* Xem danh sách phim (đang chiếu / sắp chiếu)
* Xem thông tin chi tiết phim và lịch chiếu
* Đặt vé với sơ đồ ghế tương tác
* **🔒 Bảo vệ concurrent booking** - Ngăn chặn nhiều người đặt cùng ghế
* Kiểm tra tình trạng ghế real-time
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

* MySQL 5.7+ / MariaDB
* InnoDB Engine (cho transactions & row-level locking)

**Thư viện hỗ trợ:**

* Font Awesome 5.15.4
* jQuery 3.7.1
* Chart.js (phục vụ biểu đồ thống kê)

---

## 👶 New to GitHub & PHP? Read This First
Nếu bạn là người mới, lưu ý các điểm sau:
- Không thể chạy file `.php` bằng cách double-click
- Bắt buộc phải dùng local web server **XAMPP hoặc MAMP**
- Source code phải nằm trong thư mục `htdocs`
- Cần import database trước khi mở website

➡️ Hãy làm theo từng bước ở phần **🚀 Quick Start (5 phút)** bên dưới

---

## 🚀 Quick Start (5 phút)

### 1️⃣ Clone project
```bash
git clone <your-repo-url>
cd cinema-booking-system
```

### 2️⃣ Import database
- Mở phpMyAdmin: `http://localhost/phpmyadmin`
- Tạo database: `cinema_booking`
- Import file: **`database.sql`**
- ✅ Xong! Database đã có đầy đủ tính năng concurrent booking

### 3️⃣ Cấu hình
Copy và sửa file config:
```bash
cp config.example.php config.php
```

Sửa trong `config.php`:
```php
$host = "localhost";
$user = "root";
$password = "";  // Mật khẩu MySQL của bạn
$database = "cinema_booking";
```

### 4️⃣ Chạy
- User: `http://localhost/cinema-booking-system/`
- Admin: `http://localhost/cinema-booking-system/src/pages/admin/`

**Tài khoản admin:**
- Email: `admin@cinema.com`
- Password: `admin123`

---

## Hướng dẫn cài đặt chi tiết

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

* 🎨 Giao diện dark mode hiện đại
* 📱 Thiết kế responsive tương thích mobile
* 💺 Sơ đồ ghế tương tác theo thời gian thực
* 🔒 **Concurrent booking protection** - Race condition prevention
* 📊 Dashboard quản trị với biểu đồ doanh thu
* 🔐 Bảo mật SQL injection với prepared statements
* ⚡ Transaction-based booking cho data integrity

---

## 🛡️ Concurrent Booking Protection

Hệ thống sử dụng nhiều lớp bảo vệ để ngăn chặn race conditions:

### 1. Database Level
- **UNIQUE constraint** trên `(screening_id, seat_number)`
- **Foreign keys với CASCADE delete**
- **InnoDB engine** cho row-level locking

### 2. Application Level
- **Database transactions** với BEGIN/COMMIT/ROLLBACK
- **SELECT...FOR UPDATE** locking trong transactions
- **Prepared statements** ngăn SQL injection

### 3. User Experience
- **AJAX real-time seat checking** trước khi submit
- **Visual feedback** cho ghế đã được đặt
- **Error handling** rõ ràng
---

## ❓ FAQs

### Q: File nào cần import?
**A:** Chỉ cần `database.sql` - file này đã có đầy đủ tính năng!

### Q: Database có những bảng gì?
**A:** 
- `tbl_bookings` - Thông tin đặt vé
- **`tbl_seat_bookings`** - Lưu từng ghế (ngăn duplicate)
- `tbl_screenings` - Lịch chiếu
- `tbl_movie` - Phim
- `tbl_theatre` - Rạp
- `tbl_registration` - User info
- `tbl_login` - Authentication

---

## 🔧 Troubleshooting

### Lỗi: "Table tbl_seat_bookings doesn't exist"
**Nguyên nhân:** Import file database.sql cũ

**Fix:** 
1. Drop database: `DROP DATABASE cinema_booking;`
2. Tạo lại: `CREATE DATABASE cinema_booking;`
3. Import lại `database.sql` (version mới nhất)

### Lỗi: "Duplicate entry for key 'unique_seat_per_screening'"
**Tốt!** Đây là lỗi MONG MUỐN khi có 2 người đặt cùng ghế.
Nghĩa là concurrent booking protection đang hoạt động! ✅

### Lỗi: Connection failed
**Fix:**
1. Check MySQL đã chạy chưa
2. Check username/password trong `config.php`
3. Check database name đúng chưa

---

## ✅ Setup Checklist

- [ ] Clone project về máy
- [ ] Tạo database `cinema_booking` trong phpMyAdmin
- [ ] Import file `database.sql`
- [ ] Copy và cấu hình `config.php`
- [ ] Chạy website: `http://localhost/cinema-booking-system/`
- [ ] Login với admin account
- [ ] Test booking vé

