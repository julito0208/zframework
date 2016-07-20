CREATE TABLE IF NOT EXISTS zf_file (
  id_file VARCHAR(255) primary key,
  title VARCHAR(255),
  path VARCHAR(255),
  mimetype VARCHAR(255),
  file_size INTEGER UNSIGNED DEFAULT 0
);