<?php

chdir(__DIR__ . '/config');
require_once './Database.php';
chdir(__DIR__);

$database = new Database();

?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Studenti</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
</head>
<body>
<div class="container-fluid mt-3">
<?php
try {
    $dbConnection = $database->connect();
    $sqlSelectStudents = 'SELECT students.id, students.name, students.surname,
                                students.email, students.street, students.city, students.zip,
                                regions.name AS region_name, students.gender
                         FROM students
                         INNER JOIN regions ON students.region_id = regions.id
                         ORDER BY students.id';
    $prepareStatementStudents = $dbConnection->prepare($sqlSelectStudents);
    $prepareStatementStudents->execute();
    $collectionStudents = $prepareStatementStudents->fetchAll();

    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="thead-dark"><tr>
            <th scope="col">ID</th>
            <th scope="col">Jméno</th>
            <th scope="col">Příjmení</th>
            <th scope="col">E-mail</th>
            <th scope="col">Ulice a č.p.</th>
            <th scope="col">Město</th>
            <th scope="col">PSČ</th>
            <th scope="col">Kraj</th>
            <th scope="col">Pohlaví</th>
          </tr></thead>';
    echo '<tbody>';

    foreach ($collectionStudents as $row) {
        echo '<tr>';
        echo '<th scope="row">' . (int) $row['id'] . '</th>';
        echo '<td>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['surname'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['street'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['city'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td class="text-nowrap">' . htmlspecialchars(substr($row['zip'], 0, 3) . ' ' . substr($row['zip'], 3), ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . htmlspecialchars($row['region_name'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>';
        if ($row['gender'] === 'muz') {
            echo 'Muž';
        } else {
            echo 'Žena';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
} catch (PDOException $e) {
    echo '<p>Studenty se nepodařilo načíst.</p>';
}
?>
</div>
</body>
</html>
