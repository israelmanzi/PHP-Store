/* 
    This file contains the SQL code to create the database and tables.
    Copy and paste the code into your database management tool to create the database.
*/

CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE TABLE categories (
    id uuid DEFAULT uuid_generate_v4 () PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
	description TEXT,
    created_date TIMESTAMP DEFAULT NOW()
);

CREATE TABLE products (
    id uuid DEFAULT uuid_generate_v4 () PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    tax DECIMAL(10, 2) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    category_id uuid REFERENCES categories(id),
	created_at TIMESTAMP DEFAULT NOW()
);

CREATE TABLE purchases (
    id uuid DEFAULT uuid_generate_v4 () PRIMARY KEY,
    product_id uuid REFERENCES products(id),
    purchase_date TIMESTAMP DEFAULT NOW(),
    quantity INT NOT NULL
);
