CREATE DATABASE IF NOT EXISTS `u524077001_dmt`;
USE `u524077001_dmt`;

CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `avatar` varchar(255) DEFAULT '/default.png',
    `email` varchar(255) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `position` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `status` varchar(5) NOT NULL DEFAULT 'true',
    PRIMARY KEY (`id`)
);

DELIMITER $$
CREATE TRIGGER `insert_users_permissions` AFTER INSERT ON `users` FOR EACH ROW
BEGIN
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'users.delete');

    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'products.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'products.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'products.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'products.delete');

    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'sales.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'sales.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'sales.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'sales.delete');

    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'orders.create');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'orders.read');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'orders.update');
    INSERT INTO `users_permissions` (`user_id`, `permission`) VALUES (NEW.id, 'orders.delete');
END$$
DELIMITER ;

CREATE TABLE `users_permissions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `permission` varchar(255) NOT NULL,
    `status` varchar(5) NOT NULL DEFAULT 'false',
    PRIMARY KEY (`id`)
);

CREATE TABLE `users_logs` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `date` datetime NOT NULL,
    `action` varchar(82) NOT NULL,
    `description` JSON NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `price` DECIMAL(18,2) NOT NULL,
    `slug` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `sales` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `client_name` varchar(255) NOT NULL,
    `date` datetime NOT NULL,
    `payment_methods` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `sales_products` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sale_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `amount` int(11) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `orders` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `date` datetime NOT NULL,
    `amount` int(11) NOT NULL,
    `slug` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE `api_sessions` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires` datetime NOT NULL,
    PRIMARY KEY (`id`)
);


ALTER TABLE `users_permissions` ADD CONSTRAINT `users_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `users_logs` ADD CONSTRAINT `users_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `sales` ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `sales_products` ADD CONSTRAINT `sales_products_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;
ALTER TABLE `sales_products` ADD CONSTRAINT `sales_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
ALTER TABLE `orders` ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
ALTER TABLE `api_sessions` ADD CONSTRAINT `api_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

INSERT INTO `users` (`name`, `email`, `password`, `position`, `slug`) VALUES ("teste", "teste@sharpsolucoes.com", "$2y$12$4EF0zEKbVB4ZXWGLquI2T.Q0mtK2DGPuQoY93A1HXl5eX.HtKu6l2", "tester", "1-teste");
