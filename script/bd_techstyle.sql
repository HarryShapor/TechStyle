-- Database: TechStyle

-- DROP DATABASE IF EXISTS "TechStyle";

CREATE DATABASE "techstyle"
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    TEMPLATE = template0
    LC_COLLATE = 'ru_RU.UTF-8'
    LC_CTYPE = 'ru_RU.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;
CREATE SEQUENCE user_id_seq;
CREATE TABLE Users (id int DEFAULT nextval('user_id_seq') PRIMARY KEY NOT NULL, 
						full_name varchar(80) NOT NULL,
						birthday date,
						phone varchar(20),
						mail varchar(30) UNIQUE NOT NULL,
						password varchar(30) NOT NULL,
						card varchar(30),
						address_delivery varchar(80),
						user_discount int default 0,
						role_user varchar(15) NOT NULL)

CREATE TABLE Products (article varchar(30) PRIMARY KEY NOT NULL,
						name_p varchar(80) NOT NULL,
						icon varchar(80) NOT NULL,
						description varchar(200),
						country varchar(30),
						price float(5) default 0 NOT NULL,
						quantity int default 0,
						warehouse_address varchar(80) NOT NULL)

CREATE TABLE Stores (address varchar(80) PRIMARY KEY NOT NULL,
						holder varchar(80) NOT NULL,
						w_a varchar(80) UNIQUE NOT NULL)

CREATE TABLE Basket (id_user int NOT NULL,
						article_p varchar(30) NOT NULL,
						count_p int default 1,
						cost_p money NOT NULL,
						date_create TIMESTAMP WITH TIME ZONE NOT NULL,
						payment_status boolean default false,
						address_store varchar(80) NOT NULL,
						PRIMARY KEY (id_user, article_p, date_create),
						FOREIGN KEY (id_user) REFERENCES Users (id) ON DELETE CASCADE,
						FOREIGN KEY (article_p) REFERENCES Products (article) ON DELETE CASCADE,
						FOREIGN KEY (address_store) REFERENCES Stores (address) ON DELETE CASCADE)

CREATE TABLE Orders (id_user int NOT NULL,
						article_p varchar(30) NOT NULL,
						price money NOT NULL,
						data_purchase TIMESTAMP WITH TIME ZONE NOT NULL,
						PRIMARY KEY (id_user, article_p, data_purchase),
						FOREIGN KEY (id_user) REFERENCES Users (id) ON DELETE CASCADE,
						FOREIGN KEY (article_p) REFERENCES Products (article) ON DELETE CASCADE)

-- DROP TABLE Users
-- DROP TABLE Products
-- DROP TABLE Basket
-- DROP TABLE Stores
-- DROP TABLE Orders

SET LC_MONETARY = 'ru_RU.UTF-8'
INSERT INTO Products (article, name_p, icon, description, country, price, quantity, warehouse_address) 
VALUES
('ART001', 'Чехол-книжка на Apple iPhone 15 Pro', '..\images\P1.jpg', 'Description of Product One', 'USA', 19.99 , 100, 'Warehouse A'),
('ART002', 'Чехол на iphone 11 прозрачный', '..\images\P2.jpg', 'Description of Product Two', 'Canada', 29.99, 200, 'Warehouse B'),
('ART003', 'Чехол Shield Case для Apple iPhone 16 Pro Max', '..\images\P3.jpg', 'Description of Product Three', 'Germany', 39.99, 150, 'Warehouse C'),
('ART004', 'Чехол для iPhone 11 силиконовый', '..\images\P4.jpg', 'Description of Product Four', 'France', 49.99, 300, 'Warehouse D'),
('ART005', 'Чехол на Apple iPhone 14 MagSafe', '..\images\P5.jpg', 'Description of Product Five', 'Italy', 59.99, 250, 'Warehouse E'),
('ART006', 'Чехол на iPhone 16', '..\images\P6.jpg', 'Description of Product Six', 'Spain', 69.99, 350, 'Warehouse F'),
('ART007', 'Чехол на iPhone 14 Pro Max', '..\images\P7.jpg', 'Description of Product Seven', 'UK', 79.99, 400, 'Warehouse G'),
('ART008', 'Чехол Iphone 12 эффект металлик', '..\images\P8.jpg', 'Description of Product Eight', 'Japan', 89.99, 450, 'Warehouse H'),
('ART009', 'Чехол на Iphone 14 Pro Max Magsafe', '..\images\P9.jpg', 'Description of Product Nine', 'China', 99.99, 500, 'Warehouse I'),
('ART010', 'Чехол красный на Iphone 11', '..\images\P10.jpg', 'Description of Product Ten', 'Australia', 109.99, 550, 'Warehouse J');

-- DELETE FROM Products	

-- SELECT * FROM Products

-- SELECT * FROM Users

-- SELECT * FROM Orders

INSERT INTO Users
VALUES (49,'admin',null,null,'admin@mail.ru', 'admin123', null, null, null, 'admin')

-- DELETE FROM Users
-- WHERE id = 49