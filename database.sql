CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE links (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT 1,
  code VARCHAR(20) UNIQUE NOT NULL,
  original_url TEXT NOT NULL,
  title VARCHAR(255) DEFAULT NULL,
  clicks INT DEFAULT 0,
  password VARCHAR(255) DEFAULT NULL,
  expires_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_code (code),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE click_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  link_id INT NOT NULL,
  ip VARCHAR(45),
  country VARCHAR(10) DEFAULT 'BD',
  device VARCHAR(20),
  referer TEXT,
  clicked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- অ্যাডমিন + আপনার 2টা লিংক
INSERT INTO users (id, email, password) VALUES 
(1, 'booeb.com@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO links (user_id, code, original_url, title) VALUES
(1, 'bth', 'https://bithilikha.bolt.host/', 'BithiLikha Bolt'),
(1, 'btl', 'https://bithilikha.lovable.app/', 'BithiLikha Lovable');
