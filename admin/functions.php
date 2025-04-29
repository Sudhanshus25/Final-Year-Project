<?php
function getCategories($conn) {
    $categories = [];
    $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

function getSubcategories($conn, $category_id = null) {
    $subcategories = [];
    $sql = "SELECT s.*, c.name as category_name FROM subcategories s 
            JOIN categories c ON s.category_id = c.id";
    
    if ($category_id) {
        $sql .= " WHERE s.category_id = " . (int)$category_id;
    }
    
    $sql .= " ORDER BY s.name ASC";
    
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subcategories[] = $row;
        }
    }
    return $subcategories;
}

function getCollections($conn) {
    $collections = [];
    $result = $conn->query("SELECT * FROM collections ORDER BY name ASC");
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $collections[] = $row;
        }
    }
    return $collections;
}

function getProducts($conn, $limit = null) {
    $products = [];
    $sql = "SELECT p.*, c.name as category_name, s.name as subcategory_name 
            FROM products p
            JOIN categories c ON p.category_id = c.id
            JOIN subcategories s ON p.subcategory_id = s.id
            ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

function getProductImages($conn, $product_id) {
    $images = [];
    $result = $conn->query("SELECT * FROM product_images WHERE product_id = " . (int)$product_id);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
    return $images;
}
?>