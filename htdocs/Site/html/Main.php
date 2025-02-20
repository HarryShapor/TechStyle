<?php
            session_start();

            // Проверяем наличие cookie и отсутствие активной сессии
            if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
                $_SESSION['user_id'] = $_COOKIE['user_id'];
                $_SESSION['username'] = $_COOKIE['username'];
                $_SESSION['email'] = $_COOKIE['email'];
            }

            // Подключение к БД
            $host = 'localhost';
            $db_name = 'techstyle';
            $db_user = 'postgres';
            $db_password = 'Fifa32rekrek';

            try {
                $conn = new PDO("pgsql:host=$host;port=8088;dbname=$db_name", $db_user, $db_password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Обработка формы авторизации
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $password = $_POST['password'];
                    
                    // Проверяем существование пользователя
                    $stmt = $conn->prepare("SELECT * FROM Users WHERE full_name = :name AND mail = :email");
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($user && $password === $user['password']) {
                        // Создаем сессию пользователя
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['full_name'];
                        $_SESSION['email'] = $user['mail'];
                        $_SESSION['role'] = $user['role_user'];
                        
                        // Сохраняем данные в cookie на месяц
                        $cookie_time = time() + (30 * 24 * 60 * 60); // 30 дней
                        setcookie('user_id', $user['id'], $cookie_time, '/');
                        setcookie('username', $user['full_name'], $cookie_time, '/');
                        setcookie('email', $user['mail'], $cookie_time, '/');
                        setcookie('role', $user['role_user'], $cookie_time, '/');
                        
                        header("Location: Main.php");
                        exit();
                    } else {
                        // Если пользователь не найден, перенаправляем на регистрацию
                        header("Location: registration.php");
                        exit();
                    }
                }
            } catch(PDOException $e) {
                $error_message = "Ошибка подключения к БД: " . $e->getMessage();
            }

            // Проверка, существует ли сессия для корзины, если нет - создаем
            // if (!isset($_SESSION['cart'])) {
            //     $_SESSION['cart'] = [];
            // }
            // Обработка данных формы
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
              if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
                $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
            
                try {
                    $stmt = $conn->prepare("SELECT * FROM Users WHERE full_name = :name AND mail = :email");
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->execute();
                    
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
                    if ($user && password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['full_name'];
                        $_SESSION['email'] = $user['mail'];
                        
                        // Перенаправляем на ту же страницу для обновления
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    } else {
                        $error_message = "Неверное имя пользователя или пароль";
                    }
                } catch(PDOException $e) {
                    $error_message = "Ошибка при авторизации: " . $e->getMessage();
                }
            }
              elseif ($_POST['submit'] === 'ДобавитьВКорзину'){
                $product_name = trim($_POST['product-name']);
                $product_quantity = intval($_POST['product-quantity']);
                $product_price = floatval($_POST['product-price']);
                $product_article = $_POST['product-article'];

                // Проверка на корректность данных
                if (!empty($product_name) && $product_quantity > 0 && $product_price > 0) {
                    // Проверяем наличие товара на складе
                    $stmt = $conn->prepare("SELECT quantity FROM Products WHERE article = :article");
                    $stmt->bindParam(':article', $product_article);
                    $stmt->execute();
                    $available = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($available && $available['quantity'] >= $product_quantity) {
                        // Добавляем товар в корзину
                        $_SESSION['cart'][] = [
                            'name' => $product_name,
                            'quantity' => $product_quantity,
                            'price' => $product_price,
                            'article' => $product_article,
                            'total' => $product_quantity * $product_price
                        ];
                        header("Location: Basket.php");
                        exit();
                    } else {
                        $alert = "Ошибка: недостаточно товара на складе.";
                        echo "<script type='text/javascript'>alert('$alert');</script>";
                    }
                } else {
                    $alert = "Ошибка: некорректные данные.";
                    echo "<script type='text/javascript'>alert('$alert');</script>";
                }
              }
            }
            $host = 'localhost'; // или ваш хост
            $db_name = 'techstyle'; // имя базы данных
            $db_user = 'postgres'; // имя пользователя базы данных
            $db_password = 'Fifa32rekrek'; // пароль базы данных

            try {
                $conn = new PDO("pgsql:host=$host;port=8088;dbname=$db_name", $db_user, $db_password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $connect_data = "host=localhost port=8088 dbname=techstyle user=postgres password=Fifa32rekrek";
            // $db_connect = pg_connect($connect_data);

            // Обработка поиска
            $search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';
            if (!empty($search_query)) {
                try {
                    $stmt = $conn->prepare("SELECT article, name_p, icon, description, country, price, quantity, warehouse_address 
                                           FROM Products 
                                           WHERE (name_p ILIKE :search 
                                           OR description ILIKE :search 
                                           OR article ILIKE :search)
                                           AND quantity > 0
                                           ORDER BY name_p");
                    
                    $search_param = "%{$search_query}%";
                    $stmt->bindParam(':search', $search_param);
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } catch(PDOException $e) {
                    echo "Ошибка поиска: " . $e->getMessage();
                }
            } else {
                // Существующий код получения всех товаров
                $stmt = $conn->query("SELECT article, name_p, icon, description, country, price, quantity, warehouse_address 
                                     FROM Products 
                                     WHERE quantity > 0
                                     ORDER BY name_p");
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            } catch (PDOException $e) {
                echo "Ошибка подключения: " . $e->getMessage();
            }


            
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Главная страница TechStyle</title>
        <link rel="stylesheet" href="../css/Main.css">
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
        <?php if (isset($_SESSION['order_success'])): ?>
            <script>
                alert('Заказ успешно оформлен!');
            </script>
            <?php unset($_SESSION['order_success']); ?>
        <?php endif; ?>
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
                        <form action="Main.php" method="get">
                            <input name="search_query" placeholder="Искать здесь..." type="search" 
                                   value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>">
                            <button class="circle" type="submit">
                                <img src="../search.png" alt="Поиск" height="25" width="25">
                            </button>
                        </form>
                    </div>
                    <div class="menu-buttons-block grid grid-10">
                        <button class="menu-button " aria-label="Навигация по сайту">
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
                                <label for="name" class="font-reg">Имя пользователя:</label>
                                <input class="inputform" type="text" id="name" name="name" required>
                                
                                <label for="email" class="font-reg">Электронная почта:</label>
                                <input class="inputform" type="email" id="email" name="email" required>
                                
                                <label for="password" class="font-reg">Пароль:</label>
                                <input class="inputform" type="password" id="password" name="password" required>
                                
                                <button id="loginButton" type="submit" name="login" value="login" class="button-reg">Войти</button>
                                
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
        <!-- Слайдер -->
        <div class="sl">
          <div style="visibility: hidden;"><button class="slider_btn_prev"><img src="../images/prev.png"> </button></div>
          <div class="slider">
            <div class="slider_line">
              <img class="slider_image" src="../images/slide1.jpg" id="switch1">
              <img class="slider_image" src="../images/slide2" id="switch2">
              <img class="slider_image" src="../images/slide3.jpg" id="switch3">
              <img class="slider_image" src="../images/slide4.jpg" id="switch4">
              <img class="slider_image" src="../images/slide5.jpg" id="switch5">
            </div>
          </div>
            <div style="visibility: hidden;"><button class="slider_btn_next"> <img src="../images/next.png"> </button></div>
        </div>
        <!-- Основная страница -->
        <div class="main">
            <h4 class="title-main text-center">Новинки и акции</h4>
            <?php if (!empty($search_query) && empty($products)): ?>
                <div class="no-results">
                    По запросу "<?php echo htmlspecialchars($search_query); ?>" ничего не найдено
                </div>
            <?php endif; ?>
            <!-- <?php 
                if ($_GET['admin']){
                  
                }
            ?> -->
            <?php $count = 0; ?>
            <div class="frame-main grid grid-4">
                <?php foreach ($products as $product): if ($product['quantity'] > 0):?>
                    <div class="frame grid grid-4-rows text-center">
                        <div class="img_card">
                            <img src="<?php echo htmlspecialchars($product['icon']); ?>" alt="<?php echo htmlspecialchars($product['name_p']); ?>">
                        </div>
                        <div>
                            <h4 class="product-name"><?php echo htmlspecialchars($product['name_p']); ?></h4>
                            <div class="button">
                                <a href="#0" id="basket" class="inBasket" data-name="<?php echo htmlspecialchars($product['name_p']); ?>" 
                                   data-price="<?php echo htmlspecialchars($product['price']); ?>"
                                   data-article="<?php echo htmlspecialchars($product['article']); ?>">В корзину</a>
                            </div>
                            <div class="price"><?php echo htmlspecialchars($product['price']); ?>₽</div>
                        </div>
                    </div>

                    <?php 
                    $count++;
                    // Закрываем div для "frame-main" после каждых 4 элементов
                    if ($count % 4 == 0): ?>
                        </div><div class="frame-main grid grid-4">
                    <?php endif; ?>
                    
                <?php endif; endforeach; ?>
            </div>

           
          <div id="basketForm" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form id="addToCartForm" class="add-to-cart-form" method="POST">
                    <h2>Добавить в корзину</h2>
                    <div class="product-info">
                        <input type="hidden" id="product-name" name="product-name">
                        <input type="hidden" id="product-price" name="product-price">
                        <input type="hidden" id="product-article" name="product-article">
                        <div class="product-title"></div>
                    </div>
                    
                    <div class="product-info">
                        <label for="product-quantity">Количество:</label>
                        <div class="quantity-container">
                            <button type="button" class="quantity-btn minus">-</button>
                            <input type="number" id="product-quantity" name="product-quantity" min="1" value="1" required>
                            <button type="button" class="quantity-btn plus">+</button>
                        </div>
                    </div>
                
                    <button type="submit" name="submit" value="ДобавитьВКорзину">Добавить в корзину</button>
                </form>
            </div>
          </div>
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
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('.inBasket');
            const modal = document.getElementById('basketForm');
            const closeBtn = modal.querySelector('.close');
            const form = document.getElementById('addToCartForm');
            
            links.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const name = this.dataset.name;
                    const price = this.dataset.price;
                    const article = this.dataset.article;
                    
                    document.getElementById('product-name').value = name;
                    document.getElementById('product-price').value = price;
                    document.getElementById('product-article').value = article;
                    modal.querySelector('.product-title').textContent = name;
                    
                    modal.classList.add('show');
                });
            });
            
            closeBtn.addEventListener('click', function() {
                modal.classList.remove('show');
            });

            // Закрытие по клику вне модального окна
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
        </script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const quantityInput = document.getElementById('product-quantity');
            const minusBtn = document.querySelector('.quantity-btn.minus');
            const plusBtn = document.querySelector('.quantity-btn.plus');

            minusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });

            plusBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                quantityInput.value = currentValue + 1;
            });
        });
        </script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const openModalBtn = document.getElementById('openModal');
            const modal = document.getElementById('myModal');
            const closeBtn = modal.querySelector('.close');

            openModalBtn.addEventListener('click', function() {
                modal.classList.add('show');
            });

            closeBtn.addEventListener('click', function() {
                modal.classList.remove('show');
            });

            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });
        });
        </script>
    </body>
</html>