# API - pj23-00-desafio-mt

---

- **Para rodar a API em sua máquina siga os seguintes passos:**
    - Clone o diretório (`git clone https://github.com/MarcosDevPF/pj23-00-desafio-mt.git`) em seu `htdocs` ou em uma pasta que você consiga rodar;

- **Informações sobre a API:**
    
    > Esta API foi criada com intuito de ser um treinamento de backend proposto pela [Sharp](https://www.instagram.com/sharpsolucoes/) para um programador iniciante. O projeto consiste em sistema de estoque com autenticação usuários que exibe os resultados em JSON.
    > 

- **Como utilizar a API em sua maquina:**

    Se preferir pode se orientar pelo [Notion](https://www.notion.so/pt-br) clicando aqui -> [Orientação Notion](https://www.notion.so/API-pj23-00-desafio-mt-1b65de5171eb45ee83e87a1695fef60b?pvs=4).

    - **Pré-requisitos:**
        - **Ter o xampp.**
        - **Ter o insomnia.**
    
    - **Utilização da API:**
        - Login usuários
            - Login / Validação de usuarios:
                - Rota: /me/login
                - Metodo: POST
                - Headers**:** SECRET_KEY
                
                ```json
                {
                	"email" : "varchar(255)",
                	"password" : "varchar(255)"
                }
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status: 200 OK
                
                {
                "user": {
                		"id": int,
                		"name": "varchar(255)",
                		"position": "varchar(255)",
                		"avatar": "varchar(255)",
                		"permissions": []
                	},
                "token" : "varchar(255)"
                }
                
                //Erro - Status 401 Unauthorized
                
                {
                	[]
                }
                ```
                
                - Logout
                    - Rota: /me/logout
                    - Metodo: GET
                    - Headers**:** token de acesso da API (Será usado para fazer o logout)
                    - Respostas da requisição:
                        
                        ```json
                        // Sucesso - Status 200 OK
                        
                        {
                        	"message": "Logout successfully"
                        }
                        
                        // Erro - Status 401 Unauthorized
                        
                        {
                        	[]
                        }
                        ```
                        
        
        ---
        
        - CRUD de usuarios
            - Adicionar novo usuário
                - Rota: /users/create
                - Metodo: POST
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"name" : "varchar(255)",
                	"email" : "varchar(255)",
                	"password" : "varchar(255)",
                	"position" : "varchar(255)",
                	"permissions" : {
                		"users": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"products": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"sales" : {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"orders": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"inventory": {
                			"read": "boolean"
                		}
                }
                }
                
                //Não é possivel criar um usuário com o mesmo email que outro usuário
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 201 Created
                
                [
                	"User created"
                ]
                
                //Erro - Status 400 Bad Request
                
                []
                ```
                
            - Ver usuários
                - Rota: /users (para ver TODOS os usuários)
                - Rota /users/0-slug-usuario (para ver UM usuário em especifico)
                - Metodo: GET
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status - 200 OK
                
                [
                	{
                		"id" : "int(11)",
                		"name" : "varchar(255)",
                		"email" : "varchar(255)",
                		"position" : "varchar(255)",
                		"slug" : "varchar(255)"
                	}
                ]
                
                //Erro - Status - 400 Bad Request
                
                []
                ```
                
            - Atualizar usuário
                - Rota: /users/update
                - Metodo: PUT
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"id" : "int(11)",
                	"name" : "varchar(255)",
                	"email" : "varchar(255)",
                	"position" : "varchar(255)",
                	"permissions" : {
                		"users": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"products": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"sales" : {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		},
                		"orders": {
                			"read": "boolean",
                			"create": "boolean",
                			"update": "boolean",
                			"delete": "boolean"
                		}
                }
                }
                
                //Não é possivel atualizar para o mesmo email que outro usuário
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 200 OK
                
                [
                "User updated"
                ]
                
                //Erro - Status 400 Bad Request
                
                [
                "This id does not exist or invalid URL"
                ] 
                ```
                
            - Deletar usuário
                - Rota: users/delete/0-slug-usuario (voçê escolhe o usuario que será apagado pela slug)
                - Metodo: DELETE
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso Status - 204 No Content
                
                []
                
                //Erro Status - 400 Bad Request
                
                []
                ```
                
        
        ---
        
        - CRUD de produtos
            - Adicionar novo produto
                - Rota: /products/create
                - Metodo: POST
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"name" : "varchar(255)",
                	"price" : "varchar(255)",
                }
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 201 Created
                
                [
                	"Product created"
                ]
                
                //Erro - Status 400 Bad Request
                
                []
                ```
                
            - Ver produtos
                - Rota: /products (para ver TODOS os produtos)
                - Rota /products/0-slug-produto (para ver UM produto em especifico)
                - Metodo: GET
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status - 200 OK
                
                [
                	{
                		"id" : "int(11)",
                		"name" : "varchar(255)",
                		"price" : "varchar(255)",
                		"slug" : "varchar(255)"
                	}
                ]
                
                //Erro - Status - 400 Bad Request
                
                []
                ```
                
            - Atualizar produtos
                - Rota: /products/update
                - Metodo: PUT
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"id" : "int(11)",
                	"name" : "varchar(255)",
                	"price" : "varchar(255)"
                }
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 200 OK
                
                [
                "User updated"
                ]
                
                //Erro - Status 400 Bad Request
                
                [
                "This id does not exist or invalid URL"
                ] 
                ```
                
            - Deletar produtos
                - Rota: products/delete/0-slug-produto(voçê escolhe o produto que será apagado pela slug)
                - Metodo: DELETE
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso Status - 204 No Content
                
                []
                
                //Erro Status - 400 Bad Request
                
                []
                ```
                
        
        ---
        
        - CRUD de pedidos
            - Adicionar novo pedido
                - Rota: /orders/create
                - Metodo: POST
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"product_id" : "int(11)",
                	"amount" : "int(11)"
                }
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 201 Created
                
                [
                	"Order created"
                ]
                
                //Erro - Status 400 Bad Request
                
                []
                ```
                
            - Ver pedidos
                - Rota: /orders (para ver TODOS os pedidos)
                - Rota /orders/0-slug-usuario (para ver UM pedido em especifico)
                - Metodo: GET
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status - 200 OK
                
                [
                	{
                		"id": "int(11)",
                		"product_id": "int(11)",
                		"date": "varchar(255)",
                		"amount": "int(11)",
                		"slug": "varchar(255)",
                		"user": {
                				"id": "int(11)",
                				"name": "varchar(255)",
                				"email": "varchar(255)",
                				"position": "varchar(255)",
                				"slug": "varchar(255)"
                	}
                ]
                
                //Erro - Status - 400 Bad Request
                
                []
                ```
                
            - Atualizar pedidos
                - Rota: /orders/update
                - Metodo: PUT
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"id" : "int(11)",
                	"product_id" : "int(11)",
                	"amount" : "int(11)"
                }
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 200 OK
                
                [
                "Order updated"
                ]
                
                //Erro - Status 400 Bad Request
                
                [
                "This id does not exist or invalid URL"
                ] 
                ```
                
            - Deletar pedidos
                - Rota: orders/delete/0-slug-pedido (voçê escolhe o pedido que será apagado pela slug)
                - Metodo: DELETE
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso Status - 204 No Content
                
                []
                
                //Erro Status - 400 Bad Request
                
                []
                ```
                
        
        ---
        
        - CRUD de vendas
            - Adicionar nova venda
                - Rota: /sales/create
                - Metodo: POST
                - Headers**:** token de acesso da API
                
                ```json
                {
                	"client_name" : "varchar(255)",
                	"payment_methods" : "varchar(255)",
                	"products" : [
                		{
                			"product_id": "int(11)",
                			"amount" : "int(11)"
                		}
                	]
                }
                
                // Não é possivel crir uma venda se não tiver estoque!
                ```
                
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status 201 Created
                
                [
                	"Sale created"
                ]
                
                //Erro - Status 400 Bad Request
                
                []
                ```
                
            - Ver vendas
                - Rota: /sales (para ver TODAS as vendas)
                - Rota /sales/0-slug-venda (para ver UMA venda em especifico)
                - Metodo: GET
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso - Status - 200 OK
                
                [
                	{
                		"id": "int(11)",
                		"user_id": "int(11)",
                		"client_name" : "varchar(255)"
                		"date": "varchar(255)",
                		"payment_methods": "varchar(255)",
                		"slug": "varchar(255)",
                		"products": [
                			{
                				"id": "int(11)",
                				"name": "varchar(255)",
                				"price": "float",
                				"slug": "varchar(255)"
                			}
                	}
                ]
                
                //Erro - Status - 400 Bad Request
                
                []
                ```
                
            - Cancelar vendas
                - Rota: sales/cancel_sale/0-slug-venda (voçê escolhe a venda que será apagada pela slug)
                - Metodo: DELETE
                - Headers**:** token de acesso da API
                - Respostas da requisição:
                
                ```json
                //Sucesso Status - 204 No Content
                
                ["message": "Canceled Sale"]
                
                //Erro Status - 400 Bad Request
                
                []
                ```
                
        
        ---
        
        - Visualização do estoque
            - Rota: inventory
            - Metodo: GET
            - Headers**:** token de acesso da API
            - Respostas da requisição:
            
            ```json
            [
            	{
            		"ID": "int(11)",
            		"Name": "varchar(255)",
            		"Amount": "int(11)",
            		"Unitary_value": "float",
            		"Total_value": "float"
            	}
            ]
            ```
            
    

---