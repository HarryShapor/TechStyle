<?php
require_once '../auth_check.php';
requireAdminRole();

$host = 'localhost';
$db_name = 'techstyle';
$db_user = 'postgres';
$db_password = 'Fifa32rekrek';

try {
    $conn = new PDO("pgsql:host=$host;port=8088;dbname=$db_name", $db_user, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Обработка добавления товара
    if (isset($_POST['add_product'])) {
        $stmt = $conn->prepare("INSERT INTO Products (article, name_p, icon, description, country, price, quantity, warehouse_address) 
                               VALUES (:article, :name, :icon, :description, :country, :price, :quantity, :warehouse)");
        
        $stmt->execute([
            ':article' => $_POST['article'],
            ':name' => $_POST['name'],
            ':icon' => $_POST['icon'],
            ':description' => $_POST['description'],
            ':country' => $_POST['country'],
            ':price' => $_POST['price'],
            ':quantity' => $_POST['quantity'],
            ':warehouse' => $_POST['warehouse']
        ]);
        
        header("Location: admin.php");
        exit();
    }

    // Обработка удаления товара
    if (isset($_POST['delete_product'])) {
        $stmt = $conn->prepare("DELETE FROM Products WHERE article = :article");
        $stmt->execute([':article' => $_POST['article']]);
        
        header("Location: admin.php");
        exit();
    }

    // Обработка обновления товара
    if (isset($_POST['update_product'])) {
        $stmt = $conn->prepare("UPDATE Products 
                               SET name_p = :name, 
                                   icon = :icon,
                                   description = :description,
                                   country = :country,
                                   price = :price,
                                   quantity = :quantity,
                                   warehouse_address = :warehouse
                               WHERE article = :article");
        
        $stmt->execute([
            ':article' => $_POST['article'],
            ':name' => $_POST['name'],
            ':icon' => $_POST['icon'],
            ':description' => $_POST['description'],
            ':country' => $_POST['country'],
            ':price' => $_POST['price'],
            ':quantity' => $_POST['quantity'],
            ':warehouse' => $_POST['warehouse']
        ]);
        
        header("Location: admin.php");
        exit();
    }

    // Получение данных для отображения
    $stmt = $conn->query("SELECT * FROM Products ORDER BY article");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT * FROM Users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Админ-панель TechStyle</title>
    <link rel="stylesheet" href="../css/Main.css">
    <link rel="stylesheet" href="../css/mainFrame.css">
    <style>
        .admin-panel {
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            margin: 20px;
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .admin-table th, .admin-table td {
            border: 1px solid #4a6644;
            padding: 10px;
            text-align: left;
        }
        
        .admin-table th {
            background-color: #4a6644;
            color: #fff;
        }
        
        .admin-section {
            margin-bottom: 30px;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .admin-header h2 {
            margin: 0;
        }
    </style>
</head>
<body>
    <header class="admin-header-container">
        <div class="admin-header-content">
            <div class="admin-logo">
                <img src="../images/__.png" alt="TechStyle Logo">
            </div>
            <div class="admin-title-section">
                <h2>Админ-панель TechStyle</h2>
            </div>
            <div class="admin-user-info">
                <span>Администратор: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="admin-logout-btn">Выйти</a>
            </div>
        </div>
    </header>

    <div class="admin-panel">
        <div class="admin-section">
            <div class="admin-header">
                <h2>Управление товарами</h2>
                <button onclick="showAddForm()" class="admin-btn">Добавить товар</button>
            </div>

            <!-- Форма добавления товара -->
            <div id="addProductForm" style="display: none;" class="admin-form">
                <h3>Добавить новый товар</h3>
                <form method="POST" class="product-form">
                    <input type="text" name="article" placeholder="Артикул" required>
                    <input type="text" name="name" placeholder="Название товара" required>
                    <input type="text" name="icon" placeholder="Путь к изображению" required>
                    <input type="text" name="country" placeholder="Страна производства">
                    <input type="number" step="0.01" name="price" placeholder="Цена" required>
                    <input type="number" name="quantity" placeholder="Количество" required>
                    <input type="text" name="warehouse" placeholder="Адрес склада" required>
                    <textarea name="description" placeholder="Описание товара"></textarea>
                    <button type="submit" name="add_product">Добавить товар</button>
                </form>
            </div>

            <!-- Таблица товаров -->
            <table class="admin-table">
                <tr>
                    <th>Артикул</th>
                    <th>Название</th>
                    <th>Изображение</th>
                    <th>Описание</th>
                    <th>Страна</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Склад</th>
                    <th>Действия</th>
                </tr>
                <?php foreach ($products as $product): ?>
                <tr id="row_<?php echo $product['article']; ?>">
                    <td><?php echo htmlspecialchars($product['article']); ?></td>
                    <td><?php echo htmlspecialchars($product['name_p']); ?></td>
                    <td><?php echo htmlspecialchars($product['icon']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo htmlspecialchars($product['country']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?> ₽</td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['warehouse_address']); ?></td>
                    <td>
                        <button onclick="showEditForm('<?php echo $product['article']; ?>')" class="edit-btn">Изменить</button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="article" value="<?php echo htmlspecialchars($product['article']); ?>">
                            <button type="submit" name="delete_product" class="delete-btn" 
                                    onclick="return confirm('Удалить товар?')">Удалить</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="admin-section">
            <div class="admin-header">
                <h2>Пользователи</h2>
            </div>
            <table class="admin-table">
                <tr>
                    <th>ID</th>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Скидка</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['mail']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['role_user']); ?></td>
                    <td><?php echo htmlspecialchars($user['user_discount']); ?>%</td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div class="admin-section">
            <div class="admin-header">
                <h2>Заказы</h2>
            </div>
            <table class="admin-table">
                <tr>
                    <th>ID заказа</th>
                    <th>ID пользователя</th>
                    <th>Артикул</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Дата</th>
                </tr>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['article']); ?></td>
                    <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($order['total']); ?> ₽</td>
                    <td><?php echo htmlspecialchars($order['date_created']); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

    <script>
    function showAddForm() {
        document.getElementById('addProductForm').style.display = 'block';
    }

    function showEditForm(article) {
        // Получаем данные товара
        const row = document.getElementById('row_' + article);
        const cells = row.getElementsByTagName('td');

        // Создаем форму редактирования
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="article" value="${article}">
            <input type="text" name="name" value="${cells[1].textContent}" required>
            <input type="text" name="icon" value="${cells[2].textContent}" required>
            <textarea name="description">${cells[3].textContent}</textarea>
            <input type="text" name="country" value="${cells[4].textContent}">
            <input type="number" step="0.01" name="price" value="${cells[5].textContent.replace(' ₽', '')}" required>
            <input type="number" name="quantity" value="${cells[6].textContent}" required>
            <input type="text" name="warehouse" value="${cells[7].textContent}" required>
            <button type="submit" name="update_product">Сохранить</button>
            <button type="button" onclick="cancelEdit('${article}')">Отмена</button>
        `;

        // Заменяем содержимое ячеек на форму
        const cell = cells[1];
        cell.innerHTML = '';
        cell.appendChild(form);
    }

    function cancelEdit(article) {
        location.reload();
    }
    </script>

    <style>
    .admin-btn, .edit-btn, .delete-btn {
        padding: 5px 10px;
        margin: 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .admin-btn {
        background-color: #4a6644;
        color: white;
    }

    .edit-btn {
        background-color: #4a6644;
        color: white;
    }

    .delete-btn {
        background-color: #ff4444;
        color: white;
    }

    .admin-form {
        background-color: #f5f5f5;
        padding: 20px;
        margin: 20px 0;
        border-radius: 5px;
    }

    .product-form input, .product-form textarea {
        display: block;
        margin: 10px 0;
        padding: 5px;
        width: 100%;
        max-width: 300px;
    }
    </style>
</body>
</html>
