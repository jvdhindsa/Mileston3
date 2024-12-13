-- Create the Users table
CREATE TABLE Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'editor', 'viewer') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL
);

-- Create the Books table
CREATE TABLE Books (
    book_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    ISBN VARCHAR(20) UNIQUE,
    genre VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT NOT NULL,
    publication_date DATE NULL
);

-- Create the Sales table
CREATE TABLE Sales (
    sale_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    quantity INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Create the Customers table
CREATE TABLE Customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NULL,
    address TEXT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create the Suppliers table
CREATE TABLE Suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100) NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NULL,
    address TEXT NULL
);

-- Create the Inventory_Logs table
CREATE TABLE Inventory_Logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    change_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    change_type ENUM('added', 'sold', 'restocked') NOT NULL,
    quantity INT NOT NULL,
    user_id INT NOT NULL,
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);


















-- Insert data into Users table
INSERT INTO Users (username, email, password_hash, role)
VALUES 
('admin_user', 'admin@example.com', 'hashed_password_1', 'admin'),
('staff_user', 'staff@example.com', 'hashed_password_2', 'staff'),
('editor_user', 'editor@example.com', 'hashed_password_3', 'editor'),
('viewer_user', 'viewer@example.com', 'hashed_password_4', 'viewer'),
('manager_user', 'manager@example.com', 'hashed_password_5', 'admin'),
('assistant_user', 'assistant@example.com', 'hashed_password_6', 'staff');

-- Insert data into Books table
INSERT INTO Books (title, author, ISBN, genre, price, stock_quantity, publication_date)
VALUES 
('The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 'Fiction', 10.99, 100, '1925-04-10'),
('1984', 'George Orwell', '9780451524935', 'Dystopian', 8.99, 150, '1949-06-08'),
('To Kill a Mockingbird', 'Harper Lee', '9780061120084', 'Fiction', 12.99, 200, '1960-07-11'),
('The Catcher in the Rye', 'J.D. Salinger', '9780316769488', 'Classic', 10.49, 75, '1951-07-16'),
('The Hobbit', 'J.R.R. Tolkien', '9780345339683', 'Fantasy', 15.99, 50, '1937-09-21'),
('Pride and Prejudice', 'Jane Austen', '9780141040349', 'Romance', 9.49, 120, '1813-01-28');

-- Insert data into Sales table
INSERT INTO Sales (book_id, user_id, sale_date, quantity, total_amount)
VALUES 
(1, 1, '2024-12-01 10:00:00', 1, 10.99),
(2, 2, '2024-12-01 11:30:00', 2, 17.98),
(3, 3, '2024-12-01 12:45:00', 3, 38.97),
(4, 4, '2024-12-01 14:00:00', 1, 10.49),
(5, 5, '2024-12-01 15:15:00', 1, 15.99),
(6, 6, '2024-12-01 16:30:00', 2, 18.98);

-- Insert data into Customers table
INSERT INTO Customers (name, email, phone, address)
VALUES 
('John Doe', 'john.doe@example.com', '123-456-7890', '123 Main St, Cityville, NY'),
('Jane Smith', 'jane.smith@example.com', '234-567-8901', '456 Oak St, Townsville, CA'),
('Bob Johnson', 'bob.johnson@example.com', '345-678-9012', '789 Pine St, Villageburg, TX'),
('Alice Brown', 'alice.brown@example.com', '456-789-0123', '123 Elm St, Suburbia, FL'),
('Charlie Green', 'charlie.green@example.com', '567-890-1234', '987 Maple St, Hamlet, OH'),
('David White', 'david.white@example.com', '678-901-2345', '654 Birch St, Metropolis, IL');

-- Insert data into Suppliers table
INSERT INTO Suppliers (name, contact_person, email, phone, address)
VALUES 
('Book Supplies Inc.', 'Michael Lee', 'supplier1@example.com', '800-123-4567', '321 Supplier Rd, Booktown, NY'),
('Novelty Press', 'Sarah Adams', 'supplier2@example.com', '800-234-5678', '654 Novel St, Publishing City, CA'),
('Literary Hub', 'David Clark', 'supplier3@example.com', '800-345-6789', '987 Lit St, Authorville, TX'),
('Story Books Ltd.', 'Emma Taylor', 'supplier4@example.com', '800-456-7890', '123 Story Ln, Page City, FL'),
('Readers Corner', 'Oliver Harris', 'supplier5@example.com', '800-567-8901', '456 Reader Blvd, Text Town, OH'),
('Classic Reads', 'Sophia Martin', 'supplier6@example.com', '800-678-9012', '789 Classic St, Chapterville, IL');

-- Insert data into Inventory_Logs table
INSERT INTO Inventory_Logs (book_id, change_date, change_type, quantity, user_id)
VALUES 
(1, '2024-12-01 10:00:00', 'added', 100, 1),
(2, '2024-12-01 11:30:00', 'restocked', 150, 2),
(3, '2024-12-01 12:45:00', 'sold', 3, 3),
(4, '2024-12-01 14:00:00', 'added', 75, 4),
(5, '2024-12-01 15:15:00', 'sold', 1, 5),
(6, '2024-12-01 16:30:00', 'restocked', 120, 6);






















ALTER TABLE Books ADD COLUMN image_path VARCHAR(255) NULL;

ALTER TABLE Books ADD COLUMN description TEXT NULL;
CREATE TABLE Categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL
);
CREATE TABLE Book_Categories (
    book_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (book_id, category_id),
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES Categories(category_id) ON DELETE CASCADE
);
CREATE TABLE Comments (
    comment_id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL,
    user_id INT NOT NULL,
    comment_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    moderated ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    FOREIGN KEY (book_id) REFERENCES Books(book_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

ALTER TABLE Users ADD COLUMN last_captcha_time TIMESTAMP NULL;


ALTER TABLE Sales ADD COLUMN customer_id INT NULL;
ALTER TABLE Sales ADD FOREIGN KEY (customer_id) REFERENCES Customers(customer_id) ON DELETE SET NULL;

ALTER TABLE Books ADD FULLTEXT(title, author, genre, description);

CREATE TABLE Settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NOT NULL
);




CREATE TABLE Permissions (
    permission_id INT AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'staff', 'editor', 'viewer') NOT NULL,
    feature_name VARCHAR(100) NOT NULL,
    can_access BOOLEAN DEFAULT FALSE,
    UNIQUE (role, feature_name)
);





UPDATE Books SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg';




-- Insert data into Categories table
INSERT INTO Categories (name, description)
VALUES 
('Fiction', 'Books that contain content based on imaginative narration.'),
('Dystopian', 'Books set in a fictional world that is often unpleasant or controlled by a totalitarian government.'),
('Classic', 'Books that have stood the test of time and are widely recognized for their literary merit.'),
('Fantasy', 'Books that contain elements of magic, mythical creatures, or other supernatural phenomena.'),
('Romance', 'Books that focus on romantic relationships between characters.');


-- Insert data into Book_Categories table
INSERT INTO Book_Categories (book_id, category_id)
VALUES 
(1, 1), -- 'The Great Gatsby' -> Fiction
(2, 2), -- '1984' -> Dystopian
(3, 1), -- 'To Kill a Mockingbird' -> Fiction
(4, 3), -- 'The Catcher in the Rye' -> Classic
(5, 4), -- 'The Hobbit' -> Fantasy
(6, 5); -- 'Pride and Prejudice' -> Romance






-- Insert data into Comments table
INSERT INTO Comments (book_id, user_id, comment_text, moderated)
VALUES 
(1, 1, 'A timeless classic of the American Dream.', 'approved'),
(2, 2, 'A chilling depiction of a dystopian future.', 'approved'),
(3, 3, 'A powerful exploration of racial injustice.', 'approved'),
(4, 4, 'A story of adolescent rebellion and existential questioning.', 'approved'),
(5, 5, 'A thrilling adventure through Middle Earth.', 'approved'),
(6, 6, 'A beautifully crafted romance novel.', 'approved');




-- Insert data into Settings table
INSERT INTO Settings (setting_key, setting_value)
VALUES 
('site_name', 'Bookstore Online'),
('currency', 'USD'),
('tax_rate', '0.07'),
('max_books_per_order', '10');




-- Insert data into Permissions table
INSERT INTO Permissions (role, feature_name, can_access)
VALUES 
('admin', 'view_dashboard', TRUE),
('admin', 'manage_books', TRUE),
('admin', 'manage_users', TRUE),
('admin', 'manage_orders', TRUE),
('staff', 'view_dashboard', TRUE),
('staff', 'manage_orders', TRUE),
('editor', 'view_dashboard', TRUE),
('editor', 'edit_books', TRUE),
('viewer', 'view_books', TRUE);


-- Update image path for the books
UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 1;

UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 2;

UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 3;

UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 4;

UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 5;

UPDATE Books 
SET image_path = 'https://m.media-amazon.com/images/I/81R132zRxdL._AC_UF1000,1000_QL80_.jpg'
WHERE book_id = 6;




