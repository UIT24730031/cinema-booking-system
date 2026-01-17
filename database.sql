-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 12, 2025 at 09:31 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinema_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_bookings`
--

DROP TABLE IF EXISTS `tbl_bookings`;
CREATE TABLE IF NOT EXISTS `tbl_bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `seats` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','confirmed','cancelled') DEFAULT 'confirmed',
  PRIMARY KEY (`booking_id`),
  KEY `user_id` (`user_id`),
  KEY `screening_id` (`screening_id`),
  KEY `idx_screening_status` (`screening_id`, `status`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_bookings`
--

INSERT INTO `tbl_bookings` (`booking_id`, `user_id`, `screening_id`, `seats`, `total_amount`, `booking_date`, `status`) VALUES
(1, 2, 9, 'G5,F5', 200000.00, '2026-01-17 08:19:38', 'confirmed'),
(2, 3, 1, 'A5,A6', 160000.00, '2026-01-17 10:30:00', 'confirmed'),
(3, 4, 2, 'B7,B8,B9', 300000.00, '2026-01-17 11:15:00', 'confirmed'),
(4, 5, 3, 'C5', 120000.00, '2026-01-17 14:20:00', 'confirmed'),
(5, 3, 4, 'D1,D2', 170000.00, '2026-01-17 09:45:00', 'confirmed'),
(6, 6, 5, 'E5,E6,E7,E8', 420000.00, '2026-01-17 13:30:00', 'confirmed'),
(7, 4, 6, 'F10,F11', 180000.00, '2026-01-17 10:00:00', 'cancelled'),
(8, 2, 7, 'G3,G4', 220000.00, '2026-01-17 15:45:00', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seat_bookings`
--

DROP TABLE IF EXISTS `tbl_seat_bookings`;
CREATE TABLE IF NOT EXISTS `tbl_seat_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_id` int(11) NOT NULL,
  `screening_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_seat_per_screening` (`screening_id`, `seat_number`),
  KEY `booking_id` (`booking_id`),
  KEY `screening_id` (`screening_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Bảng lưu từng ghế đã đặt - ngăn chặn duplicate booking';

--
-- Dumping data for table `tbl_seat_bookings`
--

INSERT INTO `tbl_seat_bookings` (`id`, `booking_id`, `screening_id`, `seat_number`, `created_at`) VALUES
(1, 1, 9, 'G5', '2026-01-17 08:19:38'),
(2, 1, 9, 'F5', '2026-01-17 08:19:38'),
(3, 2, 1, 'A5', '2026-01-17 10:30:00'),
(4, 2, 1, 'A6', '2026-01-17 10:30:00'),
(5, 3, 2, 'B7', '2026-01-17 11:15:00'),
(6, 3, 2, 'B8', '2026-01-17 11:15:00'),
(7, 3, 2, 'B9', '2026-01-17 11:15:00'),
(8, 4, 3, 'C5', '2026-01-17 14:20:00'),
(9, 5, 4, 'D1', '2026-01-17 09:45:00'),
(10, 5, 4, 'D2', '2026-01-17 09:45:00'),
(11, 6, 5, 'E5', '2026-01-17 13:30:00'),
(12, 6, 5, 'E6', '2026-01-17 13:30:00'),
(13, 6, 5, 'E7', '2026-01-17 13:30:00'),
(14, 6, 5, 'E8', '2026-01-17 13:30:00'),
(15, 7, 6, 'F10', '2026-01-17 10:00:00'),
(16, 7, 6, 'F11', '2026-01-17 10:00:00'),
(17, 8, 7, 'G3', '2026-01-17 15:45:00'),
(18, 8, 7, 'G4', '2026-01-17 15:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_login`
--

DROP TABLE IF EXISTS `tbl_login`;
CREATE TABLE IF NOT EXISTS `tbl_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` tinyint(4) DEFAULT 2 COMMENT '0=Admin, 1=Staff, 2=Customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_login`
--

INSERT INTO `tbl_login` (`id`, `user_id`, `username`, `password`, `user_type`, `created_at`) VALUES
(1, 1, 'admin@cinema.com', 'admin123', 0, '2026-01-10 07:38:29'),
(2, 2, 'aa@gmail.com', '1234567', 2, '2026-01-12 08:19:01'),
(3, 3, 'john.doe@gmail.com', 'password123', 2, '2026-01-14 08:00:00'),
(4, 4, 'jane.smith@gmail.com', 'password123', 2, '2026-01-15 09:30:00'),
(5, 5, 'mike.wilson@gmail.com', 'password123', 2, '2026-01-16 10:15:00'),
(6, 6, 'sarah.jones@gmail.com', 'password123', 2, '2026-01-17 11:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_movie`
--

DROP TABLE IF EXISTS `tbl_movie`;
CREATE TABLE IF NOT EXISTS `tbl_movie` (
  `movie_id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_name` varchar(200) NOT NULL,
  `director` varchar(100) DEFAULT NULL,
  `cast` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `video_url` varchar(500) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Thời lượng phim (phút)',
  `genre` varchar(100) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT 0.0,
  `status` enum('coming_soon','now_showing','ended') DEFAULT 'now_showing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`movie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_movie`
--

INSERT INTO `tbl_movie` (`movie_id`, `movie_name`, `director`, `cast`, `description`, `image`, `video_url`, `duration`, `genre`, `release_date`, `rating`, `status`, `created_at`) VALUES
(1, 'Avengers: Endgame', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Scarlett Johansson', 'Sau sự kiện tàn khốc của Infinity War, các siêu anh hùng còn lại tập hợp lần cuối để đảo ngược những gì Thanos đã làm.', 'https://image.tmdb.org/t/p/w500/or06FN3Dka5tukK1e9sl16pB3iy.jpg', 'https://www.youtube.com/watch?v=TcMBFSGVi1c', 181, 'Hành động, Phiêu lưu', '2019-04-26', 8.4, 'now_showing', '2025-12-05 07:38:29'),
(2, 'Spider-Man: No Way Home', 'Jon Watts', 'Tom Holland, Zendaya, Benedict Cumberbatch', 'Peter Parker phải đối mặt với hậu quả khi danh tính Spider-Man của anh bị tiết lộ với thế giới.', 'https://image.tmdb.org/t/p/w500/1g0dhYtq4irTY1GPXvft6k4YLjm.jpg', 'https://www.youtube.com/watch?v=JfVOs4VSpmA', 148, 'Hành động, Phiêu lưu', '2021-12-17', 8.3, 'now_showing', '2025-12-05 07:38:29'),
(3, 'The Batman', 'Matt Reeves', 'Robert Pattinson, Zoë Kravitz, Paul Dano', 'Batman khám phá tham nhũng ở Gotham City và mối liên hệ của nó với gia đình của mình.', 'https://image.tmdb.org/t/p/w500/74xTEgt7R36Fpooo50r9T25onhq.jpg', 'https://www.youtube.com/watch?v=mqqft2x_Aa4', 176, 'Hành động, Tội phạm', '2022-03-04', 7.8, 'now_showing', '2025-12-05 07:38:29'),
(4, 'Doctor Strange 2', 'Sam Raimi', 'Benedict Cumberbatch, Elizabeth Olsen', 'Doctor Strange phải đối mặt với các mối đe dọa đa vũ trụ mới.', 'https://image.tmdb.org/t/p/w500/9Gtg2DzBhmYamXBS1hKAhiwbBKS.jpg', 'https://www.youtube.com/watch?v=aWzlQ2N6qqg', 126, 'Hành động, Phiêu lưu', '2022-05-06', 7.4, 'now_showing', '2025-12-05 07:38:29'),
(5, 'Top Gun: Maverick', 'Joseph Kosinski', 'Tom Cruise, Jennifer Connelly', 'Sau hơn 30 năm phục vụ, Pete \"Maverick\" Mitchell tiếp tục là một phi công thử nghiệm hàng đầu.', 'https://image.tmdb.org/t/p/w500/62HCnUTziyWcpDaBO2i1DX17ljH.jpg', 'https://www.youtube.com/watch?v=giXco2jaZ_4', 131, 'Hành động, Drama', '2022-05-27', 8.3, 'now_showing', '2025-12-05 07:38:29'),
(6, 'Black Panther', 'Ryan Coogler', 'Chadwick Boseman, Michael B. Jordan', 'T\'Challa trở về Wakanda để lên ngôi vua nhưng phải đối mặt với thách thức từ quá khứ.', 'https://image.tmdb.org/t/p/w500/uxzzxijgPIY7slzFvMotPv8wjKA.jpg', 'https://www.youtube.com/watch?v=xjDjIWPwcPU', 134, 'Hành động, Phiêu lưu', '2018-02-16', 7.3, 'now_showing', '2025-12-05 07:38:29'),
(7, 'Inception', 'Christopher Nolan', 'Leonardo DiCaprio, Joseph Gordon-Levitt', 'Một tên trộm chuyên đánh cắp bí mật trong giấc mơ được giao nhiệm vụ cấy ý tưởng vào tiềm thức.', 'https://image.tmdb.org/t/p/w500/9gk7adHYeDvHkCSEqAvQNLV5Uge.jpg', 'https://www.youtube.com/watch?v=YoHD9XEInc0', 148, 'Hành động, Khoa học viễn tưởng', '2010-07-16', 8.8, 'now_showing', '2025-12-05 07:38:29'),
(8, 'Interstellar', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'Một nhóm nhà thám hiểm du hành qua lỗ sâu để tìm kiếm tương lai cho nhân loại.', 'https://image.tmdb.org/t/p/w500/gEU2QniE6E77NI6lCU6MxlNBvIx.jpg', 'https://www.youtube.com/watch?v=zSWdZVtXT7E', 169, 'Khoa học viễn tưởng, Drama', '2014-11-07', 8.6, 'coming_soon', '2025-12-05 07:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_news`
--

DROP TABLE IF EXISTS `tbl_news`;
CREATE TABLE IF NOT EXISTS `tbl_news` (
  `news_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `cast` text DEFAULT NULL,
  `news_date` date DEFAULT NULL,
  `attachment` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_news`
--

INSERT INTO `tbl_news` (`news_id`, `name`, `cast`, `news_date`, `attachment`, `description`, `created_at`) VALUES
(1, 'Khuyến mãi vé xem phim giá rẻ cuối tuần', NULL, '2026-01-25', 'images/promo1.jpg', 'Giảm giá 30% cho tất cả các suất chiếu vào thứ 7 và Chủ nhật', '2026-01-15 07:38:29'),
(2, 'Combo bỏng nước giảm 50%', NULL, '2026-02-01', 'images/promo2.jpg', 'Mua combo bỏng nước size lớn chỉ với 50,000đ khi mua vé online', '2026-01-15 07:38:29'),
(3, 'Fast & Furious 10 - Sắp ra mắt', 'Vin Diesel, Michelle Rodriguez, Jason Momoa', '2026-05-19', 'images/fast10.jpg', 'Phần phim mới nhất trong series Fast & Furious đình đám', '2026-01-15 07:38:29'),
(4, 'Guardians of the Galaxy Vol. 3', 'Chris Pratt, Zoe Saldana', '2026-05-05', 'images/guardians3.jpg', 'Hành trình cuối cùng của đội vệ binh dải ngân hà', '2026-01-15 07:38:29'),
(5, 'Thẻ thành viên VIP - Ưu đãi đặc biệt', NULL, '2026-02-14', 'images/vip.jpg', 'Đăng ký thẻ VIP để nhận voucher 200,000đ và tích điểm đổi quà', '2026-01-15 07:38:29'),
(6, 'The Flash - Siêu phẩm DC sắp chiếu', 'Ezra Miller, Michael Keaton', '2026-06-16', 'images/flash.jpg', 'Barry Allen du hành thời gian để cứu gia đình và thay đổi quá khứ', '2026-01-15 07:38:29'),
(7, 'Sinh nhật Cinema Star - Quà tặng hấp dẫn', NULL, '2026-02-20', 'images/birthday.jpg', 'Nhân dịp sinh nhật 5 năm - Vé chỉ từ 50,000đ cho sinh viên', '2026-01-15 07:38:29'),
(8, 'Indiana Jones 5 - Huyền thoại trở lại', 'Harrison Ford, Phoebe Waller-Bridge', '2026-06-30', 'images/indiana.jpg', 'Cuộc phiêu lưu cuối cùng của nhà khảo cổ học huyền thoại', '2026-01-15 07:38:29');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_registration`
--

DROP TABLE IF EXISTS `tbl_registration`;
CREATE TABLE IF NOT EXISTS `tbl_registration` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT 'Male',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_registration`
--

INSERT INTO `tbl_registration` (`user_id`, `name`, `email`, `phone`, `age`, `gender`, `created_at`) VALUES
(1, 'Admin', 'admin@cinema.com', '0123456789', 30, 'Male', '2026-01-10 07:38:29'),
(2, 'Nguyen Van An', 'aa@gmail.com', '0123456789', 24, 'Male', '2026-01-12 08:19:01'),
(3, 'Tran Thi Binh', 'john.doe@gmail.com', '0987654321', 28, 'Male', '2026-01-14 08:00:00'),
(4, 'Le Hoang Minh', 'jane.smith@gmail.com', '0912345678', 32, 'Female', '2026-01-15 09:30:00'),
(5, 'Pham Van Duc', 'mike.wilson@gmail.com', '0909876543', 26, 'Male', '2026-01-16 10:15:00'),
(6, 'Vo Thi Mai', 'sarah.jones@gmail.com', '0898765432', 29, 'Female', '2026-01-17 11:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_screenings`
--

DROP TABLE IF EXISTS `tbl_screenings`;
CREATE TABLE IF NOT EXISTS `tbl_screenings` (
  `screening_id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `theatre_id` int(11) NOT NULL,
  `screen_name` varchar(50) DEFAULT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `available_seats` int(11) DEFAULT 100,
  PRIMARY KEY (`screening_id`),
  KEY `movie_id` (`movie_id`),
  KEY `theatre_id` (`theatre_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_screenings`
--

INSERT INTO `tbl_screenings` (`screening_id`, `movie_id`, `theatre_id`, `screen_name`, `show_date`, `show_time`, `price`, `available_seats`) VALUES
(1, 1, 1, 'Screen 1', '2026-01-18', '10:00:00', 80000.00, 148),
(2, 1, 1, 'Screen 1', '2026-01-19', '14:30:00', 100000.00, 147),
(3, 1, 1, 'Screen 2', '2026-01-25', '19:00:00', 120000.00, 149),
(4, 2, 2, 'Screen 1', '2026-01-20', '11:00:00', 85000.00, 118),
(5, 2, 2, 'Screen 2', '2026-01-28', '16:00:00', 105000.00, 116),
(6, 3, 3, 'Screen 1', '2026-01-22', '13:00:00', 90000.00, 98),
(7, 3, 3, 'Screen 1', '2026-02-05', '20:30:00', 110000.00, 98),
(8, 4, 1, 'Screen 3', '2026-02-08', '15:00:00', 95000.00, 150),
(9, 5, 2, 'Screen 3', '2026-02-10', '18:00:00', 100000.00, 118),
(10, 6, 1, 'Screen 2', '2026-02-12', '10:30:00', 80000.00, 150),
(11, 6, 2, 'Screen 1', '2026-02-14', '14:00:00', 100000.00, 120),
(12, 7, 3, 'Screen 2', '2026-02-15', '17:30:00', 115000.00, 100),
(13, 7, 1, 'Screen 1', '2026-02-18', '21:00:00', 125000.00, 150),
(14, 1, 2, 'Screen 2', '2026-02-20', '09:00:00', 75000.00, 120),
(15, 2, 3, 'Screen 1', '2026-02-22', '12:30:00', 95000.00, 100),
(16, 3, 1, 'Screen 3', '2026-02-25', '16:00:00', 105000.00, 150),
(17, 4, 2, 'Screen 3', '2026-02-28', '19:30:00', 120000.00, 120),
(18, 5, 3, 'Screen 2', '2026-03-05', '22:00:00', 130000.00, 100),
(19, 8, 1, 'Screen 1', '2026-03-08', '10:00:00', 85000.00, 150),
(20, 8, 2, 'Screen 1', '2026-03-12', '14:30:00', 105000.00, 120),
(21, 8, 3, 'Screen 1', '2026-03-15', '19:00:00', 125000.00, 100),
(22, 1, 1, 'Screen 2', '2026-03-18', '11:00:00', 85000.00, 150),
(23, 2, 2, 'Screen 2', '2026-03-20', '15:30:00', 105000.00, 120),
(24, 5, 1, 'Screen 3', '2026-03-22', '20:00:00', 120000.00, 150),
(25, 7, 3, 'Screen 1', '2026-03-25', '13:00:00', 95000.00, 100);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_theatre`
--

DROP TABLE IF EXISTS `tbl_theatre`;
CREATE TABLE IF NOT EXISTS `tbl_theatre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_screens` int(11) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_theatre`
--

INSERT INTO `tbl_theatre` (`id`, `name`, `address`, `city`, `phone`, `total_screens`) VALUES
(1, 'CGV Vincom', '72 Lê Thánh Tôn, Quận 1', 'Hồ Chí Minh', '1900545415', 8),
(2, 'Lotte Cinema', '45A Nguyễn Văn Trỗi, Phú Nhuận', 'Hồ Chí Minh', '19002524', 10),
(3, 'Galaxy Cinema', '116 Nguyễn Du, Quận 1', 'Hồ Chí Minh', '19002224', 6),
(4, 'BHD Star Cineplex', '3/2 Street, Quận 10', 'Hồ Chí Minh', '19002099', 7);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_bookings`
--
ALTER TABLE `tbl_bookings`
  ADD CONSTRAINT `tbl_bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_registration` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_bookings_ibfk_2` FOREIGN KEY (`screening_id`) REFERENCES `tbl_screenings` (`screening_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_seat_bookings`
--
ALTER TABLE `tbl_seat_bookings`
  ADD CONSTRAINT `tbl_seat_bookings_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `tbl_bookings` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_seat_bookings_ibfk_2` FOREIGN KEY (`screening_id`) REFERENCES `tbl_screenings` (`screening_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_login`
--
ALTER TABLE `tbl_login`
  ADD CONSTRAINT `tbl_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_registration` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `tbl_screenings`
--
ALTER TABLE `tbl_screenings`
  ADD CONSTRAINT `tbl_screenings_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `tbl_movie` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tbl_screenings_ibfk_2` FOREIGN KEY (`theatre_id`) REFERENCES `tbl_theatre` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
