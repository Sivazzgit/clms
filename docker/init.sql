-- Initialize databases
CREATE DATABASE IF NOT EXISTS catlmain;
CREATE DATABASE IF NOT EXISTS anahaw;
CREATE DATABASE IF NOT EXISTS myorgap6_bareilly;
CREATE DATABASE IF NOT EXISTS kancor;

-- Grant all privileges to anahaw user on all databases
GRANT ALL PRIVILEGES ON anahaw.* TO 'anahaw'@'%' IDENTIFIED BY 'anahaw';
GRANT ALL PRIVILEGES ON catlmain.* TO 'anahaw'@'%' IDENTIFIED BY 'anahaw';
GRANT ALL PRIVILEGES ON myorgap6_bareilly.* TO 'anahaw'@'%' IDENTIFIED BY 'anahaw';
GRANT ALL PRIVILEGES ON kancor.* TO 'anahaw'@'%' IDENTIFIED BY 'anahaw';
FLUSH PRIVILEGES;

USE anahaw;

-- Create sample table to verify database connection works
CREATE TABLE IF NOT EXISTS `my_chart_data` (
  `category` date NOT NULL,
  `value1` int(11) NOT NULL,
  `value2` int(11) NOT NULL,
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `my_chart_data` (`category`, `value1`, `value2`) VALUES
('2013-08-24', 417, 127),
('2013-08-25', 417, 356),
('2013-08-26', 531, 585),
('2013-08-27', 333, 910),
('2013-08-28', 552, 30),
('2013-08-29', 492, 371),
('2013-08-30', 379, 781),
('2013-08-31', 767, 494),
('2013-09-01', 169, 364),
('2013-09-02', 314, 476),
('2013-09-03', 437, 759);
