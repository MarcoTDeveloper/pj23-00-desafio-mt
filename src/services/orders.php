<?php
require_once "products.php";
require_once "users.php";
class Orders extends API_configuration
{
    private $product;
    private $users;
    public function __construct()
    {
        parent::__construct();
        $this->product = new Products();
        $this->users = new Users();
    }

    public function create(
        int $user_id,
        int $product_id,
        int $amount
    ) {

        $values = '
        ' . $user_id . ',
        ' . $product_id . ',
        "' . date('Y-m-d H:i:s') . '",
        ' . $amount . '
        ';

        $sql = 'INSERT INTO `orders` (`user_id`, `product_id`, `date`, `amount`) VALUES (' . $values . ')';
        $create_order = $this->db_create($sql);
        if ($create_order) {
            $user = $this->users->read_by_id($user_id);
            $slug = $this->slugify($create_order . '-' . $user->name);
            $sql = 'UPDATE `orders` SET `slug` = "' . $slug . '" WHERE `id` = ' . $create_order;
            $this->db_update($sql);
            return [
                'id' => (int) $create_order,
                'user_id' => (int) $user_id,
                'product_id' => (int) $product_id,
                'amount' => (int) $amount,
                'slug' => $slug
            ];
        } else {
            return false;
        }
    }

    public function read()
    {
        $sql = 'SELECT `id`, `user_id`, `product_id`, `date`, `amount`, `slug` FROM `orders`';
        $get_orders = $this->db_read($sql);
        if ($this->db_num_rows($get_orders) > 0) {
            $orders = [];
            while ($order = $this->db_object($get_orders)) {
                $orders[] = [
                    'id' => (int) $order->id,
                    'user_id' => (int) $order->user_id,
                    'product_id' => (int) $order->product_id,
                    'date' => $order->date,
                    'amount' => (int) $order->amount,
                    'slug' => $order->slug,
                    'product' => $this->product->read_by_id((int) $order->product_id)
                ];
            }
            return $orders;
        } else {
            return [];
        }
    }

    public function read_by_slug(
        string $slug
    ) {
        $sql = 'SELECT `id`, `user_id`, `product_id`, `date`, `amount`, `slug` FROM `orders` WHERE `slug` = "' . $slug . '"';
        $get_orders = $this->db_read($sql);
        if ($this->db_num_rows($get_orders) > 0) {
            $orders = $this->db_object($get_orders);
            $orders->id = (int) $orders->id;
            return $orders;
        } else {
            return [];
        }
    }

    public function read_by_id(
        int $id
    ) {
        $sql = 'SELECT `id`, `user_id`, `product_id`, `date`, `amount`, `slug` FROM `orders` WHERE `id` = "' . $id . '"';
        $get_orders = $this->db_read($sql);
        if ($this->db_num_rows($get_orders) > 0) {
            $orders = $this->db_object($get_orders);
            $orders->id = (int) $orders->id;
            $orders->amount = (int) $orders->amount;
            return $orders;
        } else {
            return [];
        }
    }

    public function update(
        int $id,
        int $product_id,
        int $amount
    ) {
        $old_order = $this->read_by_id($id);
        if ($old_order) {
            $order = $this->read_by_id($id);
            $user = $this->users->read_by_id($order->user_id);
            $sql = 'UPDATE `orders` SET `product_id` = "' . $product_id . '" , `amount` = "' . $amount . '" , `slug` = "' . $this->slugify($id . '-' . $user->name) . '"  WHERE `id` = "' . $id .  '"';
            if ($this->db_update($sql)) {
                return [
                    'old_order' => $old_order,
                    'new_order' => [
                        'id' => (int) $id,
                        'product_id' => (int) $product_id,
                        'amount' => (int) $amount,
                        'slug' => $this->slugify($id . '-' . $user->name)
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
        $old_order = $this->read_by_slug($slug);
        if ($old_order) {
            $sql = 'DELETE FROM `orders` WHERE `slug` = "' . $slug . '"';
            if ($this->db_delete($sql)) {
                return [
                    'old_order' => $old_order
                ];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
