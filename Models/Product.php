<?php

namespace Models;

use PDO;

class Product
{
    private string $name;
    private $price;
    private string $description;
    private string $category;
    private $tax;
    private $amount;

    public function __construct($name, $price, $description, $category, $tax, $amount)
    {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->category = $category;
        $this->tax = $tax;
        $this->amount = $amount;
    }

    public function save()
    {
        $db = \Util::getDb();

        $category_exists = Category::find($this->category);

        if (!$category_exists) {
            return null;
        }

        $stmt = $db->prepare("INSERT INTO products (name, price, description, category_id, tax, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$this->name, $this->price, $this->description, $this->category, $this->tax, $this->amount]);

        return $this;
    }

    public static function all()
    {
        $db = \Util::getDb();

        $stmt = $db->prepare("SELECT * FROM products");
        $stmt->execute();

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $products;
    }

    public static function find(string $id)
    {
        $db = \Util::getDb();

        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return null;
        }

        return $product;
    }

    public static function findByCategory(string $category_id)
    {
        $db = \Util::getDb();

        $stmt = $db->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$category_id]);

        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$products) {
            return null;
        }

        return $products;
    }

    public static function listCategories()
    {
        $db = \Util::getDb();

        $stmt = $db->prepare("SELECT * FROM categories");
        $stmt->execute();

        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$categories) {
            return null;
        }

        return $categories;
    }
}
