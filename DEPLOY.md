# 🚀 Hướng Dẫn Deploy Lên Railway.app

## 📋 Yêu Cầu

- Tài khoản GitHub (đã push code lên repo)
- Tài khoản Railway.app (đăng ký miễn phí tại https://railway.app)

---

## 🔧 Bước 1: Chuẩn Bị Code

### 1.1. Commit và Push code lên GitHub

```bash
git add .
git commit -m "Add Railway deployment config"
git push origin main
```

---

## ☁️ Bước 2: Deploy Lên Railway

### 2.1. Tạo Project Mới

1. Đăng nhập vào https://railway.app
2. Click **"New Project"**
3. Chọn **"Deploy from GitHub repo"**
4. Chọn repository: `UIT24730031/cinema-booking-system`
5. Click **"Deploy Now"**

### 2.2. Thêm MySQL Database

1. Trong project vừa tạo, click **"+ New"**
2. Chọn **"Database"** → **"Add MySQL"**
3. Railway sẽ tự động tạo MySQL instance
4. Đợi 1-2 phút để MySQL khởi động

### 2.3. Connect Database với Application

Railway sẽ tự động tạo các biến môi trường:
- `MYSQL_HOST`
- `MYSQL_USER`
- `MYSQL_PASSWORD`
- `MYSQL_DATABASE`
- `MYSQL_PORT`

**Code `config.php` đã được tự động config để đọc các biến này!** ✅

### 2.4. Import Database Schema

**Cách 1: Sử dụng Railway CLI (Khuyến nghị)**

```bash
# Cài Railway CLI
npm i -g @railway/cli

# Login
railway login

# Link project
railway link

# Connect vào MySQL
railway connect MySQL

# Sau đó paste nội dung file database.sql vào terminal
```

**Cách 2: Sử dụng MySQL Client**

1. Trong Railway dashboard, click vào **MySQL service**
2. Tab **"Connect"** → copy connection string
3. Sử dụng MySQL Workbench hoặc command line:

```bash
mysql -h <MYSQL_HOST> -P <MYSQL_PORT> -u <MYSQL_USER> -p<MYSQL_PASSWORD> <MYSQL_DATABASE> < database.sql
```

**Cách 3: Sử dụng phpMyAdmin Plugin (Dễ nhất)**

1. Trong Railway MySQL service, click **"+ New"** → **"Template"**
2. Search "phpMyAdmin"
3. Add phpMyAdmin template
4. Connect với MySQL credentials
5. Import file `database.sql` qua giao diện web

---

## 🌐 Bước 3: Cấu Hình Domain

### 3.1. Lấy Public URL

1. Click vào **PHP service** (không phải MySQL)
2. Tab **"Settings"** → **"Networking"**
3. Click **"Generate Domain"**
4. Railway sẽ tạo domain dạng: `your-app.up.railway.app`

### 3.2. Test Website

Truy cập: `https://your-app.up.railway.app`

**Tài khoản Admin mặc định:**
- Email: `admin@cinema.com`
- Password: `admin123`

---

## 🔍 Bước 4: Kiểm Tra & Debug

### 4.1. Xem Logs

1. Trong Railway dashboard
2. Click vào PHP service
3. Tab **"Deployments"** → Click vào deployment mới nhất
4. Xem **"View Logs"**

### 4.2. Các Vấn Đề Thường Gặp

**Lỗi: Database connection failed**
- Kiểm tra MySQL service đã running chưa
- Verify environment variables trong Settings → Variables

**Lỗi: 404 Not Found**
- Check Dockerfile đã copy đúng files chưa
- Xem logs để debug

**Lỗi: Session not working**
- Railway mặc định support sessions
- Check PHP extension đã enable chưa

---

## 💰 Chi Phí

- **Free Tier**: $5 credit/tháng (≈ 500 giờ runtime)
- **Đủ cho đồ án sinh viên**: ✅
- **Không cần credit card**: ✅

### Tips Tiết Kiệm:

1. **Tắt service khi không dùng**:
   - Settings → Service → "Sleep" service
   
2. **Xóa old deployments**:
   - Deployments → Xóa các deployment cũ

3. **Giới hạn replica**:
   - Mặc định 1 replica (đủ dùng)

---

## 🎯 Checklist Deploy Hoàn Tất

- [ ] Code đã push lên GitHub
- [ ] Project Railway đã tạo
- [ ] MySQL database đã add
- [ ] Database schema đã import
- [ ] Environment variables đã config tự động
- [ ] Domain đã generate
- [ ] Website đã test được truy cập
- [ ] Đăng nhập admin thành công
- [ ] Test các tính năng chính (đặt vé, xem phim, ...)

---

## 📞 Support

Nếu gặp vấn đề, check:

1. **Railway Logs**: Xem chi tiết lỗi
2. **Railway Discord**: https://discord.gg/railway
3. **Railway Docs**: https://docs.railway.app

---

## 🔄 Update Code Sau Deploy

```bash
# Chỉ cần push code lên GitHub
git add .
git commit -m "Update features"
git push origin main

# Railway sẽ tự động deploy lại! 🚀
```

---

**Chúc bạn deploy thành công! 🎉**

Nếu có vấn đề gì, hãy check logs trong Railway dashboard để debug.
