<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Табачкова А.М., Шапоренко А.С. №1</title>
        <style>
            #tbl tbody tr:nth-child(2n+1){
                background-color: #86e88b;
            }
            #tbl tbody tr:nth-child(2n){
                background-color: #de93fa;
            }
            thead {
                font-family: "Times New Roman";
            }
            table {
                font-size: 10pt;
                text-align: center;
                vertical-align: middle;
                border: 1px solid black;              }
            caption {
                text-align: center;
                padding-right: 60px;
                font-family:'Arial Narrow';
                font-weight:bold;
                font-size:22px;
            }
            td {
                border: 1px solid black;
                padding: 2px 5px;
            }
            table thead {
                font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif ;
            }
        </style>
    </head>
    <body>
        <?php
            function f(){
                echo "<table id='tbl'><caption>Таблица</caption><tbody><tr>
                <td> Значение x </td> <td> Значение функции</td></tr>";
                $x = 50;
                $result = 0.0;
                for ($x = 50; $x <= 70; $x++){
                    $result = $x**$x + 2*exp(($x-1));
                    echo "<tr><td> $x </td><td>  $result </td></tr>"; 
                }
                echo "</tbody></table>";
            }
            f();
        ?>
    </body>
</html>