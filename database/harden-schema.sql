SET NAMES utf8mb4;

-- Run once per environment. This script assumes the columns/indexes do not already exist.

-- Users hardening
ALTER TABLE users
  ADD COLUMN last_login_at DATETIME NULL AFTER status,
  ADD COLUMN failed_login_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0 AFTER last_login_at,
  ADD COLUMN locked_until DATETIME NULL AFTER failed_login_attempts;

ALTER TABLE users
  ADD INDEX idx_users_status_role (status, role),
  ADD INDEX idx_users_locked_until (locked_until);

-- Services hardening
ALTER TABLE services
  ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER status;

ALTER TABLE services
  ADD INDEX idx_services_sort_order_name (sort_order, name),
  ADD INDEX idx_services_is_active (is_active),
  ADD INDEX idx_services_status (status);

-- Posts hardening
ALTER TABLE posts
  ADD COLUMN slug VARCHAR(191) NULL AFTER title,
  ADD COLUMN is_published TINYINT(1) NOT NULL DEFAULT 1 AFTER author,
  ADD COLUMN deleted_at DATETIME NULL AFTER published_at;

ALTER TABLE posts
  ADD UNIQUE INDEX uq_posts_slug (slug),
  ADD INDEX idx_posts_visibility_date (is_published, published_at),
  ADD INDEX idx_posts_author_date (author, published_at),
  ADD INDEX idx_posts_deleted_at (deleted_at),
  ADD FULLTEXT INDEX idx_posts_fulltext (title, excerpt, content);

-- Likes and comments
CREATE TABLE IF NOT EXISTS post_likes (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id VARCHAR(64) NOT NULL,
  username VARCHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_post_likes_post_user (post_id, username),
  INDEX idx_post_likes_post_id (post_id),
  INDEX idx_post_likes_username (username),
  CONSTRAINT fk_post_likes_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_likes_user FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS post_comments (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  post_id VARCHAR(64) NOT NULL,
  username VARCHAR(64) NOT NULL,
  content TEXT NOT NULL,
  status ENUM('visible', 'hidden') NOT NULL DEFAULT 'visible',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_post_comments_post_created (post_id, created_at),
  INDEX idx_post_comments_user (username),
  INDEX idx_post_comments_status (status),
  CONSTRAINT fk_post_comments_post FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  CONSTRAINT fk_post_comments_user FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
