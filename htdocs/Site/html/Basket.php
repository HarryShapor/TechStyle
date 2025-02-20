<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();  
}

// Проверяем авторизацию для доступа к корзине
if (!isset($_SESSION['user_id'])) {
    header("Location: Main.php");
    exit();
}

// Очистка корзины
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: Basket.php");
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
    
    header("Location: Basket.php");
    exit();
}

if(isset($_GET['logout'])) {
    if (!empty($_SESSION['cart'])) {
      // Открываем файл для записи (если файла нет, он будет создан)
      $file = 'basket_items.txt';
      
      // Создаем строку для записи
      $output = "Содержимое корзины:\n";
      $total_sum1 = 0;
  
      // Проходим по каждому товару в корзине
      foreach ($_SESSION['cart'] as $item) {
          $output .= sprintf(
              "Название товара: %s, Количество: %d, Цена за единицу: %.2f ₽, Общая цена: %.2f ₽\n",
              htmlspecialchars($item['name']),
              htmlspecialchars($item['quantity']),
              htmlspecialchars($item['price']),
              htmlspecialchars($item['total'])
          );
          $total_sum1 += $item['total'];
      }
      
      // Записываем данные в файл
      file_put_contents($file, $output, FILE_APPEND | LOCK_EX);
    }
    session_unset();
  }

  if (isset($_GET['admin'])){
    header("Location: file.php");
    
  }

        $host = 'localhost'; // или ваш хост
        $db_name = 'techstyle'; // имя базы данных
        $db_user = 'postgres'; // имя пользователя базы данных
        $db_password = 'Fifa32rekrek'; // пароль базы данных

        try {                  
            $conn = new PDO("pgsql:host=$host;port=8088;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

      // Проверка метода запроса
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
          // Получение данных из формы
          $id_user = $_POST['id_user'];
          $article_p = $_POST['name'];
          $count_p = $_POST['quantity'] ?? 1; // значение по умолчанию
          $cost_p = $_POST['price'];
          $address_store = $_POST['address_store'];
          $date_create = date('Y-m-d H:i:s'); // текущая дата и время

          // Подготовка SQL-запроса
          $sql = "INSERT INTO Basket (id_user, article_p, count_p, cost_p, date_create, address_store) 
                  VALUES (:id_user, :name, :quantity, :price, :date_create, :address_store)";

          // Подготовка запроса
          $stmt = $pdo->prepare($sql);

          // Привязка параметров
          $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
          $stmt->bindParam(':name', $article_p, PDO::PARAM_STR);
          $stmt->bindParam(':quantity', $count_p, PDO::PARAM_INT);
          $stmt->bindParam(':price', $cost_p, PDO::PARAM_STR);
          $stmt->bindParam(':date_create', $date_create, PDO::PARAM_STR);
          $stmt->bindParam(':address_store', $address_store, PDO::PARAM_STR);

          // Выполнение запроса
          if ($stmt->execute()) {
              echo "Товар успешно добавлен в корзину.";
          } else {
              echo "Ошибка при добавлении товара.";
          }
      }

      // Функция обновления количества товара
      function updateProductQuantity($conn, $article, $quantity) {
          try {
              // Сначала проверяем текущее количество
              $checkStmt = $conn->prepare("SELECT quantity FROM Products WHERE article = :article FOR UPDATE");
              $checkStmt->execute([':article' => $article]);
              $currentQuantity = $checkStmt->fetch(PDO::FETCH_ASSOC);

              if ($currentQuantity && $currentQuantity['quantity'] >= $quantity) {
                  $stmt = $conn->prepare("UPDATE Products 
                                        SET quantity = quantity - :quantity 
                                        WHERE article = :article 
                                        AND quantity >= :quantity");
                  
                  $stmt->execute([
                      ':quantity' => $quantity,
                      ':article' => $article
                  ]);

                  return $stmt->rowCount() > 0;
              }
              return false;
          } catch(PDOException $e) {
              error_log("Ошибка обновления количества: " . $e->getMessage());
              return false;
          }
      }

      // Обработка оформления заказа
      if (isset($_POST['checkout']) && !empty($_SESSION['cart'])) {
          try {
              $conn->beginTransaction();
              $success = true;
              $errors = [];

              // Проверяем наличие товаров
              foreach ($_SESSION['cart'] as $item) {
                  $stmt = $conn->prepare("SELECT quantity FROM Products WHERE article = :article FOR UPDATE");
                  $stmt->execute([':article' => $item['article']]);
                  $product = $stmt->fetch(PDO::FETCH_ASSOC);

                  if (!$product || $product['quantity'] < $item['quantity']) {
                      $success = false;
                      $errors[] = "Недостаточно товара '{$item['name']}' на складе. Доступно: " . 
                                 ($product ? $product['quantity'] : 0);
                  }
              }

              if ($success) {
                  // Обновляем количество товаров и создаем заказы
                  foreach ($_SESSION['cart'] as $item) {
                      // Обновляем количество товара
                      if (!updateProductQuantity($conn, $item['article'], $item['quantity'])) {
                          $success = false;
                          $errors[] = "Ошибка при обновлении количества товара '{$item['name']}'";
                          break;
                      }

                      // Добавляем запись в таблицу Orders
                      $stmt = $conn->prepare("INSERT INTO Orders (id_user, article_p, price, data_purchase) 
                                            VALUES (:id_user, :article_p, :price::money, CURRENT_TIMESTAMP)");
                      
                      $total_price = $item['quantity'] * $item['price']; // Вычисляем общую стоимость
                      
                      $stmt->execute([
                          ':id_user' => $_SESSION['user_id'],
                          ':article_p' => $item['article'],
                          ':price' => $total_price
                      ]);

                      if ($stmt->rowCount() == 0) {
                          $success = false;
                          $errors[] = "Ошибка при создании заказа для товара '{$item['name']}'";
                          break;
                      }
                  }

                  if ($success) {
                      $conn->commit();
                      $_SESSION['cart'] = array();
                      $_SESSION['order_success'] = true;
                      header("Location: Main.php");
                      exit();
                  }
              }

              if (!$success) {
                  $conn->rollBack();
                  foreach ($errors as $error) {
                      echo "<div class='error-message'>$error</div>";
                  }
              }

          } catch(PDOException $e) {
              $conn->rollBack();
              error_log("Ошибка оформления заказа: " . $e->getMessage());
              echo "<div class='error-message'>Произошла ошибка при оформлении заказа. Пожалуйста, попробуйте позже.</div>";
          }
      }
  } catch (PDOException $e) {
      error_log("Ошибка подключения к БД: " . $e->getMessage());
      echo "Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.";
  }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Корзина TechStyle</title>
        <link rel="stylesheet" href="../css/Main.css">
        <link rel="stylesheet" href="../css/basketStyle.css?<?echo time();?>">
        <link rel="stylesheet" href="../css/regForm.css">
        <link rel="stylesheet" type="text/css" href="../css/sliderAnim.css">
        <link rel="stylesheet" type="text/css" href="../css/mainFrame.css">
        <link rel="stylesheet" type="text/css" href="../css/Basement.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../js/jQuery.js" ></script>
        <script src="../js/validate.js" ></script>
        <!-- Шрифты -->
        <link id="u-page-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster:400">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,400;0,500;1,100&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&display=swap" rel="stylesheet">
        <!-- jQuery library -->
        <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"> </script>
    </head>
    <body>
        <!-- Шапка -->
        <header class="grid">
            <div class="grid grid-2-columns-hat">
                <div class="logo" name="logo">
                    <img src="../images/__.png">
                </div>
                <div class="title_and_search grid grid-3-rows">
                    <div class="title" name="title">
                      <h2><a href="Main.php">TechStyle</a></h2>
                    </div>
                    <div class="search" name="search">
                        <form action="" method="get">
                          <input name="s" placeholder="Искать здесь..." type="search">
                          <button class="circle" type="submit"><img src="../search.png" alt="Описание иконки" height="25" width="25"></button>
                        </form>
                    </div>
                    <div class="menu-buttons-block grid grid-10">
                        <button class="menu-button" aria-label="Навигация по сайту">
                            <span class="stick-menu"></span>
                        </button>
                        <div class="menu"><a href=#>Акции</a></div>
                        <div class="menu"><a href=#>Магазины</a></div>
                        <div class="menu"><a href=#>Доставка</a></div>
                        <div class="menu"><a href=#>Покупателям</a></div>
                        <div class="menu"><a href=#>Избранное</a></div>
                        <div class="menu">
                            <a href="<?php echo isset($_SESSION['user_id']) ? 'Basket.php' : 'Main.php'; ?>" 
                               <?php if (!isset($_SESSION['user_id'])) echo 'onclick="alert(\'Для доступа к корзине необходимо авторизоваться\'); return false;"'; ?>>
                                Корзина
                            </a>
                        </div>
                        <div class="menu">
                            <?php if (isset($_SESSION['username'])): ?>
                                <div class="user-info">
                                    <span name="username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                                    <a href="logout.php" class="logout-btn">Выйти</a>
                                </div>
                            <?php else: ?>
                                <a class="reg" id="openModal">Войти</a>
                            <?php endif; ?>
                        </div>
                        <div id="myModal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h2 class="text-center regName">Авторизация</h2>
                                <?php if (isset($error_message)): ?>
                                    <div class="error-message" style="color: red; text-align: center; margin-bottom: 10px;">
                                        <?php echo htmlspecialchars($error_message); ?>
                                    </div>
                                <?php endif; ?>
                                <form class="forma" action="Main.php" method="POST" id="registrationForm">
                                    <div class="form-group text-center nameBlock">
                                        <label for="name" class="font-reg">Имя пользователя:</label>
                                        <input class="inputform" type="text" id="name" name="name" required>
                                    </div>
                                    <div class="form-group text-center emailBlock">
                                        <label for="email" class="font-reg">Электронная почта:</label>
                                        <input class="inputform" type="email" id="email" name="email" required>
                                    </div>
                                    <div class="form-group text-center">
                                        <label for="password" class="font-reg">Пароль:</label>
                                        <input class="inputform" type="password" id="password" name="password" required>
                                    </div>
                                    <div class="form-group text-center">
                                        <button type="submit" name="login" value="login" class="button-reg">Войти</button>
                                    </div>
                                    <div class="reg">
                                        <a href="registration.php">Зарегистрироваться?</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
        </header>
        <!-- Основная страница -->
        <div class="main">
            <!-- <h4 class="title-main text-center">Корзина</h4> -->
            <h2  class="title-main text-center">Содержимое вашей корзины:</h2>

            <?php if (!empty($_SESSION['cart'])): ?>
              <div class="basket text-center">
                <table class="basketTable">
                    <tr>
                        <th>Название товара</th>
                        <th>Количество</th>
                        <th>Цена за единицу</th>
                        <th>Общая цена</th>
                        <th>Действия</th>
                    </tr>

                    <?php 
                    $total_sum = 0;
                    foreach ($_SESSION['cart'] as $index => $item): 
                        $total_sum += $item['total'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                    <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                           min="0" class="quantity-input">
                                    <button type="submit" name="update_quantity" class="update-btn">Обновить</button>
                                </form>
                            </td>
                            <td><?php echo htmlspecialchars($item['price']); ?> ₽</td>
                            <td><?php echo htmlspecialchars($item['total']); ?> ₽</td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                    <input type="hidden" name="quantity" value="0">
                                    <button type="submit" name="update_quantity" class="delete-btn">Удалить</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
                <h3 class="itogo">Общая сумма: <?php echo htmlspecialchars($total_sum); ?> ₽</h3>
                
                <div class="basket-buttons">
                    <form method="post" style="display: inline-block;">
                        <button type="submit" name="clear_cart" class="clear-cart-btn">Очистить корзину</button>
                    </form>
                    <a href="?logout" class="send">Оформить заказ</a>
                    <a href="Main.php" class="return">Вернуться к покупкам</a>
                </div>
              </div>
              <?php else: ?>
                <div class="empty-cart text-center">
                    <p>Ваша корзина пуста.</p>
                    <a href="Main.php" class="return">Перейти к покупкам</a>
                </div>
              <?php endif; ?>
        </div>
        <!-- Подвал -->
        <footer>
            <div class="footer grid-2">
                <div class="logo-footer grid-3">
                    <img src="../images/__.png">
                </div>
                <div class="info grid grid-4">
                      <div class="info-sector">
                        <ul class="column1" style="list-style-type: none">
                          <li>О Компании</li>
                          <li>Новости</li>
                          <li>Партнерам</li>
                          <li>Вакансии</li>
                          <li>Политика конфиденциальности</li>
                          <li>Персональные данные</li>
                          <li>Правила продаж</li>
                          <li>Правила пользования сайта</li>
                        </ul>
                      </div>
                      <div class="info-sector2">
                        <ul class="column3" style="list-style-type: none">
                          <li>Как оформить заказ</li>
                          <li>Способы оплаты</li>
                          <li>Кредиты</li>
                          <li>Доставка</li>
                          <li>Статус заказа</li>
                          <li>Обмен, возврат, гарантия</li>
                        </ul>
                      </div>
                    <div class="info-sector3">
                        <ul class="column2" style="list-style-type: none">
                          <li>Юридическим лицам&nbsp;</li>
                          <li>Подарочные карты</li>
                          <li>Бонусная программа</li>
                          <li>Помощь</li>
                          <li>Обратная связь</li>
                        </ul>
                    </div>
                  </div>
                </div>
        </footer>
    </body>
</html>