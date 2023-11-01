<?php
if (isset($_GET['url'])) {
    $api = new API_configuration();
    $api->token = isset($headers['Authorization']) ? $headers['Authorization'] : (isset($headers['authorization']) ? $headers['authorization'] : "");
    $user = $api->authorization();

    if ($url[0] == 'me') {
        require_once 'src/services/me.php';
        $authorization = $api->authorization("api");
        $me = new Me();

        if ($url[1] == 'login') {
            if (!$authorization) {
                http_response_code(401);
                exit;
            }
            $response = $me->login(
                addslashes($request->email),
                addslashes($request->password)
            );
            if ($response) {
                $api->generate_user_log(
                    $response['user']['id'],
                    'login'
                );
                http_response_code(200);
                echo json_encode($response);
            } else {
                http_response_code(401);
            }
        } else if ($url[1] == 'logout') {
            $response = $me->logout(
                addslashes($headers['Authorization'])
            );
            if ($response) {
                $api->generate_user_log(
                    $api->user_id,
                    'logout'
                );
                http_response_code(200);
                echo json_encode(['message' => 'Logout successfully']);
            } else {
                http_response_code(401);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid url']);
        }
    } else if ($user) {
        if ($url[0] == 'users') {
            require_once 'src/services/users.php';
            $users = new Users();

            if (!isset($url[1])) { //read
                $users->user_id = $user;
                $response = $users->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                $response = $users->create(
                    addslashes($request->name),
                    addslashes($request->email),
                    addslashes($request->password),
                    addslashes($request->position),
                    (array)$request->permissions

                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'User created'
                    ]);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'update') {
                $response = $users->update(
                    addslashes($request->id),
                    addslashes($request->name),
                    addslashes($request->email),
                    addslashes($request->position),
                    (array)$request->permissions
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'User updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                $response = $users->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $users->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'users.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or user not found']);
                }
            }
        } else if ($url[0] == 'products') {
            require_once 'src/services/products.php';
            $products = new Products();

            if (!isset($url[1])) { //read
                $products->user_id = $user;
                $response = $products->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'product.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                $response = $products->create(
                    addslashes($request->name),
                    addslashes($request->price)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'products.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'Product created'
                    ]);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'update') {
                $response = $products->update(
                    addslashes($request->id),
                    addslashes($request->name),
                    addslashes($request->price)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'products.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Product updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                $response = $products->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'products.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $products->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'products.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or product not found']);
                }
            }
        } else if ($url[0] == 'orders') {
            require_once 'src/services/orders.php';
            $orders = new Orders();

            if (!isset($url[1])) { //read
                $orders->user_id = $user;
                $response = $orders->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                $response = $orders->create(
                    $user,
                    addslashes($request->product_id),
                    addslashes($request->amount)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'Order created'
                    ]);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'update') {
                $response = $orders->update(
                    addslashes($request->id),
                    addslashes($request->product_id),
                    addslashes($request->amount)
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Order updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                $response = $orders->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $orders->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or order not found']);
                }
            }
        } else if ($url[0] == 'sales') {
            require_once 'src/services/sales.php';
            $sales = new Sales();

            if (!isset($url[1])) { //read
                $sales->user_id = $user;
                $response = $sales->read(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'orders.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            } else if ($url[1] == 'create') {
                $response = $sales->create(
                    $user,
                    addslashes($request->client_name),
                    (array)$request->products,
                    addslashes($request->payment_methods)
                );
                if ($response && array_keys($response)[0] != 'message') {
                    $api->generate_user_log(
                        $api->user_id,
                        'sales.create',
                        json_encode($response)
                    );
                    http_response_code(201);
                    echo json_encode([
                        'message' => 'Sale created'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode($response);
                }
            } else if ($url[1] == 'update') {
                $response = $sales->update(
                    addslashes($request->id),
                    addslashes($request->client_name),
                    addslashes($request->payment_methods),
                    (array)($request->products)
                );
                if ($response && array_keys($response)[0] != 'message') {
                    $api->generate_user_log(
                        $api->user_id,
                        'sales.update',
                        json_encode($response)
                    );
                    http_response_code(200);
                    echo json_encode([
                        'message' => 'Sale updated'
                    ]);
                } else {
                    http_response_code(400);
                    echo json_encode(['This id does not exist or invalid URL']);
                }
            } else if ($url[1] == 'delete') {
                $response = $sales->delete(
                    addslashes($url[2])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'sales.delete',
                        json_encode($response)
                    );
                    http_response_code(204);
                } else {
                    http_response_code(400);
                }
            } else {
                $response = $sales->read_by_slug(
                    addslashes($url[1])
                );
                if ($response) {
                    $api->generate_user_log(
                        $api->user_id,
                        'sales.read_by_slug'

                    );
                    http_response_code(200);
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Invalid URL or sale not found']);
                }
            }
        } else if ($url[0] == 'inventory') {
            require_once 'src/services/inventory.php';
            $inventory = new Inventory();

            if (!isset($url[1])) { //read
                $inventory->user_id = $user;
                $response = $inventory->read_inventory(
                    (isset($_GET['position']) ? ['position' => addslashes($_GET['position'])] : [])
                );
                if ($response || $response == []) {
                    $api->generate_user_log(
                        $api->user_id,
                        'inventory.read'

                    );
                    echo json_encode($response);
                } else {
                    http_response_code(400);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Invalid url']);
        }
    } else {
        http_response_code(401);
    }
} else {
    echo json_encode([
        'message' => 'server running',
        'version' => VERSION,
    ]);
}
