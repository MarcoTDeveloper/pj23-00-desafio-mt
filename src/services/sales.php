<?php
require_once "products.php";
require_once "inventory.php";
class Sales extends API_configuration
{
    private $product;
    private $inventory;
    public function __construct()
    {
        parent::__construct();
        $this->product = new Products();
        $this->inventory = new Inventory();
    }

    public function create(
        int $user_id,
        string $client_name,
        array $products,
        string $payment_methods
    ) {
        foreach ($products as $product) {
            $amount = (int) $product->amount;

            $inventory = $this->inventory->read_stock_by_product_id($product->product_id);
            if ($inventory->amount <= 0) {
                return [
                    'message' => 'Product ' . $inventory->name . ' is out of stock with id ' . $product->product_id
                ];
            } else if ($amount > $inventory->amount) {
                return [
                    'message' => 'Product ' . $inventory->name . ' has only ' . $inventory->amount . ' in stock'
                ];
            }
        }

        $values = '
        ' . $user_id . ',
        "' . $client_name . '",
        "' . date('Y-m-d H:i:s') . '",
        "' . $payment_methods . '"
        ';

        $sql = 'INSERT INTO `sales` (`user_id`, `client_name`, `date`, `payment_methods`) VALUES (' . $values . ')';
        $create_sale = $this->db_create($sql);
        if ($create_sale) {
            foreach ($products as $product) {
                $this->create_sales_products($create_sale, $product->product_id, $product->amount);
            }
            $slug = $this->slugify($create_sale . '-' . $client_name);
            $sql = 'UPDATE `sales` SET `slug` = "' . $slug . '" WHERE `id` = ' . $create_sale;
            $this->db_update($sql);
            return [
                'id' => (int) $create_sale,
                'user_id' => (int) $user_id,
                'client_name' => $client_name,
                'payment_methods' => $payment_methods,
                'slug' => $slug,
                'products' => $products
            ];
        } else {
            return false;
        }
    }

    protected function create_sales_products(
        int $sale_id,
        int $product_id,
        int $amount
    ) {
        $values = '
        "' . $sale_id . '",
        "' . $product_id . '",
        "' . $amount . '"
        ';
        $sql = 'INSERT INTO `sales_products` (`sale_id`,`product_id`,`amount`) VALUES (' . $values . ')';
        if ($this->db_create($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function read()
    {
        $sql = 'SELECT `id`, `user_id`, `client_name`, `date`, `payment_methods`, `slug` FROM `sales`';
        $get_sales = $this->db_read($sql);
        if ($this->db_num_rows($get_sales) > 0) {

            $sales_product = [];
            $sql = 'SELECT `product_id` AS `product` FROM `sales_products` SP, `sales` S WHERE `sale_id` = S.`id`';
            $get_sales_products = $this->db_read($sql);
            if ($this->db_num_rows($get_sales_products) > 0) {

                while ($sales_products = $this->db_object($get_sales_products)) {
                    $sales_product[] = [
                        'product' => (int) $sales_products->product,
                        // 'amount' => (int) $sales_products->amount
                    ];
                }
            }

            $sales = [];
            while ($sale = $this->db_object($get_sales)) {
                $sales[] = [
                    'id' => (int) $sale->id,
                    'user_id' => (int) $sale->user_id,
                    'client_name' => $sale->client_name,
                    'date' => $sale->date,
                    'payment_methods' => $sale->payment_methods,
                    'slug' => $sale->slug,
                    'products' => $sales_products
                ];
            }
            return $sales;
        } else {
            return ['message' => 'No sales found'];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `user_id`, `client_name`, `date`, `payment_methods`, `slug` FROM `sales` WHERE `slug` = "' . $slug . '"';
        $get_sales = $this->db_read($sql);
        if ($this->db_num_rows($get_sales) > 0) {
            $sales = $this->db_object($get_sales);
            $sales->id = (int) $sales->id;
            return $sales;
        } else {
            return [];
        }
    }

    public function read_by_id(
        int $id
    ) {
        $sql = 'SELECT `id`, `user_id`, `client_name`, `date`, `payment_methods`, `slug` FROM `sales` WHERE `id` = "' . $id . '"';
        $get_sales = $this->db_read($sql);
        if ($this->db_num_rows($get_sales) > 0) {
            $sales = $this->db_object($get_sales);
            $sales->id = (int) $sales->id;
            return $sales;
        } else {
            return [];
        }
    }

    public function update(
        int $id,
        string $client_name,
        string $payment_methods,
        array $products
    ) {

        foreach ($products as $product) {
            $inventory = $this->inventory->read_stock_by_product_id($product->product_id);
            if ($inventory->amount <= 0) {
                return [
                    'message' => 'Product ' . $inventory->name . ' is out of stock with id ' . $product->product_id
                ];
            }
        }

        $old_sale = $this->read_by_id($id);
        if ($old_sale) {
            $sql = 'UPDATE `sales` SET `client_name` = "' . $client_name . '" , `payment_methods` = "' . $payment_methods . '" , `slug` = "' . $this->slugify($id . '-' . $client_name) . '"  WHERE `id` = "' . $id .  '"';
            if ($this->db_update($sql)) {

                $delete_sale = 'DELETE FROM `sales_products` WHERE `sale_id` = "' . $id . '"';
                $this->db_delete($delete_sale);

                foreach ($products as $product) {
                    $this->create_sales_products($id, $product->product_id, $product->amount);
                }
                return [
                    'old_sale' => $old_sale,
                    'new_sale' => [
                        'id' => (int) $id,
                        'client_name' => $client_name,
                        'payment_methods' => $payment_methods,
                        'slug' => $this->slugify($id . '-' . $client_name),
                        'products' => $products
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
        $old_sale = $this->read_by_slug($slug);
        if ($old_sale) {
            $sql = 'DELETE FROM `sales` WHERE `slug` = "' . $slug . '"';
            if ($this->db_delete($sql)) {
                return [
                    'old_sale' => $old_sale
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
