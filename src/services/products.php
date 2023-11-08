<?php

class Products extends API_configuration
{
    public function create(
        string $name,
        string $price
    ) {

        $values = '
        "' . $name . '",
        ' . $this->real_to_float($price) . '
        ';

        $sql = 'INSERT INTO `products` (`name`, `price`) VALUES (' . $values . ')';
        $create_product = $this->db_create($sql);
        if ($create_product) {
            $slug = $this->slugify($create_product . '-' . $name);
            $sql = 'UPDATE `products` SET `slug` = "' . $slug . '" WHERE `id` = ' . $create_product;
            $this->db_update($sql);
            return [
                'id' => (int) $create_product,
                'name' => $name,
                'price' => (float) $price,
                'slug' => $slug
            ];
        } else {
            http_response_code(400);
            return ['message' => "Error creating product"];
        }
    }

    public function read()
    {
        $sql = 'SELECT `id`, `name`, `price`, `slug` FROM `products`';
        $get_products = $this->db_read($sql);
        if ($this->db_num_rows($get_products) > 0) {
            $products = [];
            while ($product = $this->db_object($get_products)) {
                $products[] = [
                    'id' => (int) $product->id,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'slug' => $product->slug
                ];
            }
            return $products;
        } else {
            return [];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `name`, `price`, `slug` FROM `products` WHERE `slug` = "' . $slug . '"';
        $get_products = $this->db_read($sql);
        if ($this->db_num_rows($get_products) > 0) {
            $products = $this->db_object($get_products);
            $products->id = (int) $products->id;
            return $products;
        } else {
            return [];
        }
    }

    public function read_by_id(
        int $id
    ) {
        $sql = 'SELECT `id`, `name`, `price`, `slug` FROM `products` WHERE `id` = "' . $id . '"';
        $get_products = $this->db_read($sql);
        if ($this->db_num_rows($get_products) > 0) {
            $products = $this->db_object($get_products);
            $products->id = (int) $products->id;
            $products->price = (float) $products->price;
            return $products;
        } else {
            return [];
        }
    }

    public function update(
        int $id,
        string $name,
        string $price
    ) {
        $old_product = $this->read_by_id($id);
        if ($old_product) {
            $sql = 'UPDATE `products` SET `name` = "' . $name . '" , `price` = "' . $this->real_to_float($price) . '" , `slug` = "' . $this->slugify($id . '-' . $name) . '"  WHERE `id` = "' . $id .  '"';
            if ($this->db_update($sql)) {
                return [
                    'old_product' => $old_product,
                    'new_user' => [
                        'id' => (int) $id,
                        'name' => $name,
                        'price' => (float) $price,
                        'slug' => $this->slugify($id . '-' . $name)
                    ]
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delete(
        string $slug
    ) {
        $old_product = $this->read_by_slug($slug);
        if ($old_product) {
            $sql = 'DELETE FROM `products` WHERE `slug` = "' . $slug . '"';
            if ($this->db_delete($sql)) {
                return [
                    'old_product' => $old_product
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
