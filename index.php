<?php
/* создаем БД
CREATE TABLE `gromtestbase`.`sorttable` ( `id` INT NOT NULL AUTO_INCREMENT , `content` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

для уникальности:
alter table sorttable add hash binary(16) null;

update `sorttable` set `hash`=unhex(md5(`content`));

create unique index `sorttable_column_uindex` on `sorttable` (`hash`);
*/

// Параметры для подключения
$db_host = "localhost"; 
$db_user = "user_login"; // Логин БД
$db_password = "password"; // Пароль БД
$db_base = "db_name"; // Имя БД
$db_table = "sorttable"; // Имя Таблицы БД

// Подключение к базе данных
$mysqli = new mysqli($db_host,$db_user,$db_password,$db_base);

// Если есть ошибка соединения, выводим её и убиваем подключение
if ($mysqli->connect_error) {
    die('Ошибка : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

// массив
$firstarr = array(
    'Пожалуйста,',
    'Просто',
    'Если сможете,'
);
$secondarr = array(
    'удивительное',
    'крутое',
    'простое',
    'важное',
    'бесполезное'
);
$thirdarr = array(
    'изменялось',
    'случайным образом',
    'менялось каждый раз'
);
$fourtharr = array (
    'быстро',
    'мгновенно',
    'оперативно',
    'правильно'
);

// случайный выбор из массива
function randarr ($array) {
    $random_keys = array_rand($array,2);
    return $array[$random_keys[0]];
}

// переменная результата
$result = '<b>'.randarr($firstarr).'</b>'.' сделайте так, чтобы это '.'<b>'.randarr($secondarr).'</b>'.' тестовое предложение '.'<b>'.randarr($thirdarr).' '.randarr($fourtharr).'</b>.';

$text = $_POST['content'];

// Запись в БД
if ( isset($_POST['content'])){

    $resultsql = $mysqli->query("INSERT INTO `$db_table` (`content`) VALUES ('$text')");

    // Вывод отчета
    if ($resultsql == true){
        echo "Информация занесена в базу данных";
    }else{
        echo "Информация <b>не</b> занесена в базу данных";
    }
}

// отобразим результат
echo $resultsql;

// проверка на совпадение в бд
$stmt = $mysqli->prepare("INSERT `$db_table` (`content`, `hash`) VALUES (?, unhex(md5(?)))");
$stmt->bind_param("ss", $result, $result);
if($stmt->execute()) {
    echo 'проверка прошла</br>';
} else {
    echo '<b>уже есть такая запись</b></br>';
}

// Вывод отчета
if ($resultsql == true){
    echo "Информация занесена в базу данных";
} else {
    echo "Информация <b>не</b> занесена в базу данных";
}

// Удаление из БД
// переменная удаления
$deleteId = $_POST['del-id'];

if (isset($_POST['del-id'])){

    $resultsql = $mysqli->query("DELETE FROM `$db_table` WHERE `$db_table`.`id` = ('$deleteId')");

    // Вывод отчета
    if ($resultsql == true){
        echo "Информация удалена из базы данных";
    }else{
        echo "Информация не удалена из базы данных";
    }
}

?>
<html>
<head>
    <meta content="text/html; charset=utf-8">
    <title>Запись в БД через форму на php</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
body {
    font-family: "Open Sans";
    margin: auto;
}
header {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
}
table tbody tr:first-child {
    text-transform: uppercase;
}
table tbody tr td {word-break: break-word;min-width: 35px;}

table tbody tr:nth-child(2n) {background: #ccc;}

.result {
    display: flex;
    justify-content: center;
    width: 70%;
    margin: auto;
}
.actionform {
    display: block;
    position: fixed;
    top: 20px;
    right: 10px;
    width: 170px;
    border: 1px solid #eee;
    background: #01dbff;
}
.addform {
    width: 100%;
    margin: auto;
}
.delform {
    width: 100%;
    margin: auto;
}
</style>
</head>
<header>
    <a href="./">индекс /</a>
    <a href="../">/ вверх</a>
</header>
<body>
<div class="show"><?php echo $result; ?></div>
<div class="result">
<?php
$query ="SELECT * FROM `$db_table` ORDER BY `id` DESC";

$resultsql = mysqli_query($mysqli, $query) or die("Ошибка " . mysqli_error($mysqli)); 
if($resultsql)
{
    $rows = mysqli_num_rows($resultsql); // количество полученных строк
     
    echo "<table><tr><th>Id</th><th>Предложение</th></tr>";
    for ($i = 0 ; $i < $rows ; ++$i)
    {
        $row = mysqli_fetch_row($resultsql);
        echo "<tr>";
            for ($j = 0 ; $j < 3 ; ++$j) echo "<td>$row[$j]</td>";
        echo "</tr>";
    }
    echo "</table>";

    // очищаем результат
    mysqli_free_result($resultsql);
}
?>
</div>
<?php
mysqli_close($mysqli);
?>
<section class="actionform">
    <div class="addform">
        <form method="POST" action="">
            <input name="content" type="text" placeholder="Имя"/><br />
            <div class="" name=""></div>
            <input type="submit" value="Записать"/>
            <hr>
        </form>
    </div>
    <div class="delform">
        <form method="POST" action="">
            <input name="del-id" type="number" placeholder="Введите ID"/>
            <input type="submit" value="Удалить"/>
        </form>
    </div>
</section>
</body>
</html>
