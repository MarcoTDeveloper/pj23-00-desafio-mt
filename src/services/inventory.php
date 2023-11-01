<?php

class Inventory extends API_configuration
{

    public function read_inventory()
    {
        $sql = 'SELECT P.`id` AS `id`, P.`name` AS `name`, (COALESCE(SUM(O.`amount`), 0) - COALESCE(SUM(SP.`amount`), 0)) AS `amount`, P.`price` AS `unitary_value`, 
    (P.`price` * (COALESCE(SUM(O.`amount`), 0) - COALESCE(SUM(SP.`amount`), 0))) AS `total_value` FROM `products` P LEFT JOIN `sales_products` SP ON P.`id` = SP.`product_id`
    LEFT JOIN `orders` O ON P.`id` = O.`product_id` GROUP BY P.`id`, P.`name`, P.`price`';
        $get_inventory = $this->db_read($sql);

        if ($this->db_num_rows($get_inventory) > 0) {
            $inventory = [];
            while ($inventory_object = $this->db_object($get_inventory)) {
                $inventory[] = [
                    'id' => (int) $inventory_object->id,
                    'name' => $inventory_object->name,
                    'amount' => max(0, (int)$inventory_object->amount),
                    'unitary_value' => (float) $inventory_object->unitary_value,
                    'total_value' =>  max(0, (float)$inventory_object->total_value)
                ];
            }
            return $inventory;
        } else {
            return false;
        }
    }

    public function read_stock_by_product_id(
        int $product_id
    ) {
        $sql = 'SELECT P.`name` AS `name`, (COALESCE(SUM(O.`amount`), 0) - COALESCE(SUM(SP.`amount`), 0)) AS `amount` FROM `products` P LEFT JOIN `sales_products` SP ON P.`id` = SP.`product_id` LEFT JOIN `orders` O ON P.`id` = O.`product_id` WHERE P.`id` = ' . $product_id . ' GROUP BY p.id, p.name, p.price';
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
