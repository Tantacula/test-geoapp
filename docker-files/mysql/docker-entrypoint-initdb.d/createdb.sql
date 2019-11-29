CREATE DATABASE IF NOT EXISTS `homestead` COLLATE 'utf8mb4_general_ci' ;
GRANT ALL ON `homestead`.* TO 'homestead'@'%' ;
ALTER USER 'homestead'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';

FLUSH PRIVILEGES ;
