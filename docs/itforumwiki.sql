-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 10:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE
= "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone
= "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `itforumwiki`
--

-- --------------------------------------------------------

--
-- Table structure for table `flagged_comments`
--

CREATE TABLE `flagged_comments`
(
  `id` int
(11) NOT NULL,
  `comment_id` int
(11) NOT NULL,
  `flagged_by` int
(11) DEFAULT NULL,
  `flagged_at` datetime NOT NULL DEFAULT current_timestamp
()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flagged_posts`
--

CREATE TABLE `flagged_posts`
(
  `id` int
(11) NOT NULL,
  `post_id` int
(11) NOT NULL,
  `flagged_by` int
(11) DEFAULT NULL,
  `flagged_at` datetime NOT NULL DEFAULT current_timestamp
()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts`
(
  `id` int
(11) NOT NULL,
  `author_id` int
(11) DEFAULT NULL,
  `title` varchar
(150) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp
(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp
() ON
UPDATE current_timestamp(),
  `is_wiki_entry
` tinyint
(1) NOT NULL DEFAULT 0,
  `wiki_category_id` int
(11) DEFAULT NULL,
  `answer_comment_id` int
(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`
id`,
`author_id
`, `title`, `content`, `created_at`, `updated_at`, `is_wiki_entry`, `wiki_category_id`, `answer_comment_id`) VALUES
(1, 1, 'Root User Test Post | Title Here', 'Root User Test Post - Content Here', '2025-04-23 09:27:45', '2025-04-28 12:58:40', 1, NULL, NULL),
(2, NULL, 'Deleted User Test Post', 'Deleted User Test Post Deleted User Test Post Deleted User Test Post Deleted User Test Post Deleted User Test Post', '2025-04-25 09:04:07', '2025-04-28 12:57:47', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments`
(
  `id` int
(11) NOT NULL,
  `post_id` int
(11) NOT NULL,
  `author_id` int
(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp
()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`
id`,
`post_id
`, `author_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'Test Comment', '2025-04-23 09:32:32');

-- --------------------------------------------------------

--
-- Table structure for table `post_reactions`
--

CREATE TABLE `post_reactions`
(
  `id` int
(11) NOT NULL,
  `post_id` int
(11) NOT NULL,
  `user_id` int
(11) NOT NULL,
  `reaction_type` enum
('upvote','downvote') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_reactions`
--

INSERT INTO `post_reactions` (`
id`,
`post_id
`, `user_id`, `reaction_type`) VALUES
(1, 2, 1, 'downvote');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users`
(
  `id` int
(11) NOT NULL,
  `role` enum
('user','moderator','admin') NOT NULL DEFAULT 'user',
  `username` varchar
(20) NOT NULL,
  `email` varchar
(100) NOT NULL,
  `password` varchar
(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp
(),
  `profile_image_path` varchar
(255) NOT NULL DEFAULT 'uploads/user_avatars/default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`
id`,
`role
`, `username`, `email`, `password`, `created_at`, `profile_image_path`) VALUES
(1, 'admin', 'root', 'root@gmail.com', '$2y$10$JHZnCkPblwnmGPo5i3VoL./qsi45ebi5E7KHLYliJbH5vmi/rx/Lu', '2025-04-25 15:12:41', 'uploads/user_avatars/default.png'),
(2, 'user', 'jonathan', 'jonathan@gmail.com', '$2y$10$8JyuUIYWjhqV.pgBA3MzFOr14VnNYsbK3CAoeu0piagbPUz9c966i', '2025-04-25 12:06:32', 'uploads/user_avatars/default.png');

-- --------------------------------------------------------

--
-- Table structure for table `user_bookmarks`
--

CREATE TABLE `user_bookmarks`
(
  `id` int
(11) NOT NULL,
  `user_id` int
(11) NOT NULL,
  `post_id` int
(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_bookmarks`
--

INSERT INTO `user_bookmarks` (`
id`,
`user_id
`, `post_id`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `wiki_categories`
--

CREATE TABLE `wiki_categories`
(
  `id` int
(11) NOT NULL,
  `name` varchar
(150) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wiki_categories`
--

INSERT INTO `wiki_categories` (`
id`,
`name
`, `description`) VALUES
(1, 'Netzwerk', 'Grundlagen zu Netzwerktechnologien'),
(3, 'Programmieren', 'Tipps & Tricks rund ums Coden'),
(4, 'Betriebssysteme', 'Infos zu Linux, Windows & Co.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `flagged_comments`
--
ALTER TABLE `flagged_comments`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_flagged_comments_comment_id`
(`comment_id`),
ADD KEY `fk_flagged_comments_flagged_by`
(`flagged_by`);

--
-- Indexes for table `flagged_posts`
--
ALTER TABLE `flagged_posts`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_flagged_posts_post_id`
(`post_id`),
ADD KEY `fk_flagged_posts_flagged_by`
(`flagged_by`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_posts_author_id`
(`author_id`),
ADD KEY `fk_posts_wiki_category_id`
(`wiki_category_id`),
ADD KEY `fk_posts_wiki_answer_comment_id`
(`answer_comment_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_comments_post_id`
(`post_id`),
ADD KEY `fk_comments_author_id`
(`author_id`);

--
-- Indexes for table `post_reactions`
--
ALTER TABLE `post_reactions`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_post_reactions_post_id`
(`post_id`),
ADD KEY `fk_post_reactions_user_id`
(`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
ADD PRIMARY KEY
(`id`),
ADD UNIQUE KEY `username`
(`username`),
ADD UNIQUE KEY `email`
(`email`);

--
-- Indexes for table `user_bookmarks`
--
ALTER TABLE `user_bookmarks`
ADD PRIMARY KEY
(`id`),
ADD KEY `fk_user_bookmarks_post_id`
(`post_id`),
ADD KEY `fk_user_bookmarks_user_id`
(`user_id`);

--
-- Indexes for table `wiki_categories`
--
ALTER TABLE `wiki_categories`
ADD PRIMARY KEY
(`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `flagged_comments`
--
ALTER TABLE `flagged_comments`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `flagged_posts`
--
ALTER TABLE `flagged_posts`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `post_reactions`
--
ALTER TABLE `post_reactions`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_bookmarks`
--
ALTER TABLE `user_bookmarks`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wiki_categories`
--
ALTER TABLE `wiki_categories`
  MODIFY `id` int
(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flagged_comments`
--
ALTER TABLE `flagged_comments`
ADD CONSTRAINT `fk_flagged_comments_comment_id` FOREIGN KEY
(`comment_id`) REFERENCES `post_comments`
(`id`) ON
DELETE CASCADE,
ADD CONSTRAINT `fk_flagged_comments_flagged_by` FOREIGN KEY
(`flagged_by`) REFERENCES `users`
(`id`) ON
DELETE
SET NULL;

--
-- Constraints for table `flagged_posts`
--
ALTER TABLE `flagged_posts`
ADD CONSTRAINT `fk_flagged_posts_flagged_by` FOREIGN KEY
(`flagged_by`) REFERENCES `users`
(`id`) ON
DELETE
SET NULL
,
ADD CONSTRAINT `fk_flagged_posts_post_id` FOREIGN KEY
(`post_id`) REFERENCES `posts`
(`id`) ON
DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
ADD CONSTRAINT `fk_posts_author_id` FOREIGN KEY
(`author_id`) REFERENCES `users`
(`id`) ON
DELETE
SET NULL
,
ADD CONSTRAINT `fk_posts_wiki_answer_comment_id` FOREIGN KEY
(`answer_comment_id`) REFERENCES `post_comments`
(`id`) ON
DELETE
SET NULL
,
ADD CONSTRAINT `fk_posts_wiki_category_id` FOREIGN KEY
(`wiki_category_id`) REFERENCES `wiki_categories`
(`id`) ON
DELETE
SET NULL;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
ADD CONSTRAINT `fk_comments_author_id` FOREIGN KEY
(`author_id`) REFERENCES `users`
(`id`) ON
DELETE
SET NULL
,
ADD CONSTRAINT `fk_comments_post_id` FOREIGN KEY
(`post_id`) REFERENCES `posts`
(`id`) ON
DELETE CASCADE;

--
-- Constraints for table `post_reactions`
--
ALTER TABLE `post_reactions`
ADD CONSTRAINT `fk_post_reactions_post_id` FOREIGN KEY
(`post_id`) REFERENCES `posts`
(`id`) ON
DELETE CASCADE,
ADD CONSTRAINT `fk_post_reactions_user_id` FOREIGN KEY
(`user_id`) REFERENCES `users`
(`id`) ON
DELETE CASCADE;

--
-- Constraints for table `user_bookmarks`
--
ALTER TABLE `user_bookmarks`
ADD CONSTRAINT `fk_user_bookmarks_post_id` FOREIGN KEY
(`post_id`) REFERENCES `posts`
(`id`) ON
DELETE CASCADE,
ADD CONSTRAINT `fk_user_bookmarks_user_id` FOREIGN KEY
(`user_id`) REFERENCES `users`
(`id`) ON
DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
