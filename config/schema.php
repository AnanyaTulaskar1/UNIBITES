<?php

function ensure_orders_schema(mysqli $conn): void {
    $createOrdersSql = "
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        shop_key VARCHAR(64) NOT NULL,
        shop_label VARCHAR(120) NOT NULL,
        token_no INT NOT NULL,
        token_code VARCHAR(40) NOT NULL,
        items_json LONGTEXT NOT NULL,
        item_count INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'PLACED',
        payment_method VARCHAR(20) NOT NULL DEFAULT 'UPI',
        payment_status VARCHAR(20) NOT NULL DEFAULT 'PAID',
        receipt_no VARCHAR(40) NOT NULL DEFAULT '',
        payment_ref VARCHAR(60) NOT NULL DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_shop_token (shop_key, token_no),
        INDEX idx_user (user_id)
    )";
    mysqli_query($conn, $createOrdersSql);

    $columns = [];
    $res = mysqli_query($conn, "SHOW COLUMNS FROM orders");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $columns[$row['Field']] = true;
        }
        mysqli_free_result($res);
    }

    $alter = [];
    if (!isset($columns['payment_method'])) {
        $alter[] = "ADD COLUMN payment_method VARCHAR(20) NOT NULL DEFAULT 'UPI'";
    }
    if (!isset($columns['payment_status'])) {
        $alter[] = "ADD COLUMN payment_status VARCHAR(20) NOT NULL DEFAULT 'PAID'";
    }
    if (!isset($columns['receipt_no'])) {
        $alter[] = "ADD COLUMN receipt_no VARCHAR(40) NOT NULL DEFAULT ''";
    }
    if (!isset($columns['payment_ref'])) {
        $alter[] = "ADD COLUMN payment_ref VARCHAR(60) NOT NULL DEFAULT ''";
    }

    if (!empty($alter)) {
        mysqli_query($conn, "ALTER TABLE orders " . implode(", ", $alter));
    }
}

function ensure_menu_items_schema(mysqli $conn): void {
    $createMenuSql = "
    CREATE TABLE IF NOT EXISTS menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_key VARCHAR(64) NOT NULL,
        name VARCHAR(120) NOT NULL,
        price DECIMAL(10,2) NOT NULL DEFAULT 0,
        image VARCHAR(255) NOT NULL,
        is_available TINYINT(1) NOT NULL DEFAULT 1,
        available_from TIME NULL,
        available_to TIME NULL,
        quality_note VARCHAR(100) NULL,
        auto_ready TINYINT(1) NOT NULL DEFAULT 1,
        is_seeded TINYINT(1) NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_shop (shop_key)
    )";
    mysqli_query($conn, $createMenuSql);

    $columns = [];
    $res = mysqli_query($conn, "SHOW COLUMNS FROM menu_items");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $columns[$row['Field']] = true;
        }
        mysqli_free_result($res);
    }

    if (!isset($columns['is_seeded'])) {
        mysqli_query($conn, "ALTER TABLE menu_items ADD COLUMN is_seeded TINYINT(1) NOT NULL DEFAULT 0");
    }
}
