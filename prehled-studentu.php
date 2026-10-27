<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/Database.php';

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$students = [];
$error = null;
$createdStudentId = $_SESSION['created_student_id'] ?? null;
unset($_SESSION['created_student_id']);

try {
    $connection = (new Database())->connect();
    $students = $connection->query(
        'SELECT s.id, s.name, s.surname, s.email, s.street, s.city, s.zip,
                r.name AS region_name, s.gender, s.note
         FROM students AS s
         INNER JOIN regions AS r ON s.region_id = r.id
         ORDER BY s.id'
    )->fetchAll();
} catch (PDOException | RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(503);
    $error = 'Studenty se nepodařilo načíst. Zkuste to prosím později.';
}

$pageTitle = 'Přehled studentů';
require __DIR__ . '/html/ukol3Head.php';
?>
<div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
    <div>
        <p class="text-muted mb-1">Evidence studentů</p>
        <h1 class="h2 mb-0">Přehled studentů</h1>
    </div>
    <a href="form.php" class="btn btn-primary my-2">Přidat studenta</a>
</div>

<?php if ($error !== null): ?>
    <div class="alert alert-danger" role="alert"><?= $escape($error) ?></div>
<?php else: ?>
    <?php if ($createdStudentId !== null): ?>
        <div class="alert alert-success" role="status">Student byl úspěšně uložen.</div>
    <?php endif; ?>
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <strong>Všichni studenti</strong>
            <span class="badge badge-primary badge-pill">Počet záznamů: <?= count($students) ?></span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <caption class="px-3">Seznam studentů včetně kontaktních údajů a kraje.</caption>
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
                        <th scope="col">Poznámka</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($students === []): ?>
                        <tr><td colspan="10" class="text-center text-muted py-4">Zatím tu není žádný student.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $student): ?>
                        <tr<?= (int) $student['id'] === $createdStudentId ? ' class="table-success"' : '' ?>>
                            <th scope="row"><?= (int) $student['id'] ?></th>
                            <td><?= $escape($student['name']) ?></td>
                            <td><?= $escape($student['surname']) ?></td>
                            <td><?= $escape($student['email']) ?></td>
                            <td><?= $escape($student['street']) ?></td>
                            <td><?= $escape($student['city']) ?></td>
                            <td class="text-nowrap"><?= $escape(substr($student['zip'], 0, 3) . ' ' . substr($student['zip'], 3)) ?></td>
                            <td><?= $escape($student['region_name']) ?></td>
                            <td><?= $student['gender'] === 'muz' ? 'Muž' : 'Žena' ?></td>
                            <td><?= nl2br($escape($student['note'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/html/ukol3Foot.php'; ?>
