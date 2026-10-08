-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2026 at 06:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kdfdairyfarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(4, 'Butter Milk'),
(7, 'Cheese'),
(5, 'Curd'),
(2, 'Ghee'),
(1, 'MILK'),
(6, 'Paneer'),
(3, 'Sweet');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash on Delivery',
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `payment_method`, `status`, `created_at`) VALUES
(1, 2, 2400.00, 'Cash on Delivery', 'confirmed', '2026-08-06 16:22:13'),
(2, 2, 210.00, 'Cash on Delivery', 'confirmed', '2026-08-06 16:22:47'),
(3, 3, 560.00, 'Cash on Delivery', 'pending', '2026-08-07 16:35:24'),
(4, 3, 800.00, 'Cash on Delivery', 'pending', '2026-08-07 16:35:56'),
(5, 2, 300.00, 'Cash on Delivery', 'pending', '2026-08-13 07:20:45'),
(6, 8, 800.00, 'Cash on Delivery', 'cancelled', '2026-08-13 16:10:28'),
(7, 8, 3900.00, 'Cash on Delivery', 'confirmed', '2026-08-13 16:38:54'),
(8, 2, 160.00, 'Cash on Delivery', 'pending', '2026-08-13 17:00:11'),
(9, 3, 300.00, 'Cash on Delivery', 'pending', '2026-08-14 03:58:40'),
(10, 11, 300.00, 'Cash on Delivery', 'confirmed', '2026-08-14 04:04:08'),
(11, 12, 800.00, 'Cash on Delivery', 'cancelled', '2026-08-14 04:06:39'),
(12, 11, 700.00, 'Cash on Delivery', 'pending', '2026-08-14 04:26:25'),
(13, 11, 70.00, 'Cash on Delivery', 'pending', '2026-08-14 04:29:22'),
(14, 11, 80.00, 'Cash on Delivery', 'cancelled', '2026-08-14 04:36:54'),
(15, 2, 300.00, 'Cash on Delivery', 'cancelled', '2026-08-17 16:52:43'),
(16, 13, 2240.00, 'Cash on Delivery', 'confirmed', '2026-08-29 12:16:15'),
(17, 2, 40.00, 'Cash on Delivery', 'confirmed', '2026-08-29 12:19:35'),
(18, 13, 460.00, 'Cash on Delivery', 'pending', '2026-08-29 12:21:30'),
(19, 2, 700.00, 'Cash on Delivery', 'confirmed', '2026-09-15 09:21:52'),
(20, 11, 2000.00, 'Cash on Delivery', 'confirmed', '2026-10-08 04:36:54');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 1, 4, 3),
(2, 2, 2, 3),
(3, 3, 2, 8),
(4, 4, 1, 10),
(5, 5, 5, 1),
(6, 6, 4, 1),
(7, 7, 3, 3),
(8, 7, 5, 6),
(9, 8, 1, 2),
(10, 9, 5, 1),
(11, 10, 5, 1),
(12, 11, 4, 1),
(13, 12, 3, 1),
(14, 13, 2, 1),
(15, 14, 1, 1),
(16, 15, 5, 1),
(17, 16, 1, 1),
(18, 16, 3, 2),
(19, 16, 7, 2),
(20, 16, 5, 2),
(21, 17, 6, 1),
(22, 18, 1, 1),
(23, 18, 6, 2),
(24, 18, 5, 1),
(25, 19, 3, 1),
(26, 20, 10, 3),
(27, 20, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(30) DEFAULT 'unit',
  `stock` int(10) UNSIGNED DEFAULT 0,
  `image` varchar(255) DEFAULT 'default.jpg',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `description`, `price`, `unit`, `stock`, `image`, `status`, `created_at`) VALUES
(1, 'Buffalo Milk', 1, 'Buffalo milk is a thick, nutrient-rich dairy liquid produced by water buffaloes. It features significantly higher fat, protein, and calorie levels than cow\'s milk, alongside a bright white appearance and a creamy texture.\r\n\r\nBuffalo milk constitutes over 12% of the global milk production, and in the Indian subcontinent the greater part of the milk produced is by buffaloes (>53% in India and >68% in Pakistan, of the total milk production). Buffalo milk provides more energy per unit volume than provided by cow’s milk due to its higher fat and protein content. Due to the higher content of total solids, fat, proteins, and colloidal calcium, and the larger size of the casein micelles and fat globules, buffalo milk is well suited for fluid milk supply, fermented products (dahi, yogurt, shrikhand, lassi, leben, cremir, etc.), fat-rich dairy products (cream, butter, ghee, butter oil, etc.), heat desiccated and acid-coagulated products (khoa, paneer, casein, and caseinate), ice cream, and dairy whitener.', 80.00, 'Litre', 35, 'product_1786032843_775.png', 'active', '2026-08-06 16:14:03'),
(2, 'Cow Milk', 1, 'Cow milk is a nutrient-rich, opaque white liquid produced by the mammary glands of dairy cows, widely consumed for its balanced profile of proteins, fats, carbohydrates, and essential minerals.\r\n\r\nCows\' milk is a nutrient-dense food consisting of varying amounts of carbohydrate, fat, and protein. The major constituents of cows\' milk are water (87.4%) and milk solids (12.6%), which includes vitamins, minerals, carbohydrate, fat, and protein. Cows\' milk is known to contain many nutrients, including varying concentrations of fat and water-soluble vitamins, minerals, trace elements, and salts. The principal carbohydrate in milk is lactose. The lactose content of milk varies by species. Cows\' milk contains about 4.8% lactose (12–12.5 g lactose per cup), whereas human milk has 7% lactose. In addition to lactose, minor quantities of glucose, galactose, and oligosaccharides are present. Milk fat is a complex lipid existing as an oil-in-water emulsion. Milk lipids are mainly triacylglycerols or fatty acid esters with glycerol, of which more than 400 have been identified. Phospholipids, sterols, waxes, and free fatty acids have also been identified', 70.00, 'Litre', 28, 'product_1786032974_613.png', 'active', '2026-08-06 16:16:14'),
(3, 'Cow Ghee', 2, 'Cow ghee is a golden-yellow clarified butter made from cow\'s milk, prized for its nutty aroma, fine granular (danedar) texture, and deep roots in Indian cooking and Ayurvedic traditions\r\n\r\nNutrient Rich: Contains fat-soluble vitamins (A, D, E, K2) and healthy short-chain fatty acids like butyrate.\r\n\r\nDigestibility: Considered lighter on the gut and easier to absorb compared to other heavy fats.\r\n\r\nAyurvedic Value: Highly revered in traditional wellness for supporting immunity, vitality, and digestion.', 700.00, 'KG', 13, 'product_1786033134_399.png', 'active', '2026-08-06 16:18:54'),
(4, 'Buffalo Ghee', 2, 'Buffalo ghee is a rich, nutrient-dense clarified butter made exclusively from buffalo milk, prized for its thick texture, high-fat content, and long shelf life.\r\n\r\nCalorie & Fat Density: Higher in total fat and calories than cow ghee, making it an excellent choice for individuals looking to gain weight and boost energy.\r\n\r\nNutrient Rich: Naturally packed with beneficial butyric acid, calcium, phosphorus, and essential omega fatty acids.\r\n\r\n Contains lower moisture content, granting it a naturally longer shelf life than cow ghee.', 800.00, 'KG', 15, 'product_1786033241_119.png', 'active', '2026-08-06 16:20:41'),
(5, 'Penda', 3, 'Penda is a traditional Indian milk-based sweet that is especially popular in Gujarat and other parts of India. It is prepared using high-quality milk, sugar, and traditional ingredients such as cardamom. The milk is slowly cooked until it becomes thick and creamy, giving Penda its characteristic soft and slightly grainy texture.\r\n\r\nPenda has a rich, milky flavor with a pleasant sweetness and a delicious aroma. Depending on the recipe, it may also be flavored with cardamom, saffron, or garnished with chopped almonds and pistachios. Its round, small shape makes it convenient to serve and enjoy.\r\n\r\nPenda is commonly prepared during festivals, religious occasions, weddings, family celebrations, and special events. It is also a popular choice for gifting to family and friends. Fresh dairy milk gives Penda its rich taste and makes it a special traditional sweet for dairy product lovers.', 300.00, 'KG', 27, 'product_1786506958_702.webp', 'active', '2026-08-12 03:55:58'),
(6, 'Butter Milk', 4, 'The definition of buttermilk is different from the country to country. Buttermilk is sometimes associated or even confused with sour milk, natural (conventional) buttermilk, cultured milk, cultured buttermilk, and cultured skim milk or even sometimes with fermented milk. Buttermilk is a liquid that releases during the churning of cream in a butter production. Buttermilk contains water-soluble components like milk protein, lactose, and minerals. In addition, what is specific for buttermilk, it encloses material that derives from milk fat globule membrane (MFGM) and is disrupted during the churning process and mainly passes in to the buttermilk fraction. Because of the presence of MFGM material, buttermilk contains more phospholipids than milk. Phospholipids in buttermilk constitute about one-third of the MFGM dry matter. Literature quotes that buttermilk has seven times more phospholipids than whole milk and their concentrations are 0.89 mg/g in buttermilk and 0.12 mg/g in whole milk (Libudzisz and Stepaniak, 2002). This high content of phospholipids in buttermilk makes it interesting dairy ingredient as a functional component in food industry because they have very good emulsifying properties (Wong and Kitts, 2003). Phospholipids have shown some bioactivity. Some studies have demonstrated the anticarcinogenic potential of phospholipids, especially against colon cancer, and their protective effect against bacterial toxins and infection (Libudzisz and Stepaniak, 2002).', 40.00, 'Litre', 17, 'product_1787804437_842.png', 'active', '2026-08-27 04:14:56'),
(7, 'Curd', 5, 'Curd is a traditional dairy product made by fermenting milk with beneficial lactic acid bacteria. It has a smooth, creamy texture and a mildly sour taste. Curd is commonly consumed with meals and is also used in dishes such as raita, kadhi, lassi, and chaas.\r\nFresh Curd – Enjoy our fresh and creamy curd made from high-quality milk. Naturally fermented to develop a smooth texture and delicious taste, our curd is a nutritious addition to your everyday meals. Perfect for raita, kadhi, lassi, chaas, or simply enjoying with your favorite food.', 80.00, 'KG', 15, 'product_1787805057_801.png', 'active', '2026-08-27 04:30:57'),
(9, 'Paneer', 6, 'Fresh Paneer is a soft, creamy, and protein-rich Indian cottage cheese made from fresh milk. It has a mild taste and smooth texture, making it perfect for a variety of dishes such as Paneer Butter Masala, Palak Paneer, Shahi Paneer, Paneer Tikka, and sandwiches. Our paneer is freshly prepared, hygienically packed, and suitable for everyday cooking.', 350.00, 'KG', 19, 'product_1791433781_882.jpg', 'active', '2026-10-08 04:29:41'),
(10, 'Cheese', 7, 'Fresh Cheese is a delicious, creamy, and nutritious dairy product made from high-quality fresh milk. It has a smooth texture, rich flavor, and excellent melting properties, making it suitable for a wide variety of dishes. Cheese is a good source of protein, calcium, and other essential nutrients that support a healthy and balanced diet.\r\n\r\nOur cheese is prepared and packed under hygienic conditions to maintain its freshness, taste, and quality. It can be used in pizza, burgers, sandwiches, pasta, sandwiches, wraps, salads, grilled dishes, and many other recipes. Its creamy texture and rich taste add extra flavor to both traditional and modern meals.', 550.00, 'KG', 7, 'product_1791434037_724.jpg', 'active', '2026-10-08 04:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_details`
--

CREATE TABLE `shipping_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `name` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_details`
--

INSERT INTO `shipping_details` (`id`, `order_id`, `name`, `phone`, `address`) VALUES
(1, 1, 'Shyam Bodar', '8780476673', 'Jamngar'),
(2, 2, 'Shyam Bodar', '8780476673', 'jamngar'),
(3, 3, 'karan', '6351564171', 'lalpur'),
(4, 4, 'karan', '6351564171', 'lalpur'),
(5, 5, 'Shyam Bodar', '8780476673', 'jamnager'),
(6, 6, 'divyesh', '9016343753', 'jamnager'),
(7, 7, 'divyesh', '9016343753', 'jamnagar'),
(8, 8, 'Shyam Bodar', '8780476673', 'Ahir Bording'),
(9, 9, 'karan', '6351564171', 'jamnager'),
(10, 10, 'k yadav', '9016343753', 'jamnagar'),
(11, 11, 'sumit', '6351564171', 'jamangar'),
(12, 12, 'k yadav', '6351564171', 'lalpur'),
(13, 13, 'k yadav', '6351564171', 'lalpur'),
(14, 14, 'k yadav', '6351564171', 'rajkot'),
(15, 15, 'Shyam Bodar', '8780476673', 'jamnager'),
(16, 16, 'Bodar', '6331395773', 'Dharampur'),
(17, 17, 'Shyam Bodar', '8780476673', 'dharampur'),
(18, 18, 'Bodar', '6331395773', 'Dharampur'),
(19, 19, 'Shyam Bodar', '8780476673', 'jamnager'),
(20, 20, 'k yadav', '6351564171', 'jamnagar');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `address`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@dairyfarm.com', '9999999999', 'admin123', 'Jamnager', 'admin', '2026-08-06 16:07:43'),
(2, 'Shyam Bodar', 'shyambodar543@gmail.com', '8780476673', 'shyam123', 'Ahir Boding jamnager', 'user', '2026-08-06 16:21:38'),
(3, 'karan', 'kyadav8537@gmail.com', '6351564171', 'karan123', NULL, 'user', '2026-08-07 16:34:58'),
(4, 'jay', 'jay12@gmail.com', '6351451051', 'Jay@123', 'Dhrampur \r\nTA: Lalpur', 'user', '2026-08-13 04:07:39'),
(8, 'divyesh', 'ambaliyadivyesh3470@gmail.com', '9016343753', 'abc123', NULL, 'user', '2026-08-13 16:09:24'),
(11, 'k yadav', 'karanbodar9603@gmail.com', '9904066321', 'k12345', NULL, 'user', '2026-08-14 04:03:44'),
(12, 'sumit ', 'sonagarasumit86@gmail.com', '990401245', 'sumit123', 'sumit123', 'user', '2026-08-14 04:05:43'),
(13, 'Bodar', 'priyankabodar43@gmail.com', '6351395773', 'priyanka', NULL, 'user', '2026-08-29 12:14:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `shipping_details`
--
ALTER TABLE `shipping_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shipping_details`
--
ALTER TABLE `shipping_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `shipping_details`
--
ALTER TABLE `shipping_details`
  ADD CONSTRAINT `shipping_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
