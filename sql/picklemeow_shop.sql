SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE categories (
  id int(11) NOT NULL,
  name varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories (id, name) VALUES
(1, 'Điện thoại'),
(2, 'Máy tính'),
(3, 'Âm thanh - Tai nghe'),
(4, 'Phụ kiện'),
(5, 'Đồ chơi');

CREATE TABLE orders (
  id int(11) NOT NULL,
  user_id int(11) NOT NULL,
  total_price int(11) NOT NULL,
  status enum('pending','processing','shipped','completed','cancelled') DEFAULT 'pending',
  created_at timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE order_details (
  order_id int(11) NOT NULL,
  product_id int(11) NOT NULL,
  quantity int(11) NOT NULL,
  price int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE products (
  id int(11) NOT NULL,
  category_id int(11) NOT NULL,
  name varchar(255) NOT NULL,
  description text DEFAULT NULL,
  price int(11) NOT NULL,
  stock int(11) DEFAULT 0,
  image varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  discount int(11) DEFAULT 0,
  is_banner tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO products (id, category_id, name, description, price, stock, image, created_at, discount, is_banner) VALUES
(1, 1, 'iPhone 15 Pro', 'Điện thoại cao cấp từ Apple.', 22990000, 50, 'https://cdn2.fptshop.com.vn/unsafe/828x0/filters:format(webp):quality(75)/2023_9_13_638301983422003341_VN_iPhone_15_Pro_Natural_Titanium_PDP_Image_Position-1A_Natural_Titanium_Color.jpg', '2026-04-23 06:16:28', 0, 0),
(2, 1, 'Galaxy S24 Ultra', 'Flagship AI đỉnh cao của Samsung.', 18990000, 30, 'https://images.samsung.com/is/image/samsung/p6pim/vn/feature/164987682/vn-feature-smartphones-539782779?$FB_TYPE_A_MO_JPG$', '2026-04-23 06:16:28', 0, 0),
(3, 2, 'Macbook Air M5', 'Laptop siêu mỏng nhẹ, pin trâu.', 27990000, 20, 'https://www.apple.com/v/macbook-air/y/images/overview/hero/hero_static__c9sislzzicq6_large.png', '2026-04-23 06:16:28', 0, 0),
(4, 3, 'AirPods Pro 2', 'Tai nghe chống ồn chủ động.', 4990000, 100, 'https://cdn2.fptshop.com.vn/unsafe/1920x0/filters:format(webp):quality(75)/2023_9_18_638306528295272368_tai-nghe-airpods-pro-2023-usb-c-dd.jpg', '2026-04-23 06:16:28', 0, 0),
(6, 1, 'châu huy', 'adađa', 123, 0, 'https://www.shutterstock.com/image-vector/sparkling-light-teal-number-one-260nw-2698552113.jpg', '2026-04-23 06:25:18', 0, 0),
(7, 3, 'tài', 'anh này ngầu', 999999, 0, 'img/products/prod_1776953543_816.png', '2026-04-23 14:12:23', 0, 0),
(8, 1, 'hùng', 'anh này cx ngầu', 999999, 0, 'img/products/prod_1776953563_422.png', '2026-04-23 14:12:43', 0, 0),
(9, 2, 'huy', 'rác', -2147483648, 0, 'img/products/prod_1776953800_730.png', '2026-04-23 14:16:40', 0, 0),
(10, 1, 'meomeo', 'con mèo', 67676767, 0, 'img/products/prod_1776953883_940.png', '2026-04-23 14:18:03', 0, 0),
(11, 2, 'susi', 'mèo quá béo', 1102024, 0, 'img/products/prod_1776953930_983.png', '2026-04-23 14:18:50', 0, 0),
(12, 1, 'smiling lu', '', 68371838, 0, 'img/products/prod_1776954105_196.png', '2026-04-23 14:21:45', 0, 0),
(13, 1, ' evil lulu', 'gâgagagaga', 2147483647, 0, 'img/products/prod_1776954156_786.png', '2026-04-23 14:22:36', 0, 0),
(16, 1, 'tài5vr fgtnhyu8ci9==', 'jdjđạkfdkad', 1534567, 0, 'img/products/default.png', '2026-04-24 11:08:57', 0, 0),
(17, 3, 'đạt', 'pro vãi cứt', 400000000, 0, 'img/products/prod_1777033299_139.jpg', '2026-04-24 12:21:39', 0, 0),
(18, 1, 'đạt 09', 'cũng pro vc', 399000000, 0, 'img/products/prod_1777033350_201.jpg', '2026-04-24 12:22:30', 0, 0),
(19, 1, 'nguyên', 'người hữu duyên sẽ mua', 0, 0, 'img/products/prod_1777033442_736.jpg', '2026-04-24 12:24:02', 0, 0),
(20, 1, 'sì', 'con vịt của Huy', 240000, 0, 'img/products/prod_1777045026_920.jpg', '2026-04-24 15:37:06', 0, 0),
(21, 1, 'zâu', 'pet của huy', 812006, 0, 'img/products/prod_1777045065_260.jpg', '2026-04-24 15:37:45', 0, 0),
(22, 1, 'mr bơ', 'của huy luôn', 20000, 0, 'img/products/prod_1777045086_600.jpg', '2026-04-24 15:38:06', 0, 0),
(23, 1, 'bông', 'cute mà', 24100812, 0, 'img/products/prod_1777045117_595.jpg', '2026-04-24 15:38:37', 0, 0),
(24, 1, 'Điện thoại iPhone 16 256GB', 'CẤU HÌNH & BỘ NHỚ\r\nHệ điều hành: iOS 18\r\nChip xử lý (CPU): Apple A18 6 nhân\r\nTốc độ CPU: Hãng không công bố\r\nChip đồ họa (GPU): Apple GPU 5 nhân\r\nRAM: 8 GB\r\nDung lượng lưu trữ: 128 | 256 GB | 512GB\r\nDung lượng còn lại (khả dụng) khoảng: 241 GB\r\nDanh bạ: Không giới hạn', 22490000, 0, 'https://cdn.tgdd.vn/Products/Images/42/329136/iphone-16-green-600x600.png', '2026-04-24 15:41:49', 0, 0),
(30, 1, 'điện thoại', 'alo', 10000, 0, 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxITEhUTEhMWFhUWGBcXFxcYGBUYGBoXFxUXFxcVFxgYHSggGBslHRUXIjEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGhAQGy0lICUtLS0tKy0tLS0tOC0tLS0tLS0tLS0tLS0tLy0tLS0tLS8tLS0tLS0tLS0tLS0tLS0tLf/AABEIAKIBNwMBIgACEQEDEQH/', '2026-04-27 07:50:27', 10, 0),
(31, 1, 'điện thoại', 'alo', 10000, 0, 'https://cdn.viettablet.com/images/companies/1/dien-thoai-8.jpg?1719717372985', '2026-04-27 07:50:54', 70, 0),
(37, 2, 'máy tính', '123w123', 20000, 0, 'https://maytinhhoangha.com/uploads/files/Case%20V%E1%BB%8F/Banmaytinhcu.jpg', '2026-04-27 07:57:53', 18, 0),
(38, 4, 'phụ kiện', '213321', 30000, 0, 'https://bizweb.dktcdn.net/thumb/1024x1024/100/173/741/products/d345eb29694f8c11d55e.jpg?v=1555904803497', '2026-04-27 07:58:32', 29, 1),
(39, 3, 'tai nghe', '12312', 5000, 0, 'https://bcec.vn/upload/original-image/cdn1/images/202203/source_img/tai-nghe-sony-wh-ch510-P6697-1647506281221.jpg', '2026-04-27 07:59:06', 100, 0),
(40, 1, 'đt1', 'tét', 100, 0, 'https://cdnv2.tgdd.vn/mwg-static/common/News/1576531/dien-thoai-1.jpg', '2026-04-27 08:00:16', 1, 1),
(41, 1, 'đt2', '213', 2, 0, 'https://product.hstatic.net/1000406564/product/iphonexs-1_a39c946bbe6140d9b301585f9df3b381_master.jpg', '2026-04-27 08:00:34', 2, 0),
(42, 1, 'đt3', 'https://baotinmobile.vn/uploads/2025/09/iphone-17-2-400x400.jpg', 444, 0, 'https://baotinmobile.vn/uploads/2025/09/iphone-17-2-400x400.jpg', '2026-04-27 08:00:48', 34, 0),
(43, 1, 'đt4', '123', 444, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSHfl_UDPsnLYti9Tkr8MEL7IFGkUSCmpSVvA&s', '2026-04-27 08:01:06', 44, 0),
(44, 1, 'đt5', '132321', 5, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsb6R_xy6w2yrLYK4ElwzrN47GGjFqSDToSw&s', '2026-04-27 08:01:20', 55, 0),
(45, 1, 'đt6', '2313221', 666, 0, 'https://img.websosanh.vn/v10/users/review/images/1cpby8my64idx/iphone-12-040421-030410-e1615532134331.jpg?compress=85', '2026-04-27 08:01:35', 66, 0),
(46, 1, 'đt7', '23231', 7777, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQaE72AZZ79uFvZUUZrzwPxpZFBEXa-TVKmTw&s', '2026-04-27 08:01:46', 77, 0),
(47, 1, 'đt8', '13223', 888, 0, 'https://down-vn.img.susercontent.com/file/6eca5534f7e6588ca98921f3dd8977d8', '2026-04-27 08:01:58', 88, 1),
(48, 1, 'đt9', '32131', 99, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTygI2nmfIZ7df9aUAw1ht5amBv1tnNRQ8U1g&s', '2026-04-27 08:02:12', 99, 1),
(49, 1, 'điện thoại bình thường', '', 2000, 0, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpJXBGUrVeaTuH1PIQ0SyJA-sbvu0_V9yLHw&s', '2026-04-27 08:50:34', 0, 0);

CREATE TABLE users (
  id int(11) NOT NULL,
  fullname varchar(100) NOT NULL,
  email varchar(100) NOT NULL,
  phone varchar(15) DEFAULT NULL,
  address text DEFAULT NULL,
  password varchar(255) NOT NULL,
  role enum('user','admin') DEFAULT 'user',
  created_at timestamp NOT NULL DEFAULT current_timestamp(),
  avatar varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users (id, fullname, email, phone, address, password, role, created_at, avatar) VALUES
(7, 'admin', 'admin@gmail.com', 'không có', 'vô gia cư', '$2y$10$x6dTjI/uJj5P23HljypXEuZVhe3tfRLErakKkzThGWAGagwTQnOZ.', 'admin', '2026-04-23 05:31:15', 'user_7_1777125031.jpg'),
(8, 'user', 'user@gmail.com', '0776545527', 'ádsaccc', '$2y$10$x6dTjI/uJj5P23HljypXEuZVhe3tfRLErakKkzThGWAGagwTQnOZ.', 'user', '2026-04-23 05:31:15', 'user_8_1776927908.png'),
(9, 'con chó', 'nian@gmail.com', NULL, NULL, '$2y$10$TvzdAVG7vcFRi0SMwkx/qOgKseZpp4fIXC6dI.a/g3wJI9sHJOg6e', 'user', '2026-04-23 16:05:36', 'default.png'),
(10, 'đạt chó', 'vlxx@concac', NULL, NULL, '$2y$10$FpNASvinnDdaE/HSBer7tOf.wSlPNug7KyCdovO2jObrFBdmO7SI6', 'user', '2026-04-24 12:25:08', 'default.png');


ALTER TABLE categories
  ADD PRIMARY KEY (id);

ALTER TABLE orders
  ADD PRIMARY KEY (id),
  ADD KEY user_id (user_id);

ALTER TABLE order_details
  ADD PRIMARY KEY (order_id,product_id),
  ADD KEY product_id (product_id);

ALTER TABLE products
  ADD PRIMARY KEY (id),
  ADD KEY category_id (category_id);

ALTER TABLE users
  ADD PRIMARY KEY (id),
  ADD UNIQUE KEY email (email);


ALTER TABLE categories
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE orders
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE products
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE users
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE orders
  ADD CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES `users` (id) ON DELETE CASCADE;

ALTER TABLE order_details
  ADD CONSTRAINT order_details_ibfk_1 FOREIGN KEY (order_id) REFERENCES `orders` (id) ON DELETE CASCADE,
  ADD CONSTRAINT order_details_ibfk_2 FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE;

ALTER TABLE products
  ADD CONSTRAINT products_ibfk_1 FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
