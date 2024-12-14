DROP DATABASE IF EXISTS xlib;
CREATE DATABASE xlib;
USE xlib;

DROP TABLE IF EXISTS checkout;
DROP TABLE IF EXISTS reader;
DROP TABLE IF EXISTS book;
DROP TABLE IF EXISTS user;
DROP TABLE IF EXISTS librarian;
DROP TABLE IF EXISTS shelf;

-- First, create tables with no foreign key dependencies
CREATE TABLE shelf (
    shelfID INT PRIMARY KEY AUTO_INCREMENT,
    shelfName VARCHAR(100) NOT NULL
);

CREATE TABLE librarian (
    librarianID INT PRIMARY KEY AUTO_INCREMENT,
    librarianName VARCHAR(100) NOT NULL,
    l_username VARCHAR(100) NOT NULL,
    l_password VARCHAR(100) NOT NULL,
    l_email VARCHAR(100) NOT NULL,
    l_phone VARCHAR(100) NOT NULL,
    l_address VARCHAR(100) NOT NULL
);

CREATE TABLE user (
    u_username VARCHAR(100) PRIMARY KEY,
    u_password VARCHAR(100) NOT NULL,
    u_fullname VARCHAR(100) NOT NULL,
    u_email VARCHAR(100) NOT NULL,
    u_phone VARCHAR(100) NOT NULL,
    u_address VARCHAR(100) NOT NULL
);

-- Then create book table which depends on shelf
CREATE TABLE book (
    ISBN VARCHAR(13) PRIMARY KEY,
    Picture mediumblob DEFAULT NULL,
    Title VARCHAR(100) NOT NULL,
    Author VARCHAR(100) NOT NULL,
    Publisher VARCHAR(100) NOT NULL,
    PublicationYear INT NOT NULL,
    Genre VARCHAR(100) NOT NULL,
    Synopsis VARCHAR(255) NOT NULL,
    b_status VARCHAR(20) NOT NULL DEFAULT 'Available',
    shelfID INT NOT NULL,
    FOREIGN KEY (shelfID) REFERENCES shelf(shelfID)
);

-- Then create reader table which depends on book
CREATE TABLE reader (
    readerID INT PRIMARY KEY AUTO_INCREMENT,
    readerName VARCHAR(100) NOT NULL,
    ReadDate DATE NOT NULL DEFAULT CURRENT_DATE,
    ISBN VARCHAR(13) NOT NULL,
    FOREIGN KEY (ISBN) REFERENCES book(ISBN)
);

-- Finally create checkout table which depends on book, user, and librarian
CREATE TABLE checkout (
    checkoutID INT PRIMARY KEY AUTO_INCREMENT,
    checkoutDate DATETIME NOT NULL,
    ReturnDate DATE NOT NULL,
    ISBN VARCHAR(13) NOT NULL,
    u_username VARCHAR(100) NOT NULL,    -- Changed from username to u_username
    librarianID INT NOT NULL,
    FOREIGN KEY (ISBN) REFERENCES book(ISBN),
    FOREIGN KEY (u_username) REFERENCES user(u_username),    -- Changed from username to u_username
    FOREIGN KEY (librarianID) REFERENCES librarian(librarianID)
);

INSERT INTO shelf (shelfName) VALUES 
  ('Fiction'),
  ('History'),
  ('Science');

INSERT INTO librarian (librarianName, l_username, l_password, l_email, l_phone, l_address) VALUES 
  ('John Doe', 'xjohn', '123', 'john@gmail.com', '12345', '123 Main St');

INSERT INTO user (u_username, u_password, u_fullname, u_email, u_phone, u_address) VALUES
  ('test', '123', 'testSubject', 'test@gmail.com', '12345', '123 Main St');