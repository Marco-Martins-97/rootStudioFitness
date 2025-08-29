mysql -u root

CREATE DATABASE IF NOT EXISTS root_studio_fitness;

CREATE USER IF NOT EXISTS 'root_admin'@'localhost' IDENTIFIED BY 'admin';
GRANT ALL PRIVILEGES ON root_studio_fitness.* TO 'root_admin'@'localhost';
FLUSH PRIVILEGES;

--SHOW GRANTS FOR 'root_admin'@'localhost';

CREATE USER IF NOT EXISTS 'root_user'@'localhost' IDENTIFIED BY 'user';
GRANT SELECT, INSERT, UPDATE, DELETE ON root_studio_fitness.* TO 'root_user'@'localhost';
FLUSH PRIVILEGES;

--SHOW GRANTS FOR 'root_user'@'localhost';

CREATE USER IF NOT EXISTS 'root_guest'@'localhost' IDENTIFIED BY 'guest';
GRANT SELECT, INSERT ON root_studio_fitness.* TO 'root_guest'@'localhost';
FLUSH PRIVILEGES;

--SHOW GRANTS FOR 'root_guest'@'localhost';

-- SELECT user, host, plugin, authentication_string FROM mysql.user;

-- mysql -u root_admin -p

-- use root_studio_fitness;

CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    pwd VARCHAR(255) NOT NULL,
    userRole VARCHAR(16) NOT NULL DEFAULT 'user',
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);
