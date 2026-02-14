-- Favoriler tablosu (opsiyonel)
CREATE TABLE IF NOT EXISTS bull_favorites (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  bull_id INT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_bull (user_id, bull_id),
  KEY idx_user (user_id),
  KEY idx_bull (bull_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
