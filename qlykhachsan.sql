-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 22, 2026 lúc 12:57 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `qlykhachsan`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bangluong`
--

CREATE TABLE `bangluong` (
  `id_bangluong` int(11) NOT NULL,
  `id_nhanvien` int(11) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `so_ngay_cong` int(11) DEFAULT 0,
  `thuong` decimal(15,2) DEFAULT 0.00,
  `phat` decimal(15,2) DEFAULT 0.00,
  `luong_co_ban` decimal(15,2) NOT NULL,
  `tong_luong` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chamcong`
--

CREATE TABLE `chamcong` (
  `id_chamcong` int(11) NOT NULL,
  `id_nhanvien` int(11) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `so_ngay_di_lam` int(11) DEFAULT 0,
  `so_ngay_nghi_khong_phep` int(11) DEFAULT 0,
  `so_ngay_nghi_co_phep` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `combo`
--

CREATE TABLE `combo` (
  `id_combo` int(11) NOT NULL,
  `ten_combo` varchar(255) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia_combo` decimal(15,2) NOT NULL,
  `so_dem_luu_tru` int(11) NOT NULL DEFAULT 1,
  `gia_phong_dinh_muc` decimal(15,2) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `loai_phong_ap_dung` enum('Standard','Deluxe','Suite') NOT NULL,
  `quyen_loi` text DEFAULT NULL,
  `dieu_khoan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `combo`
--

INSERT INTO `combo` (`id_combo`, `ten_combo`, `mo_ta`, `gia_combo`, `so_dem_luu_tru`, `gia_phong_dinh_muc`, `hinh_anh`, `loai_phong_ap_dung`, `quyen_loi`, `dieu_khoan`) VALUES
(1, 'Combo Nghỉ dưỡng 2N1Đ', '1 đêm nghỉ + buffet sáng + hồ bơi miễn phí', 1200000.00, 1, 1000000.00, 'https://images.pexels.com/photos/14746040/pexels-photo-14746040.jpeg', 'Standard', '- Trải nghiệm số đêm lưu trú trọn gói phòng sang trọng.\n- Miễn phí buffet bữa sáng mỗi ngày.\n- Miễn phí sử dụng hồ bơi vô cực và phòng gym.\n- Nước uống chào mừng khi nhận phòng.', '- Giá đã bao gồm thuế VAT và phí dịch vụ.\n- Yêu cầu thanh toán trước 100% khi xác nhận.\n- Không hoàn hủy trong các ngày lễ Tết.\n- Trẻ em đi kèm tính phí theo quy định của khách sạn.'),
(2, 'Combo Gia đình 3N2Đ', '2 đêm nghỉ + ăn sáng + vé vui chơi', 2500000.00, 2, 1200000.00, 'https://images.pexels.com/photos/164595/pexels-photo-164595.jpeg', 'Deluxe', '- Trải nghiệm số đêm lưu trú trọn gói phòng sang trọng.\n- Miễn phí buffet bữa sáng mỗi ngày.\n- Miễn phí sử dụng hồ bơi vô cực và phòng gym.\n- Nước uống chào mừng khi nhận phòng.', '- Giá đã bao gồm thuế VAT và phí dịch vụ.\n- Yêu cầu thanh toán trước 100% khi xác nhận.\n- Không hoàn hủy trong các ngày lễ Tết.\n- Trẻ em đi kèm tính phí theo quy định của khách sạn.'),
(3, 'Combo Couple Romantic', 'Trang trí phòng + rượu vang + 1 đêm nghỉ', 1800000.00, 1, 1500000.00, 'https://images.pexels.com/photos/271624/pexels-photo-271624.jpeg', 'Suite', '- Trải nghiệm số đêm lưu trú trọn gói phòng sang trọng.\n- Miễn phí buffet bữa sáng mỗi ngày.\n- Miễn phí sử dụng hồ bơi vô cực và phòng gym.\n- Nước uống chào mừng khi nhận phòng.', '- Giá đã bao gồm thuế VAT và phí dịch vụ.\n- Yêu cầu thanh toán trước 100% khi xác nhận.\n- Không hoàn hủy trong các ngày lễ Tết.\n- Trẻ em đi kèm tính phí theo quy định của khách sạn.'),
(4, 'Combo Công tác tiết kiệm', 'Phòng nghỉ + ăn sáng + wifi tốc độ cao', 900000.00, 1, 800000.00, 'https://images.pexels.com/photos/261102/pexels-photo-261102.jpeg', 'Standard', '- Trải nghiệm số đêm lưu trú trọn gói phòng sang trọng.\n- Miễn phí buffet bữa sáng mỗi ngày.\n- Miễn phí sử dụng hồ bơi vô cực và phòng gym.\n- Nước uống chào mừng khi nhận phòng.', '- Giá đã bao gồm thuế VAT và phí dịch vụ.\n- Yêu cầu thanh toán trước 100% khi xác nhận.\n- Không hoàn hủy trong các ngày lễ Tết.\n- Trẻ em đi kèm tính phí theo quy định của khách sạn.'),
(5, 'Combo Nghỉ dưỡng cao cấp 4N3Đ', '3 đêm nghỉ + buffet + spa + đưa đón sân bay', 4500000.00, 3, 2000000.00, 'https://images.pexels.com/photos/189296/pexels-photo-189296.jpeg', 'Suite', '- Trải nghiệm số đêm lưu trú trọn gói phòng sang trọng.\n- Miễn phí buffet bữa sáng mỗi ngày.\n- Miễn phí sử dụng hồ bơi vô cực và phòng gym.\n- Nước uống chào mừng khi nhận phòng.', '- Giá đã bao gồm thuế VAT và phí dịch vụ.\n- Yêu cầu thanh toán trước 100% khi xác nhận.\n- Không hoàn hủy trong các ngày lễ Tết.\n- Trẻ em đi kèm tính phí theo quy định của khách sạn.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `combo_dichvu`
--

CREATE TABLE `combo_dichvu` (
  `id_combo` int(11) NOT NULL,
  `id_dichvu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `datphong`
--

CREATE TABLE `datphong` (
  `id_datphong` int(11) NOT NULL,
  `id_khachhang` int(11) NOT NULL,
  `id_phong` int(11) DEFAULT NULL,
  `id_combo` int(11) DEFAULT NULL,
  `id_voucher` int(11) DEFAULT NULL,
  `ngay_dat` date NOT NULL,
  `ngay_nhan` date NOT NULL,
  `ngay_tra` date DEFAULT NULL,
  `loai_hinh_dat` enum('LẺ','COMBO') NOT NULL,
  `tong_tien_phai_tra` decimal(15,2) NOT NULL DEFAULT 0.00,
  `trang_thai` enum('Chờ xác nhận','Đã xác nhận','Đã thanh toán','Đã hủy') DEFAULT 'Chờ xác nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `datphong`
--

INSERT INTO `datphong` (`id_datphong`, `id_khachhang`, `id_phong`, `id_combo`, `id_voucher`, `ngay_dat`, `ngay_nhan`, `ngay_tra`, `loai_hinh_dat`, `tong_tien_phai_tra`, `trang_thai`) VALUES
(64, 33, 3, NULL, NULL, '2026-05-18', '2026-05-18', '2026-05-19', 'LẺ', 1500000.00, 'Đã xác nhận'),
(68, 33, 1, NULL, NULL, '2026-05-20', '2026-05-20', '2026-05-21', 'LẺ', 500000.00, 'Đã xác nhận'),
(73, 33, 2, NULL, NULL, '2026-05-20', '2026-05-20', '2026-05-21', 'LẺ', 500000.00, 'Đã xác nhận'),
(74, 33, 4, NULL, NULL, '2026-05-20', '2026-05-20', '2026-05-21', 'LẺ', 1450000.00, 'Đã xác nhận'),
(75, 33, NULL, 2, NULL, '2026-05-20', '2026-05-20', '2026-05-21', 'COMBO', 2500000.00, 'Đã xác nhận'),
(76, 33, NULL, 1, NULL, '2026-05-20', '2026-05-20', '2026-05-21', 'COMBO', 1200000.00, 'Đã xác nhận');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `dichvu`
--

CREATE TABLE `dichvu` (
  `id_dichvu` int(11) NOT NULL,
  `ten_dich_vu` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `gia` decimal(15,2) NOT NULL,
  `gia_von` decimal(15,2) DEFAULT 0.00,
  `hinh_anh` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `dichvu`
--

INSERT INTO `dichvu` (`id_dichvu`, `ten_dich_vu`, `mo_ta`, `gia`, `gia_von`, `hinh_anh`) VALUES
(1, 'Spa and Massage', 'Thư giãn toàn thân với liệu trình massage chuyên nghiệp.', 300000.00, 0.00, '1759747238_pexels-olly-3757657.jpg'),
(2, 'Ăn sáng tại phòng', 'Thực đơn buffet sáng, phục vụ tận phòng.', 111000.00, 0.00, '1759745315_pexels-julieaagaard-1426715.jpg'),
(3, 'Giặt ủi quần áo', 'Dịch vụ giặt, sấy và ủi, trả trong ngày.', 30000.00, 0.00, '1759746018_laundry-la-gi.webp'),
(4, 'Thuê xe đưa đón sân bay', 'Xe 4–7 chỗ, đưa đón khách tại sân bay Nội Bài.', 500000.00, 0.00, '1759748376_1.jpg'),
(7, 'Hồ bơi ngoài trời', 'Sử dụng hồ bơi vô cực ngoài trời, có khăn và nước uống.', 200000.00, 0.00, '1759746383_pexels-quang-nguyen-vinh-222549-14036440.jpg'),
(8, 'Mini Bar trong phòng', 'Đồ uống, snack có sẵn trong minibar, tính theo sử dụng', 550000.00, 0.00, '1759746758_pexels-andreevaleksandar-17705729.jpg'),
(9, 'Phòng gym', 'Trang bị máy chạy, tạ và huấn luyện viên hỗ trợ.', 80000.00, 0.00, '1759746963_pexels-heyho-7031705.jpg'),
(10, 'Thuê phòng hội nghị', 'Phòng hội nghị sức chứa 50 người, trang bị máy chiếu, micro.', 2000000.00, 0.00, '1759747757_phong-hoi-nghi-tai-Almaz-long-bien.jpg'),
(11, 'Karaoke gia đình', 'Phòng karaoke cách âm, dàn âm thanh hiện đại.', 510000.00, 0.00, '1759746527_chi-phi-thiet-ke-phong-karaoke-tai-nha-1.jpg'),
(12, 'Dịch vụ trông trẻ', 'Nhân viên trông trẻ chuyên nghiệp, an toàn, có trách nhiệm', 399000.00, 0.00, '1759745254_pexels-ivan-samkov-8504273.jpg'),
(13, 'Thuê xe đạp tham quan', 'Xe đạp cho khách tham quan quanh khu vực.', 90000.00, 0.00, '1759748359_snapedit_1759748347766.jpeg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `id_hoadon` int(11) NOT NULL,
  `id_datphong` int(11) NOT NULL,
  `tong_tien` decimal(15,2) NOT NULL,
  `ngay_xuat` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `hoadon`
--

INSERT INTO `hoadon` (`id_hoadon`, `id_datphong`, `tong_tien`, `ngay_xuat`) VALUES
(51, 64, 1500000.00, '2026-05-18'),
(55, 68, 500000.00, '2026-05-20'),
(60, 73, 500000.00, '2026-05-20'),
(61, 74, 1450000.00, '2026-05-20'),
(62, 75, 2500000.00, '2026-05-20'),
(63, 76, 1200000.00, '2026-05-20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `id_khachhang` int(11) NOT NULL,
  `tai_khoan_khachhang_id` int(11) DEFAULT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ngay_sinh` date DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ','Khác') DEFAULT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khachhang`
--

INSERT INTO `khachhang` (`id_khachhang`, `tai_khoan_khachhang_id`, `ho_ten`, `ngay_sinh`, `gioi_tinh`, `so_dien_thoai`, `email`, `cccd`, `dia_chi`) VALUES
(32, NULL, 'Lê Huy Đạt', '2004-06-22', 'Nam', '0358414532', 'huydatsan@gmail.com', '001204009986', 'Gl-HN'),
(33, 29, 'Lê Huy Đạt', '2004-06-22', 'Nam', '0358414532', 'huydatsan@gmail.com', '001204009986', 'GL');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `id_nhanvien` int(11) NOT NULL,
  `tai_khoan_nhanvien_id` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `chuc_vu` varchar(50) DEFAULT NULL,
  `luong_co_ban` decimal(15,2) NOT NULL,
  `ngay_vao_lam` date DEFAULT NULL,
  `so_dien_thoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong`
--

CREATE TABLE `phong` (
  `id_phong` int(11) NOT NULL,
  `so_phong` varchar(10) NOT NULL,
  `loai_phong` enum('Standard','Deluxe','Suite') NOT NULL,
  `gia_phong` decimal(15,2) NOT NULL,
  `so_luong_nguoi` int(11) NOT NULL,
  `trang_thai` enum('Trống','Đã đặt','Bảo trì') DEFAULT 'Trống',
  `mo_ta` text DEFAULT NULL,
  `anh` varchar(500) DEFAULT NULL,
  `dien_tich` varchar(100) DEFAULT NULL,
  `huong_phong` varchar(100) DEFAULT NULL,
  `so_phong_ngu` int(11) DEFAULT NULL,
  `tien_nghi` text DEFAULT NULL,
  `thong_tin_quan_trong` text DEFAULT NULL,
  `giam_gia_percent` int(11) DEFAULT 0,
  `sale_tu_ngay` datetime DEFAULT NULL,
  `sale_den_ngay` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `phong`
--

INSERT INTO `phong` (`id_phong`, `so_phong`, `loai_phong`, `gia_phong`, `so_luong_nguoi`, `trang_thai`, `mo_ta`, `anh`, `dien_tich`, `huong_phong`, `so_phong_ngu`, `tien_nghi`, `thong_tin_quan_trong`, `giam_gia_percent`, `sale_tu_ngay`, `sale_den_ngay`) VALUES
(1, '101', 'Standard', 500000.00, 2, 'Đã đặt', 'Phòng Standard nhỏ gọn (~20 m²), 1 giường đôi queen loại cơ bản, có cửa sổ nhìn vào nội khu, điều hoà, TV màn hình phẳng, wifi tốc độ cao, phòng tắm vòi sen, đồ vệ sinh cá nhân tiêu chuẩn.', 'https://images.pexels.com/photos/28272332/pexels-photo-28272332.jpeg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 20, '2026-05-21 00:00:00', '2026-05-22 23:59:59'),
(2, '102', 'Standard', 500000.00, 2, 'Đã đặt', 'Phòng tiêu chuẩn tiện nghi, 2 giường đơn, thích hợp cho bạn bè hoặc đồng nghiệp.', 'https://ezcloud.vn/wp-content/uploads/2023/03/phong-standard-la-gi.webp', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(3, '103', 'Standard', 600000.00, 2, 'Đã đặt', 'Phòng tiêu chuẩn gọn gàng, 1 giường đôi, có ánh sáng tự nhiên, lý tưởng cho kỳ nghỉ ngắn.', 'https://res.cloudinary.com/maistra/image/upload/w_1920,c_lfill,g_auto,q_auto,dpr_auto/f_auto/v1700658053/Proprietes/Select/Zagreb/Hotel%20International/22.11.23/23074-09-18%20Hotel%20International%20Rooms/23074-09-18%20Hotel%20International%20Rooms%20Standard%20Single%20Use/Webres%202000px/23074-09-18_Hotel_International_Rooms_Classic_Queen_1_2000px_sivgq2.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(4, '104', 'Deluxe', 1450000.00, 3, 'Đã đặt', 'Phòng cao cấp rộng rãi, thiết kế hiện đại, cửa sổ view thành phố.', 'https://noithaticon.vn/wp-content/uploads/2023/08/kich-thuoc-giuong-don-tan-co-dien-2-1690878432.jpg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(5, '105', 'Deluxe', 1500000.00, 3, 'Trống', 'Phòng cao cấp sang trọng, có ban công nhỏ và tầm nhìn thoáng đãng.', 'https://dyf.vn/wp-content/uploads/2021/12/phong-Deluxe-Double.jpg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(6, '106', 'Deluxe', 1000000.00, 3, 'Trống', 'Phòng cao cấp tiện nghi, giường lớn, phù hợp nghỉ dưỡng dài ngày.', 'https://statics.vinpearl.com/gia-phong-vinpearl-ha-long-03.jpg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(7, '201', 'Suite', 3200000.00, 4, 'Trống', 'Suite cao cấp - VIP: diện tích ~70 m², phòng khách + bếp nhỏ, bàn ăn, bồn tắm jacuzzi + vòi sen đứng, nhiều view đẹp, tiện ích đặc biệt (welcome amenities, đôi giày đi trong nhà,…)', 'https://images.pexels.com/photos/7005295/pexels-photo-7005295.jpeg', '2,090 foot vuông / 194 mét vuông', 'Hướng biển ngoạn mục', 2, 'Dịch vụ quản gia (GEM) phục vụ theo yêu cầu\r\nHệ thống âm thanh BOSE cao cấp\r\nĐiện thoại gọi quốc tế trực tiếp (IDD)\r\nMáy pha Espresso chuyên dụng\r\nTủ lạnh hai cánh kiểu Mỹ\r\nHồ bơi vô cực riêng tư\r\nTủ rượu mini', 'Tầm nhìn hướng biển ngoạn mục trọn vẹn\r\nBiệt thự thiết kế không gian mở\r\nHai phòng ngủ chính với giường King\r\nPhòng tắm ngoài trời hòa mình với thiên nhiên\r\nSân hiên riêng có khu vực BBQ và ghế tắm nắng\r\nLối đi riêng thẳng ra bãi biển riêng tư', 0, NULL, NULL),
(8, '202', 'Suite', 2800000.00, 4, 'Trống', 'Suite sang trọng hơn, ban công rộng hoặc cửa sổ panorama, decor sang trọng, có thêm bàn ăn nhỏ nếu có thể, dịch vụ ưu tiên.', 'https://www.hotelscombined.com.au/himg/3f/97/5c/ice-18471-106990353-583520.jpg', '2,090 foot vuông / 194 mét vuông', 'Hướng biển ngoạn mục', 2, 'Dịch vụ quản gia (GEM) phục vụ theo yêu cầu\r\nHệ thống âm thanh BOSE cao cấp\r\nĐiện thoại gọi quốc tế trực tiếp (IDD)\r\nMáy pha Espresso chuyên dụng\r\nTủ lạnh hai cánh kiểu Mỹ\r\nHồ bơi vô cực riêng tư\r\nTủ rượu mini', 'Tầm nhìn hướng biển ngoạn mục trọn vẹn\r\nBiệt thự thiết kế không gian mở\r\nHai phòng ngủ chính với giường King\r\nPhòng tắm ngoài trời hòa mình với thiên nhiên\r\nSân hiên riêng có khu vực BBQ và ghế tắm nắng\r\nLối đi riêng thẳng ra bãi biển riêng tư', 0, NULL, NULL),
(9, '203', 'Standard', 670000.00, 2, 'Đã đặt', 'Standard có ban công nhỏ / cửa sổ lớn đón sáng, nội thất gỗ/laminate, phong cách hiện đại.', 'https://sp-ao.shortpixel.ai/client/to_webp,q_glossy,ret_img/https://neworienthoteldanang.com/wp-content/uploads/2023/09/stay9.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(10, '204', 'Standard', 700000.00, 2, 'Trống', 'Phòng Standard lớn hơn (~20-22 m²), view hướng đường/nội khu đẹp hơn, bộ đồ uống nóng lạnh miễn phí.', 'https://dulichsaigon.edu.vn/wp-content/uploads/2025/01/8-cac-loai-phong-trong-khach-san.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(11, '205', 'Deluxe', 1590000.00, 3, 'Trống', 'Reluxe VIP với phòng khách riêng, thiết kế đẳng cấp, decor cao cấp hơn, có tiện nghi cộng thêm như dịch vụ đặt thức ăn phòng hoặc minibar cao cấp.', 'https://noithattamviet.com.vn/public/images/products/combo-noi-that-phong-ngu-master-go-cong-nghiep-cpn-37-1744164932.jpg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(12, '301', 'Deluxe', 990000.00, 3, 'Trống', 'Phòng Reluxe (~28-30 m²), giường Queen size, thiết kế nội thất cao cấp hơn, có minibar, TV lớn, phòng tắm đôi (vòi sen & bồn tắm nếu có).', 'https://images.pexels.com/photos/7018391/pexels-photo-7018391.jpeg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(13, '302', 'Suite', 3000000.00, 4, 'Trống', 'Suite hạng cao với tiện nghi đầy đủ, thiết kế hiện đại, có phòng khách, khu tiếp khách, view đẹp, nội thất chất lượng cao.', 'https://images.pexels.com/photos/30587967/pexels-photo-30587967.jpeg', '2,090 foot vuông / 194 mét vuông', 'Hướng biển ngoạn mục', 2, 'Dịch vụ quản gia (GEM) phục vụ theo yêu cầu\r\nHệ thống âm thanh BOSE cao cấp\r\nĐiện thoại gọi quốc tế trực tiếp (IDD)\r\nMáy pha Espresso chuyên dụng\r\nTủ lạnh hai cánh kiểu Mỹ\r\nHồ bơi vô cực riêng tư\r\nTủ rượu mini', 'Tầm nhìn hướng biển ngoạn mục trọn vẹn\r\nBiệt thự thiết kế không gian mở\r\nHai phòng ngủ chính với giường King\r\nPhòng tắm ngoài trời hòa mình với thiên nhiên\r\nSân hiên riêng có khu vực BBQ và ghế tắm nắng\r\nLối đi riêng thẳng ra bãi biển riêng tư', 0, NULL, NULL),
(14, '303', 'Standard', 550000.00, 2, 'Trống', 'Standard được trang bị thêm thiết bị vệ sinh cao cấp hơn (vòi sen mưa, máy sấy tóc, áo choàng tắm).', 'https://static.fireant.vn/Upload/20240331/images/phong-mau-khach-san-go-cong-nghiep-01.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(15, '304', 'Deluxe', 950000.00, 3, 'Trống', 'Reluxe có phòng khách nhỏ hoặc ghế sofa thư giãn, ban công lớn hoặc cửa sổ hướng đẹp, vật liệu nội thất cao cấp hơn.', 'https://dyf.vn/wp-content/uploads/2021/12/co-nen-chon-phong-deluxe.jpg', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(16, '401', 'Suite', 3500000.00, 4, 'Trống', 'Suite VIP cao cấp nhất: diện tích ~80-90 m², view toàn cảnh, trang thiết bị cao cấp (cà phê espresso, máy pha cao cấp, minibar loại thượng hạng…), dịch vụ tùy chỉnh (nhận/trả phòng linh hoạt, dịch vụ riêng, decor đặc biệt).', 'https://vanangroup.com.vn/wp-content/uploads/2024/05/President-Suite-phong-dac-biet-danh-cho-nguyen-thu-quoc-gia.jpg', '2,090 foot vuông / 194 mét vuông', 'Hướng biển ngoạn mục', 2, 'Dịch vụ quản gia (GEM) phục vụ theo yêu cầu\r\nHệ thống âm thanh BOSE cao cấp\r\nĐiện thoại gọi quốc tế trực tiếp (IDD)\r\nMáy pha Espresso chuyên dụng\r\nTủ lạnh hai cánh kiểu Mỹ\r\nHồ bơi vô cực riêng tư\r\nTủ rượu mini', 'Tầm nhìn hướng biển ngoạn mục trọn vẹn\r\nBiệt thự thiết kế không gian mở\r\nHai phòng ngủ chính với giường King\r\nPhòng tắm ngoài trời hòa mình với thiên nhiên\r\nSân hiên riêng có khu vực BBQ và ghế tắm nắng\r\nLối đi riêng thẳng ra bãi biển riêng tư', 0, NULL, NULL),
(17, '402', 'Standard', 710000.00, 2, 'Trống', 'Standard Premium: không chỉ tiện nghi đầy đủ mà còn có thiết kế decor nội thất bắt mắt hơn, lớp sơn & trang trí tốt hơn.', 'https://noithatvieta.vn/upload/images/thi-cong-noi-that-khach-san-hien-dai.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL),
(18, '403', 'Deluxe', 1550000.00, 3, 'Trống', 'Phòng có 1 phòng ngủ lớn hoặc giường king + giường đơn, phù hợp gia đình nhỏ, có thêm sofa bed / khu tiếp khách.', 'https://dyf.vn/wp-content/uploads/2021/12/phong-deluxe-la-gi.png', '550 foot vuông / 51 mét vuông', 'Hướng biển một phần', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi LCD 42 inch\r\nBồn tắm nằm và phòng tắm đứng\r\nDụng cụ pha trà và cà phê\r\nÁo choàng tắm và dép đi trong nhà\r\nKét sắt an toàn', 'Ban công rộng rãi với ghế tắm nắng\r\nKhu vực tiếp khách riêng biệt\r\nBồn tắm thư giãn sang trọng\r\nDịch vụ dọn phòng 2 lần/ngày\r\nPhục vụ ăn tại phòng 24/7', 0, NULL, NULL),
(19, '501', 'Suite', 2500000.00, 4, 'Trống', 'Suite rộng (~50-60 m²), 2 phòng ngủ riêng, view đẹp (phố hoặc hồ), minibar đầy đủ, bồn tắm lớn, thiết bị cao cấp, dịch vụ phòng 24h.', 'https://images.pexels.com/photos/18801087/pexels-photo-18801087.jpeg', '2,090 foot vuông / 194 mét vuông', 'Hướng biển ngoạn mục', 2, 'Dịch vụ quản gia (GEM) phục vụ theo yêu cầu\r\nHệ thống âm thanh BOSE cao cấp\r\nĐiện thoại gọi quốc tế trực tiếp (IDD)\r\nMáy pha Espresso chuyên dụng\r\nTủ lạnh hai cánh kiểu Mỹ\r\nHồ bơi vô cực riêng tư\r\nTủ rượu mini', 'Tầm nhìn hướng biển ngoạn mục trọn vẹn\r\nBiệt thự thiết kế không gian mở\r\nHai phòng ngủ chính với giường King\r\nPhòng tắm ngoài trời hòa mình với thiên nhiên\r\nSân hiên riêng có khu vực BBQ và ghế tắm nắng\r\nLối đi riêng thẳng ra bãi biển riêng tư', 0, NULL, NULL),
(20, '502', 'Standard', 950000.00, 1, 'Trống', 'Standard cao cấp với ban công / view nhìn phố, âm thanh cách âm tốt, có khu vực nhỏ tiếp khách / ghế thư giãn.', 'https://hocquanlynhahangkhachsan.wordpress.com/wp-content/uploads/2018/04/86481-nhieu-loai-phong-khach-nhau.jpg', '300 foot vuông / 28 mét vuông', 'Hướng vườn', 1, 'Wi-Fi tốc độ cao miễn phí\r\nTivi màn hình phẳng\r\nĐiều hòa nhiệt độ\r\nPhòng tắm vòi sen đứng\r\nMinibar cơ bản\r\nMáy sấy tóc', 'Tầm nhìn hướng vườn xanh mát\r\nGiường King hoặc 2 giường đơn\r\nKhông gian ấm cúng phù hợp cho cặp đôi\r\nBan công nhỏ riêng biệt\r\nKhông hút thuốc trong phòng', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sudungdichvu`
--

CREATE TABLE `sudungdichvu` (
  `id_sudungdv` int(11) NOT NULL,
  `id_datphong` int(11) NOT NULL,
  `id_dichvu` int(11) NOT NULL,
  `so_luong` int(11) DEFAULT 1,
  `thanh_tien` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `sudungdichvu`
--

INSERT INTO `sudungdichvu` (`id_sudungdv`, `id_datphong`, `id_dichvu`, `so_luong`, `thanh_tien`) VALUES
(42, 64, 1, 3, 900000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `id_taikhoan` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('USER','NHANVIEN','ADMIN') DEFAULT 'USER',
  `trang_thai` enum('ACTIVE','BLOCKED') DEFAULT 'ACTIVE',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`id_taikhoan`, `username`, `password`, `role`, `trang_thai`, `created_at`) VALUES
(29, 'hltv', '$2y$12$iwIJjTi45uY6LIC4C5oB9.URb6JoNOyN/JgO213XXQGsoG3t3J1Uu', 'USER', 'ACTIVE', '2026-04-22 21:41:37'),
(30, 'testdangky', '$2y$12$uz0buX/W4inUq71hk.nJ6uEyp3FUEvcjC9pwHoFCFUZ/JJVkYkyKa', 'USER', 'ACTIVE', '2026-04-22 22:20:12'),
(31, 'Test1', '$2y$12$z504nTGgRbDsGYUiwclxP.wdsT.J4vf/DEopNG2r701jpU4n6F4Ty', 'USER', 'ACTIVE', '2026-04-22 22:30:31'),
(32, 'test2', '$2y$12$9r4jePluRgizRIEJAi.WgOVELPMFtvHNo2vPVMfmtwXiplA9IAY82', 'USER', 'ACTIVE', '2026-04-22 22:58:08'),
(33, 'nhanvien1', '$2y$10$0zwaFFDMw/gy18szM1fSZ.ZfDKHNocL.bSn.JyIoR2PezwamFB7DG', 'NHANVIEN', 'ACTIVE', '2026-05-21 18:39:38'),
(34, 'admin1', '$2y$12$IAXVkPBdHDyfvt7WZbv6F.6464y3l1/dlbx6/tWfN/Qa.C8RqCR1K', 'ADMIN', 'ACTIVE', '2026-05-21 18:39:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `id_thanhtoan` int(11) NOT NULL,
  `id_datphong` int(11) NOT NULL,
  `ngay_thanh_toan` datetime DEFAULT current_timestamp(),
  `so_tien` decimal(15,2) NOT NULL,
  `vnp_transaction_no` varchar(100) DEFAULT NULL,
  `vnp_response_code` varchar(50) DEFAULT NULL,
  `hinh_thuc` enum('Tiền mặt','Chuyển khoản','VNPAY') DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `loai_thanh_toan` enum('Đặt cọc 30%','Thanh toán trọn gói Combo','Tiền gia hạn phòng','Thanh toán phần còn lại','Thu tiền bồi thường (hư hại)') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `thanhtoan`
--

INSERT INTO `thanhtoan` (`id_thanhtoan`, `id_datphong`, `ngay_thanh_toan`, `so_tien`, `vnp_transaction_no`, `vnp_response_code`, `hinh_thuc`, `ghi_chu`, `loai_thanh_toan`) VALUES
(53, 64, '2026-05-18 18:55:30', 450000.00, 'MOCK_VNP_682668', '00', 'Chuyển khoản', 'Giả lập thanh toán VNPay cọc 30% thành công.', 'Đặt cọc 30%'),
(54, 68, '2026-05-20 15:04:29', 150000.00, '15548796', '00', 'VNPAY', 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công', 'Đặt cọc 30%'),
(59, 73, '2026-05-20 16:03:55', 150000.00, '15548858', '00', 'VNPAY', 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công', 'Đặt cọc 30%'),
(60, 74, '2026-05-20 16:33:47', 435000.00, '15548908', '00', 'VNPAY', 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công', 'Đặt cọc 30%'),
(61, 75, '2026-05-20 19:16:03', 750000.00, '15548996', '00', 'VNPAY', 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công', 'Đặt cọc 30%'),
(62, 76, '2026-05-20 19:24:42', 360000.00, '15549001', '00', 'VNPAY', 'Thanh toán VNPay (cổng thanh toán) - cọc 30% thành công', 'Đặt cọc 30%');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `voucher`
--

CREATE TABLE `voucher` (
  `id_voucher` int(11) NOT NULL,
  `ma_code` varchar(20) NOT NULL,
  `loai_voucher` enum('PHONG','DICH_VU','ALL') NOT NULL,
  `muc_giam` decimal(15,2) NOT NULL,
  `is_percent` tinyint(1) DEFAULT 0,
  `ngay_het_han` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bangluong`
--
ALTER TABLE `bangluong`
  ADD PRIMARY KEY (`id_bangluong`),
  ADD UNIQUE KEY `id_nhanvien` (`id_nhanvien`,`thang`,`nam`);

--
-- Chỉ mục cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD PRIMARY KEY (`id_chamcong`),
  ADD UNIQUE KEY `id_nhanvien` (`id_nhanvien`,`thang`,`nam`);

--
-- Chỉ mục cho bảng `combo`
--
ALTER TABLE `combo`
  ADD PRIMARY KEY (`id_combo`);

--
-- Chỉ mục cho bảng `combo_dichvu`
--
ALTER TABLE `combo_dichvu`
  ADD PRIMARY KEY (`id_combo`,`id_dichvu`),
  ADD KEY `id_dichvu` (`id_dichvu`);

--
-- Chỉ mục cho bảng `datphong`
--
ALTER TABLE `datphong`
  ADD PRIMARY KEY (`id_datphong`),
  ADD KEY `id_khachhang` (`id_khachhang`),
  ADD KEY `id_phong` (`id_phong`),
  ADD KEY `fk_datphong_combo` (`id_combo`),
  ADD KEY `fk_datphong_voucher` (`id_voucher`);

--
-- Chỉ mục cho bảng `dichvu`
--
ALTER TABLE `dichvu`
  ADD PRIMARY KEY (`id_dichvu`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`id_hoadon`),
  ADD KEY `id_datphong` (`id_datphong`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`id_khachhang`),
  ADD UNIQUE KEY `tai_khoan_khachhang_id` (`tai_khoan_khachhang_id`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`id_nhanvien`),
  ADD UNIQUE KEY `tai_khoan_nhanvien_id` (`tai_khoan_nhanvien_id`);

--
-- Chỉ mục cho bảng `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id_phong`),
  ADD UNIQUE KEY `so_phong` (`so_phong`);

--
-- Chỉ mục cho bảng `sudungdichvu`
--
ALTER TABLE `sudungdichvu`
  ADD PRIMARY KEY (`id_sudungdv`),
  ADD KEY `id_datphong` (`id_datphong`),
  ADD KEY `id_dichvu` (`id_dichvu`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`id_taikhoan`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`id_thanhtoan`),
  ADD KEY `id_datphong` (`id_datphong`);

--
-- Chỉ mục cho bảng `voucher`
--
ALTER TABLE `voucher`
  ADD PRIMARY KEY (`id_voucher`),
  ADD UNIQUE KEY `ma_code` (`ma_code`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bangluong`
--
ALTER TABLE `bangluong`
  MODIFY `id_bangluong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  MODIFY `id_chamcong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `combo`
--
ALTER TABLE `combo`
  MODIFY `id_combo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `datphong`
--
ALTER TABLE `datphong`
  MODIFY `id_datphong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT cho bảng `dichvu`
--
ALTER TABLE `dichvu`
  MODIFY `id_dichvu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `id_hoadon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `id_khachhang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `id_nhanvien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `phong`
--
ALTER TABLE `phong`
  MODIFY `id_phong` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT cho bảng `sudungdichvu`
--
ALTER TABLE `sudungdichvu`
  MODIFY `id_sudungdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `id_taikhoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `id_thanhtoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT cho bảng `voucher`
--
ALTER TABLE `voucher`
  MODIFY `id_voucher` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bangluong`
--
ALTER TABLE `bangluong`
  ADD CONSTRAINT `bangluong_ibfk_1` FOREIGN KEY (`id_nhanvien`) REFERENCES `nhanvien` (`id_nhanvien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `chamcong`
--
ALTER TABLE `chamcong`
  ADD CONSTRAINT `chamcong_ibfk_1` FOREIGN KEY (`id_nhanvien`) REFERENCES `nhanvien` (`id_nhanvien`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `combo_dichvu`
--
ALTER TABLE `combo_dichvu`
  ADD CONSTRAINT `combo_dichvu_ibfk_1` FOREIGN KEY (`id_combo`) REFERENCES `combo` (`id_combo`) ON DELETE CASCADE,
  ADD CONSTRAINT `combo_dichvu_ibfk_2` FOREIGN KEY (`id_dichvu`) REFERENCES `dichvu` (`id_dichvu`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `datphong`
--
ALTER TABLE `datphong`
  ADD CONSTRAINT `datphong_ibfk_1` FOREIGN KEY (`id_khachhang`) REFERENCES `khachhang` (`id_khachhang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `datphong_ibfk_2` FOREIGN KEY (`id_phong`) REFERENCES `phong` (`id_phong`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_datphong_combo` FOREIGN KEY (`id_combo`) REFERENCES `combo` (`id_combo`),
  ADD CONSTRAINT `fk_datphong_voucher` FOREIGN KEY (`id_voucher`) REFERENCES `voucher` (`id_voucher`);

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`id_datphong`) REFERENCES `datphong` (`id_datphong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD CONSTRAINT `khachhang_ibfk_1` FOREIGN KEY (`tai_khoan_khachhang_id`) REFERENCES `taikhoan` (`id_taikhoan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `nhanvien_ibfk_1` FOREIGN KEY (`tai_khoan_nhanvien_id`) REFERENCES `taikhoan` (`id_taikhoan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `sudungdichvu`
--
ALTER TABLE `sudungdichvu`
  ADD CONSTRAINT `sudungdichvu_ibfk_1` FOREIGN KEY (`id_datphong`) REFERENCES `datphong` (`id_datphong`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sudungdichvu_ibfk_2` FOREIGN KEY (`id_dichvu`) REFERENCES `dichvu` (`id_dichvu`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`id_datphong`) REFERENCES `datphong` (`id_datphong`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
