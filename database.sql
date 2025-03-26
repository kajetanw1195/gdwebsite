CREATE DATABASE golebi_dwor;

USE golebi_dwor;

CREATE TABLE services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  description TEXT
);

CREATE TABLE contacts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example insert:
INSERT INTO services (title, description) VALUES
('Dom Seniora', 'Profesjonalna opieka całodobowa dla seniorów'),
('Rehabilitacja', 'Nowoczesna rehabilitacja neurologiczna'); 