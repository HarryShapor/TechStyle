<?php
        $host = 'localhost'; // или ваш хост
        $db_name = 'techstyle'; // имя базы данных
        $db_user = 'postgres'; // имя пользователя базы данных
        $db_password = 'Fifa32rekrek'; // пароль базы данных

        try {                  
            $conn = new PDO("pgsql:host=$host;port=8088;dbname=$db_name", $db_user, $db_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                                                      
            // $conn = "host=localhost port=8088 dbname=techstyle user=postgres password=Fifa32rekrek";
            // $db_connect = pg_connect($conn);
            if ($_SERVER["REQUEST_METHOD"] == "POST") { 
                $username = trim($_POST['username']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password_user']);
                $confirm_password = trim($_POST['confirm_password']);
                $role = 'simple';
                $errors = [];

                // Валидация имени пользователя
                if (empty($username)) {
                    $errors[] = "Имя пользователя обязательно.";
                }

                // Валидация электронной почты
                if (empty($email)) {
                    $errors[] = "Электронная почта обязательна.";
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Некорректный формат электронной почты.";
                }

                // Валидация пароля
                if (empty($password)) {
                    $errors[] = "Пароль обязателен.";
                } elseif (strlen($password) < 8) {
                    $errors[] = "Пароль должен содержать не менее 8 символов.";
                }

                // Проверка совпадения паролей
                if ($password !== $confirm_password) {
                    $errors[] = "Пароли не совпадают.";
                }

                // Если нет ошибок, сохраняем данные в базу
                if (empty($errors)) {
                    // Хеширование пароля перед сохранением
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    // Подготовка SQL-запроса
                    $stmt = $conn->prepare("INSERT INTO Users (full_name, mail, password, role_user) VALUES (:username, :email, :password_user, :role_user)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password_user', $password);
                    $stmt->bindParam(':role_user', $role);
                    // Выполнение запроса и проверка на ошибки
                    if ($stmt->execute()) {
                        header("Location: Main.php");
                    } else {
                        echo "<p style='color:red;'>Ошибка: не удалось зарегистрироваться.</p>";
                    }
                } else {
                    foreach ($errors as $error) {
                        echo "<p style='color:red;'>$error</p>";
                    }
                }
            }
        }
        catch (PDOException $e) {
            echo "Ошибка подключения: " . $e->getMessage();
        }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма регистрации</title>
    <link rel="stylesheet" href="../css/Main.css">
    <link rel="stylesheet" href="../css/Registration.css">
        <link rel="stylesheet" type="text/css" href="../css/mainFrame.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="../js/jQuery.js" ></script>
        <script src="../js/validate.js" ></script>
        <!-- Шрифты -->
        <link id="u-page-google-font" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster:400">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,400;0,500;1,100&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Edu+AU+VIC+WA+NT+Pre:wght@400..700&display=swap" rel="stylesheet">
        <!--  -->
        <!-- jQuery library -->
        <script src= "https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <!-- Popper JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <!-- Latest compiled JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"> </script>
        
</head>
<body>
    <header class="registration-header">                                                                    
        <div class="title">
            <h2><a href="Main.php">TechStyle</a></h2>
        </div>
    </header>
    <div class="body">
        <h2>Регистрация</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form class="forma" action="registration.php" method="POST">
            <div class="form-group">
                <label for="username" class="font-reg">Имя пользователя</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="email" class="font-reg">Электронная почта</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password_user" class="font-reg">Пароль</label>
                <input type="password" id="password_user" name="password_user" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password" class="font-reg">Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <input type="hidden" id="role_user" name="role_user" value="simple">
            <input type="submit" value="Зарегистрироваться">
        </form>
    </div>
</body>
</html>