<?php
    // session_start();

    // Проверка, существует ли сессия для корзины, если нет - создаем
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Очистка корзины
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Обновление количества товара
    if (isset($_POST['update_quantity'])) {
        $index = $_POST['item_index'];
        $new_quantity = (int)$_POST['quantity'];
        
        if ($new_quantity > 0) {
            $_SESSION['cart'][$index]['quantity'] = $new_quantity;
            $_SESSION['cart'][$index]['total'] = $new_quantity * $_SESSION['cart'][$index]['price'];
        } elseif ($new_quantity == 0) {
            // Удаляем товар из корзины
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Переиндексируем массив
        }
        
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    
    // Обработка данных формы
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $product_name = trim($_POST['product_name']);
        $product_price = floatval($_POST['product_price']);
        $product_quantity = max(1, (int)$_POST['product_quantity']);
    
        // Проверка на корректность данных
        if (!empty($product_name) && $product_price > 0) {
            // Проверяем, есть ли уже такой товар в корзине
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['name'] === $product_name) {
                    $item['quantity'] += $product_quantity;
                    $item['total'] = $item['quantity'] * $item['price'];
                    $found = true;
                    break;
                }
            }
            
            // Если товар не найден, добавляем новый
            if (!$found) {
                $_SESSION['cart'][] = [
                    'name' => $product_name,
                    'price' => $product_price,
                    'quantity' => $product_quantity,
                    'total' => $product_price * $product_quantity
                ];
            }
        } else {
            echo "Ошибка: некорректные данные.";
        }
    }
    
    // Вывод содержимого корзины
    if (!empty($_SESSION['cart'])) {
        echo "<h2>Содержимое вашей корзины:</h2>";
        echo "<form method='post'>";
        echo "<table border='1'>
                <tr>
                    <th>Название товара</th>
                    <th>Цена за единицу</th>
                    <th>Количество</th>
                    <th>Общая стоимость</th>
                    <th>Действия</th>
                </tr>";
        
        $total = 0;
    
        foreach ($_SESSION['cart'] as $index => $item) {
            echo "<tr>
                    <td>{$item['name']}</td>
                    <td>{$item['price']} ₽</td>
                    <td>
                        <form method='post' style='display: inline;'>
                            <input type='hidden' name='item_index' value='{$index}'>
                            <input type='number' name='quantity' value='{$item['quantity']}' min='0' style='width: 60px'>
                            <button type='submit' name='update_quantity'>Обновить</button>
                        </form>
                    </td>
                    <td>{$item['total']} ₽</td>
                    <td>
                        <form method='post' style='display: inline;'>
                            <input type='hidden' name='item_index' value='{$index}'>
                            <input type='hidden' name='quantity' value='0'>
                            <button type='submit' name='update_quantity'>Удалить</button>
                        </form>
                    </td>
                  </tr>";
            $total += $item['total'];
        }
    
        echo "</table>";
        echo "<h3>Общая сумма: {$total} ₽</h3>";
        
        // Кнопка очистки корзины
        echo "<form method='post' style='margin-top: 20px;'>
                <button type='submit' name='clear_cart'>Очистить корзину</button>
              </form>";
    } else {
        echo "<h2>Ваша корзина пуста.</h2>";
    }
?>