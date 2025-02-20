-- Database: techstyle

-- DROP DATABASE IF EXISTS techstyle;

CREATE DATABASE techstyle
    WITH
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'Russian_Russia.1251'
    LC_CTYPE = 'Russian_Russia.1251'
    LOCALE_PROVIDER = 'libc'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

CREATE TABLE Users (id int PRIMARY KEY NOT NULL, 
						full_name varchar(80) NOT NULL,
						birthday date NOT NULL,
						phone varchar(20),
						mail varchar(30) NOT NULL,
						password_user varchar(30) NOT NULL,
						card varchar(30),
						address_delivery varchar(80) NOT NULL,
						user_discount int default 0,
						role_user varchar(15) NOT NULL)

CREATE TABLE Products (article varchar(30) PRIMARY KEY NOT NULL,
						name_p varchar(80) NOT NULL,
						icon varchar(80) NOT NULL,
						description varchar(200),
						country varchar(30),
						price money NOT NULL,
						quantity int default 0,
						warehouse_address varchar(80) UNIQUE NOT NULL)

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
						

CREATE TABLE Stores (address varchar(80) PRIMARY KEY NOT NULL,
						holder varchar(80) NOT NULL,
						w_a varchar(80),
						FOREIGN KEY (w_a) REFERENCES Products (warehouse_address))

CREATE TABLE Orders (id_user int NOT NULL,
						article_p varchar(30) NOT NULL,
						price money NOT NULL,
						data_purchase TIMESTAMP WITH TIME ZONE NOT NULL,
						PRIMARY KEY (id_user, article_p, data_purchase),
						FOREIGN KEY (id_user) REFERENCES Users (id) ON DELETE CASCADE,
						FOREIGN KEY (article_p) REFERENCES Products (article) ON DELETE CASCADE)