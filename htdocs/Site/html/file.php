<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Файл TechStyle</title>
    </head>
    <body>
        <?php
            $file = 'basket_items.txt';
            // Проверяем, существует ли файл
            if (file_exists($file)) {
                // Читаем содержимое файла
                $content = file_get_contents($file);
                
                // Выводим содержимое на экран
                echo '<div>' . htmlspecialchars($content) . '</div>';
            } else {
                echo '<p>Файл не найден.</p>';
            }
        ?>
    </body>
</html>
