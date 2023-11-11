<?php

class Inventory extends API_configuration
{

    public function read_inventory()
    {
        $sql = 'SELECT P.`id` AS `id`, P.`name` AS `name`, 
        (COALESCE(`order_amount`, 0) - COALESCE(`sales_product_amount`, 0)) AS `amount`, 
        P.`price` AS `unitary_value`, 
        P.`price` * (COALESCE(`order_amount`, 0) - COALESCE(`sales_product_amount`, 0)) AS `total_value` 
        FROM `products` P 
        LEFT JOIN (SELECT `product_id`, SUM(`amount`) AS `order_amount` FROM `orders` GROUP BY `product_id`) O 
        ON P.`id` = O.`product_id` 
        LEFT JOIN (SELECT `product_id`, SUM(`amount`) AS `sales_product_amount` FROM `sales_products` SP
        JOIN `sales` S ON SP.`sale_id` = S.`id`
        WHERE S.`status` = "true"
        GROUP BY `product_id`) SP ON P.`id` = SP.`product_id`';
        $get_inventory = $this->db_read($sql);

        if ($this->db_num_rows($get_inventory) > 0) {
            $inventory = [];
            while ($inventory_info = $this->db_object($get_inventory)) {
                $inventory[] = [
                    'id' => (int) $inventory_info->id,
                    'name' => $inventory_info->name,
                    'amount' => (int)$inventory_info->amount < 0 ? 0 : (int)$inventory_info->amount,
                    'unitary_value' => (float) $inventory_info->unitary_value,
                    'total_value' => (float) number_format((float)$inventory_info->total_value < 0 ? 0 : (float)$inventory_info->total_value,  2, '.', '')
                ];
            }
            return $inventory;
        } else {
            return [];
        }
    }

    public function read_stock_by_product_id(
        int $product_id
    ) {
        $sql = 'SELECT
        P.`name` AS `name`,
        (COALESCE(`order_amount`, 0) - COALESCE(`sales_product_amount`, 0)) AS `amount`
        FROM `products` P LEFT JOIN
        (SELECT `product_id`, SUM(`amount`) AS `order_amount` FROM `orders` GROUP BY `product_id`) O
        ON P.`id` = O.`product_id`
        LEFT JOIN
        (SELECT `product_id`, SUM(`amount`) AS `sales_product_amount` FROM `sales_products` SP, `sales` S
        WHERE SP.`sale_id` = S.`id` AND S.`status` = "true"
        GROUP BY `product_id`) SP
        ON P.`id` = SP.`product_id`
        WHERE P.`id` = '. $product_id .'';
        $get_inventory = $this->db_read($sql);
        if ($this->db_num_rows($get_inventory) > 0) {
            $inventory = $this->db_object($get_inventory);
            $inventory->amount = (int) $inventory->amount;
            return $inventory;
        } else {
            return [];
        }
    }
}
