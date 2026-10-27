<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config/Database.php';

$escape = static fn ($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$values = array_fill_keys(['email', 'firstname', 'surname', 'street', 'city', 'zip', 'region', 'gender', 'note'], '');
$errors = [];
$regions = [];
$connection = null;

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $connection = (new Database())->connect();
    $regions = $connection->query('SELECT id, name FROM regions ORDER BY name')->fetchAll();
} catch (PDOException | RuntimeException $e) {
    error_log($e->getMessage());
    http_response_code(503);
    $errors[] = 'Údaje se nepodařilo načíst. Zkuste to prosím později.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $connection !== null && $regions !== []) {
    foreach ($values as $field => $value) {
        $values[$field] = is_string($_POST[$field] ?? null) ? trim($_POST[$field]) : '';
    }
    $values['zip'] = preg_replace('/\s/u', '', $values['zip']) ?? '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $password2 = is_string($_POST['password2'] ?? null) ? $_POST['password2'] : '';

    if (!is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = 'Platnost formuláře vypršela. Odešlete ho prosím znovu.';
    }
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($values['email']) > 254) {
        $errors[] = 'Vyplňte platný e-mail do 254 znaků.';
    }
    if (mb_strlen($password) < 8 || strlen($password) > 72 || str_contains($password, "\0")) {
        $errors[] = 'Heslo musí mít alespoň 8 znaků a nesmí být příliš dlouhé.';
    }
    if ($password !== $password2) {
        $errors[] = 'Hesla se musí shodovat.';
    }
    foreach (['firstname' => ['Jméno', 50], 'surname' => ['Příjmení', 50], 'street' => ['Ulice a č.p.', 150], 'city' => ['Město', 100]] as $field => [$label, $maxLength]) {
        if ($values[$field] === '' || mb_strlen($values[$field]) > $maxLength) {
            $errors[] = $label . ': vyplňte nejvýše ' . $maxLength . ' znaků.';
        }
    }
    if (!preg_match('/^[0-9]{5}$/D', $values['zip'])) {
        $errors[] = 'PSČ musí obsahovat právě pět číslic.';
    }
    $regionId = filter_var($values['region'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (!in_array($regionId, array_map('intval', array_column($regions, 'id')), true)) {
        $errors[] = 'Vyberte kraj ze seznamu.';
    }
    if (!in_array($values['gender'], ['muz', 'zena'], true)) {
        $errors[] = 'Vyberte pohlaví.';
    }
    if (mb_strlen($values['note']) > 2000) {
        $errors[] = 'Poznámka může mít nejvýše 2 000 znaků.';
    }

    if ($errors === []) {
        try {
            $statement = $connection->prepare(
                'INSERT INTO students (name, surname, email, password, street, city, zip, region_id, gender, note)
                 VALUES (:name, :surname, :email, :password, :street, :city, :zip, :region_id, :gender, :note)'
            );
            $statement->execute([
                'name' => $values['firstname'],
                'surname' => $values['surname'],
                'email' => $values['email'],
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'street' => $values['street'],
                'city' => $values['city'],
                'zip' => $values['zip'],
                'region_id' => $regionId,
                'gender' => $values['gender'],
                'note' => $values['note'],
            ]);
            $_SESSION['created_student_id'] = (int) $connection->lastInsertId();
            header('Location: prehled-studentu.php', true, 303);
            exit;
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                $errors[] = 'Student s tímto e-mailem už existuje.';
            } else {
                error_log($e->getMessage());
                http_response_code(503);
                $errors[] = 'Studenta se nepodařilo uložit. Zkuste to prosím později.';
            }
        }
    }

    if ($errors !== [] && http_response_code() !== 503) {
        http_response_code(422);
    }
}

$pageTitle = 'Registrace studenta';
require __DIR__ . '/html/ukol3Head.php';
?>
<div class="mx-auto" style="max-width: 900px;">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <p class="text-muted mb-1">Evidence studentů</p>
            <h1 class="h2">Registrace studenta</h1>
        </div>
        <a href="prehled-studentu.php" class="btn btn-outline-primary my-2">Přehled studentů</a>
    </div>

    <?php if ($errors !== []): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= $escape($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($connection !== null && $regions !== []): ?>
        <form method="post" action="form.php" class="card shadow-sm">
            <div class="card-body p-md-4">
                <input type="hidden" name="csrf_token" value="<?= $escape($_SESSION['csrf_token']) ?>">
                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" class="form-control" name="email" maxlength="254" autocomplete="email" value="<?= $escape($values['email']) ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="password">Heslo</label>
                        <input type="password" id="password" class="form-control" name="password" minlength="8" maxlength="72" autocomplete="new-password" required>
                        <small class="form-text text-muted">Alespoň 8 znaků.</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="password2">Potvrzení hesla</label>
                        <input type="password" id="password2" class="form-control" name="password2" minlength="8" maxlength="72" autocomplete="new-password" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="firstname">Jméno</label>
                        <input type="text" id="firstname" class="form-control" name="firstname" maxlength="50" autocomplete="given-name" value="<?= $escape($values['firstname']) ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="surname">Příjmení</label>
                        <input type="text" id="surname" class="form-control" name="surname" maxlength="50" autocomplete="family-name" value="<?= $escape($values['surname']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="street">Ulice a č.p.</label>
                    <input type="text" id="street" class="form-control" name="street" maxlength="150" autocomplete="street-address" value="<?= $escape($values['street']) ?>" required>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-8">
                        <label for="city">Město</label>
                        <input type="text" id="city" class="form-control" name="city" maxlength="100" autocomplete="address-level2" value="<?= $escape($values['city']) ?>" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="zip">PSČ</label>
                        <input type="text" id="zip" class="form-control" name="zip" maxlength="6" inputmode="numeric" pattern="[0-9]{3} ?[0-9]{2}" autocomplete="postal-code" value="<?= $escape($values['zip']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="region">Kraj</label>
                    <select id="region" class="form-control" name="region" autocomplete="address-level1" required>
                        <option value="">-- Vyberte kraj --</option>
                        <?php foreach ($regions as $region): ?>
                            <option value="<?= (int) $region['id'] ?>" <?= $values['region'] === (string) $region['id'] ? 'selected' : '' ?>><?= $escape($region['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <fieldset class="form-group">
                    <legend class="col-form-label pt-0">Pohlaví</legend>
                    <?php foreach (['muz' => 'Muž', 'zena' => 'Žena'] as $gender => $label): ?>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="gender-<?= $gender ?>" class="form-check-input" name="gender" value="<?= $gender ?>" <?= $values['gender'] === $gender ? 'checked' : '' ?> required>
                            <label class="form-check-label" for="gender-<?= $gender ?>"><?= $label ?></label>
                        </div>
                    <?php endforeach; ?>
                </fieldset>
                <div class="form-group">
                    <label for="note">Poznámka <span class="text-muted">(nepovinné)</span></label>
                    <textarea id="note" class="form-control" name="note" rows="3" maxlength="2000"><?= $escape($values['note']) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary" name="submit" value="1">Uložit studenta</button>
            </div>
        </form>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/html/ukol3Foot.php'; ?>
