<?php
declare(strict_types=1);

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$students = [];
$error = null;

try {
    $config = parse_ini_file(__DIR__ . '/config/.env');
    if ($config === false) {
        throw new RuntimeException('Nepodařilo se načíst nastavení databáze.');
    }
    $connection = new PDO(
        'mysql:host=' . $config['DB_HOST'] . ';dbname=' . $config['DB_NAME'] . ';charset=utf8mb4',
        $config['DB_USER'],
        $config['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $statement = $connection->prepare(
        'SELECT s.id, s.name, s.surname, s.email, s.street, s.city, s.zip,
                r.name AS region_name, s.gender
         FROM students AS s
         INNER JOIN regions AS r ON s.region_id = r.id
         ORDER BY s.id'
    );
    $statement->execute();
    $students = $statement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException | RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(503);
    $error = 'Studenty se nepodařilo načíst.';
}
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
    <?php if ($error !== null): ?>
        <p><?= $escape($error) ?></p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Jméno</th>
                        <th scope="col">Příjmení</th>
                        <th scope="col">E-mail</th>
                        <th scope="col">Ulice a č.p.</th>
                        <th scope="col">Město</th>
                        <th scope="col">PSČ</th>
                        <th scope="col">Kraj</th>
                        <th scope="col">Pohlaví</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <th scope="row"><?= (int) $student['id'] ?></th>
                            <td><?= $escape($student['name']) ?></td>
                            <td><?= $escape($student['surname']) ?></td>
                            <td><?= $escape($student['email']) ?></td>
                            <td><?= $escape($student['street']) ?></td>
                            <td><?= $escape($student['city']) ?></td>
                            <td class="text-nowrap"><?= $escape(substr($student['zip'], 0, 3) . ' ' . substr($student['zip'], 3)) ?></td>
                            <td><?= $escape($student['region_name']) ?></td>
                            <td><?= $student['gender'] === 'muz' ? 'Muž' : 'Žena' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
