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
        $sql = 'SELECT `id`, `user_id`, `client_name`, `date`, `payment_methods`, `slug`, `status` FROM `sales`';
        $get_sales = $this->db_read($sql);
        if ($this->db_num_rows($get_sales) > 0) {
            if ($get_sales == 'false') {
                return false;
            } else {
                $sales = [];
                while ($sale = $this->db_object($get_sales)) {

                    $sales_products = [];
                    $sql = 'SELECT `product_id`, `amount` FROM `sales_products` WHERE `sale_id` = ' . $sale->id . '';
                    $get_sales_products = $this->db_read($sql);
                    while ($sale_product = $this->db_object($get_sales_products)) {
                        $product = $this->product->read_by_id($sale_product->product_id);
                        $product->amount = (float) $sale_product->amount;
                        $product->total_value = (float) number_format($sale_product->amount * $product->price, 2, '.', '');

                        unset($product->slug);
                        array_push($sales_products, $product);
                    }

                    $sales[] = [
                        'id' => (int) $sale->id,
                        'user_id' => (int) $sale->user_id,
                        'client_name' => $sale->client_name,
                        'date' => $sale->date,
                        'payment_methods' => $sale->payment_methods,
                        'slug' => $sale->slug,
                        'status' => $sale->status,
                        'products' => $sales_products
                    ];
                }
                return $sales;
            }
        } else {
            return ['message' => 'No sales found'];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `user_id`, `client_name`, `date`, `payment_methods`, `slug`, `status` FROM `sales` WHERE `slug` = "' . $slug . '"';
        $get_sale = $this->db_read($sql);
        if ($this->db_num_rows($get_sale) > 0) {
            $sales = [];
            while ($sale = $this->db_object($get_sale)) {
                $sales_products = [];
                $sql = 'SELECT `product_id`, `amount` FROM `sales_products` WHERE `sale_id` = ' . $sale->id . '';
                $get_sales_products = $this->db_read($sql);
                while ($sale_product = $this->db_object($get_sales_products)) {
                    $product = $this->product->read_by_id($sale_product->product_id);
                    $product->amount = (float) $sale_product->amount;
                    $product->total_value = (float) number_format($sale_product->amount * $product->price, 2, '.', '');

                    unset($product->slug);
                    array_push($sales_products, $product);
                }

                $sales[] = [
                    'id' => (int) $sale->id,
                    'user_id' => (int) $sale->user_id,
                    'client_name' => $sale->client_name,
                    'date' => $sale->date,
                    'payment_methods' => $sale->payment_methods,
                    'slug' => $sale->slug,
                    'status' => $sale->status,
                    'products' => $sales_products
                ];
            }
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

    public function cancel_sale(
        string $slug
    ) {
        $old_sale = $this->read_by_slug($slug);
        if ($old_sale) {
            $sql = 'UPDATE `sales` SET `status` = "false" WHERE `slug` = "' . $slug . '"';
            if ($this->db_update($sql)) {
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
