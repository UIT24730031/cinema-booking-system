# Cinema Booking System - Railway Deployment

## 🎬 Project Info
Cinema booking system với PHP + MySQL

## 🚀 Quick Deploy

### 1. Push code lên GitHub
```bash
git add .
git commit -m "Add Railway config"
git push origin main
```

### 2. Deploy trên Railway.app

1. **Tạo project**: https://railway.app → New Project → Deploy from GitHub
2. **Add MySQL**: + New → Database → MySQL
3. **Import DB**: Dùng Railway CLI hoặc phpMyAdmin template
4. **Generate domain**: Settings → Networking → Generate Domain

### 3. Done! 
Website sẽ live tại: `https://your-app.up.railway.app`

## 📖 Chi Tiết

Xem hướng dẫn đầy đủ trong file `DEPLOY.md`

## 🔧 Files Quan Trọng

- `Dockerfile` - Container config
- `railway.json` - Railway deploy settings  
- `config.php` - Auto đọc Railway env variables
- `.htaccess` - Apache config
- `database.sql` - Schema để import

## 💰 Chi Phí

**FREE** - $5 credit/tháng (~500 giờ runtime)

## ✅ Default Admin

- Email: `admin@cinema.com`
- Password: `admin123`

## 🐛 Debug

Xem logs: Railway Dashboard → Service → Deployments → View Logs
