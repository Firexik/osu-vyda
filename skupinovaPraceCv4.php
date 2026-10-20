<?php

declare(strict_types=1);

function get_post_value(string $name): string {
    if (isset($_POST[$name]) && is_string($_POST[$name]))
        return trim($_POST[$name]);
    return '';
}

function escape(string $text): string {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$allowedTopics = ['web', 'mobile', 'data', 'other'];
$topicNames = [
    'web' => 'Webová aplikace',
    'mobile' => 'Mobilní aplikace',
    'data' => 'Práce s daty',
    'other' => 'Jiné',
];

$teamName = get_post_value('team_name');
$members = [
    get_post_value('member1'),
    get_post_value('member2'),
    get_post_value('member3'),
];
$projectName = get_post_value('project_name');
$topic = get_post_value('topic');
$description = get_post_value('description');

$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    if ($teamName === '')  $errors[] = 'Vyplňte název týmu.';
    elseif (strlen($teamName) > 40) $errors[] = 'Název týmu může mít nejvýše 40 znaků.';

    foreach ($members as $index => $member)
        if ($member === '')
            $errors[] = 'Vyplňte jméno člena ' . ($index + 1) . '.';

    if ($projectName === '')
        $errors[] = 'Vyplňte název projektu.';

    if (!in_array($topic, $allowedTopics, true))
        $errors[] = 'Vyberte téma projektu ze seznamu.';

    if (strlen($description) < 20)
        $errors[] = 'Popis musí mít alespoň 20 znaků.';
}

$accepted = $submitted && count($errors) === 0;

$wordCount = 0;
if ($accepted) {
    $words = explode(' ', $description);
    foreach ($words as $word)
        if ($word !== '')
            $wordCount++;
}
?>
<!DOCTYPE html>
<html lang="cs">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Přihláška týmu na minikonferenci</title>
    <link rel="stylesheet" href="./bootstrap/css/bootstrap.css">
</head>

<body>
    <div class="container py-4">
        <h1>Přihláška týmu na minikonferenci</h1>

        <?php if ($accepted) { ?>
            <div class="alert alert-success">Přihláška je přijatá.</div>

            <div class="card mb-3">
                <div class="card-header"><?= escape($teamName) ?></div>
                <div class="card-body">
                    <p><strong>Projekt:</strong> <?= escape($projectName) ?></p>
                    <p><strong>Téma:</strong> <?= escape($topicNames[$topic]) ?></p>
                    <p><strong>Členové:</strong> <?= escape(implode(', ', $members)) ?></p>
                    <p><?= nl2br(escape($description)) ?></p>
                    <p>Délka popisu: <?= strlen($description) ?> znaků, <?= $wordCount ?> slov.</p>
                    <p>Odesláno: <?= date('d.m.Y H:i') ?></p>

                    <?php if (str_contains($description, 'PHP') || str_contains($description, 'php')) { ?>
                        <p>V popisu je PHP. Na konferenci bude stát za zmínku, kterou část PHP použijete.</p>
                    <?php } ?>
                </div>
            </div>

            <a href="skupinovaPraceCv4.php">Nová přihláška</a>
        <?php } else { ?>
            <?php if (count($errors) > 0) { ?>
                <div class="alert alert-danger">
                    <p>Přihlášku teď nejde přijmout:</p>
                    <ul>
                        <?php foreach ($errors as $error) { ?>
                            <li><?= escape($error) ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>

            <form method="post" action="skupinovaPraceCv4.php" novalidate>
                <div class="form-group">
                    <label for="team_name">Název týmu</label>
                    <input type="text" class="form-control" id="team_name" name="team_name" value="<?= escape($teamName) ?>" required>
                </div>

                <div class="form-row">
                    <?php foreach ($members as $index => $member) { ?>
                        <div class="form-group col-md-4">
                            <label for="member<?= $index + 1 ?>">Člen <?= $index + 1 ?></label>
                            <input type="text" class="form-control" id="member<?= $index + 1 ?>" name="member<?= $index + 1 ?>" value="<?= escape($member) ?>" required>
                        </div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="project_name">Název projektu</label>
                    <input type="text" class="form-control" id="project_name" name="project_name" value="<?= escape($projectName) ?>" required>
                </div>

                <div class="form-group">
                    <label for="topic">Téma</label>
                    <select class="form-control" id="topic" name="topic" required>
                        <option value="">— vyberte —</option>
                        <?php foreach ($topicNames as $value => $label) { ?>
                            <option value="<?= escape($value) ?>" <?= $topic === $value ? ' selected' : '' ?>><?= escape($label) ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Popis</label>
                    <textarea class="form-control" id="description" name="description" rows="5" minlength="20" required><?= escape($description) ?></textarea>
                </div>

                <button type="submit" class="btn btn-success">Odeslat přihlášku</button>
            </form>
        <?php } ?>
    </div>
</body>

</html>