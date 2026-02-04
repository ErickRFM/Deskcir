

DROP DATABASE IF EXISTS DataMTVAwards;
CREATE DATABASE DataMTVAwards CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE DataMTVAwards;


CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO roles (id, name) VALUES
(1,'Administrator'),
(2,'Manager'),
(3,'Audience')
ON DUPLICATE KEY UPDATE name=VALUES(name);


CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  display_name VARCHAR(150),
  role_id INT NOT NULL DEFAULT 3,
  avatar VARCHAR(255),
  status TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);


-- Password real con HASH correcto para MySQL:
-- password = "mtvadmin2025"

INSERT INTO users (username, email, password, display_name, role_id)
VALUES (
  'admin',
  'admin@mtv.local',
  SHA2('mtvadmin2025', 256),
  'Administrator MTV',
  1
)
ON DUPLICATE KEY UPDATE username=username;


CREATE TABLE genres (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
);

INSERT IGNORE INTO genres (name) VALUES
('Pop'),('Rock'),('Hip Hop'),('Electronic'),('R&B');


CREATE TABLE artists (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  pseudonym VARCHAR(150),
  sex VARCHAR(20),
  nationality VARCHAR(100),
  biography TEXT,
  photo VARCHAR(255),
  status TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO artists (name,pseudonym,sex,nationality,biography,photo) VALUES
('Juan Pérez','JP','M','MX','Artista de ejemplo','img/artists/juan.jpg');


CREATE TABLE albums (
  id INT AUTO_INCREMENT PRIMARY KEY,
  artist_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  release_date DATE,
  description TEXT,
  cover VARCHAR(255),
  genre_id INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
  FOREIGN KEY (genre_id) REFERENCES genres(id)
);

INSERT INTO albums (artist_id,title,release_date,description,cover,genre_id) VALUES
(1,'Álbum Demo','2023-05-01','Descripción demo','img/albums/album_demo.jpg',1);


CREATE TABLE songs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  album_id INT,
  title VARCHAR(255) NOT NULL,
  release_date DATE,
  genre_id INT,
  mp3_url VARCHAR(500),
  video_url VARCHAR(500),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
  FOREIGN KEY (genre_id) REFERENCES genres(id)
);

INSERT INTO songs (album_id,title,release_date,genre_id,mp3_url,video_url) VALUES
(1,'Canción Demo','2023-05-01',1,'','');

CREATE TABLE nominations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  type ENUM('artist','album') NOT NULL,
  category VARCHAR(150),
  start_date DATETIME,
  end_date DATETIME,
  status ENUM('active','closed','finished') DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO nominations (title,type,category,start_date,end_date,status) VALUES
('Mejor Artista del Año','artist','Mejor Artista','2024-12-01 00:00:00','2024-12-31 23:59:59','active');


CREATE TABLE nomination_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nomination_id INT NOT NULL,
  artist_id INT NULL,
  album_id INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (nomination_id) REFERENCES nominations(id) ON DELETE CASCADE,
  FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE SET NULL,
  FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
);

INSERT INTO nomination_items (nomination_id,artist_id) VALUES (1,1);


CREATE TABLE votes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  nomination_id INT NOT NULL,
  nomination_item_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (nomination_id) REFERENCES nominations(id) ON DELETE CASCADE,
  FOREIGN KEY (nomination_item_id) REFERENCES nomination_items(id) ON DELETE CASCADE,
  UNIQUE KEY unique_vote (user_id, nomination_id)
);
