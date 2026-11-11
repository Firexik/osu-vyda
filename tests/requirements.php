<?php declare(strict_types=1);

// Run against a local test server. A unique database is created and removed.
require dirname(__DIR__) . '/vendor/autoload.php';
date_default_timezone_set('Europe/Prague');

use App\Model\AccountAuthenticator;
use App\Model\TermFacade;
use Nette\Database\Connection;
use Nette\Security\Passwords;

if (($argv[1] ?? '') === '--enroll') {
    $db = new Connection(getenv('VYDA_TEST_DSN'), getenv('VYDA_TEST_USER'), getenv('VYDA_TEST_PASSWORD') ?: '');
    while (microtime(true) < (float) $argv[4]) { usleep(1000); }
    try {
        (new TermFacade($db))->enroll((int) $argv[2], $argv[3]);
        echo 'enrolled';
    } catch (RuntimeException $e) {
        if (get_class($e) !== RuntimeException::class) { throw $e; }
        echo 'rejected: ' . $e->getMessage();
    }
    exit;
}

$root = dirname(__DIR__);
$port = (int) (getenv('VYDA_TEST_PORT') ?: 33077);
$user = getenv('VYDA_TEST_USER') ?: 'root';
$password = getenv('VYDA_TEST_PASSWORD') ?: '';
$database = 'vyda_test_' . bin2hex(random_bytes(6));
$admin = new PDO("mysql:host=127.0.0.1;port=$port;charset=utf8mb4", $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$admin->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci");
$dsn = "mysql:host=127.0.0.1;port=$port;dbname=$database;charset=utf8mb4";
$db = new Connection($dsn, $user, $password);
$pdo = $db->getPdo();
$runtime = sys_get_temp_dir() . '/vyda_http_' . bin2hex(random_bytes(6));
$server = null;
$checks = 0;

function check(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) { throw new RuntimeException($message); }
    $checks++;
    echo "OK $checks: $message\n";
}

function rejected(callable $action, string $message): void
{
    try { $action(); } catch (Exception $e) {
        if (get_class($e) !== RuntimeException::class && !$e instanceof Nette\Security\AuthenticationException) { throw $e; }
        check(true, $message); return;
    }
    throw new RuntimeException('Expected rejection: ' . $message);
}

function seed(): void
{
    global $pdo, $root;
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN) as $table) {
        $pdo->exec("TRUNCATE TABLE `$table`");
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    $pdo->exec(file_get_contents($root . '/database/data.sql'));
}

function copyTree(string $from, string $to): void
{
    if (!is_dir($to)) { mkdir($to, 0770, true); }
    foreach (new DirectoryIterator($from) as $file) {
        if ($file->isDot()) { continue; }
        $target = $to . '/' . $file->getFilename();
        $file->isDir() ? copyTree($file->getPathname(), $target) : copy($file->getPathname(), $target);
    }
}

function request(string $url, string $cookie, ?array $post = null): array
{
    $curl = curl_init($url);
    curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => false, CURLOPT_COOKIEJAR => $cookie, CURLOPT_COOKIEFILE => $cookie,
        CURLOPT_TIMEOUT => 10]);
    if ($post !== null) { curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post)); }
    $response = curl_exec($curl);
    if ($response === false) { throw new RuntimeException(curl_error($curl)); }
    $status = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
    unset($curl);
    return ['status' => $status, 'headers' => substr($response, 0, $size), 'body' => substr($response, $size)];
}

function fields(string $html, string $id): array
{
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
    $xpath = new DOMXPath($dom);
    $data = [];
    foreach ($xpath->query('//form[@id="' . $id . '"]//input') as $input) {
        if ($input->getAttribute('name') !== '') { $data[$input->getAttribute('name')] = $input->getAttribute('value'); }
    }
    if (!$data) { throw new RuntimeException("Form $id missing"); }
    return $data;
}

function login(string $base, string $cookie, string $role, string $name): void
{
    $page = request($base . '/prihlaseni', $cookie);
    $data = fields($page['body'], 'frm-signInForm');
    $data = array_replace($data, ['role' => $role, 'username' => $name, 'password' => 'Vyda2026!']);
    $result = request($base . '/prihlaseni', $cookie, $data);
    if ($result['status'] !== 303) { file_put_contents($cookie . '.failure.html', $result['body']); }
    check($result['status'] === 303, "HTTP login $role $name (status {$result['status']})");
}

function race(array $attempts): array
{
    global $dsn, $user, $password;
    $environment = array_merge(getenv(), ['VYDA_TEST_DSN' => $dsn, 'VYDA_TEST_USER' => $user, 'VYDA_TEST_PASSWORD' => $password]);
    $start = (string) (microtime(true) + 0.6);
    $workers = [];
    foreach ($attempts as [$id, $student]) {
        $pipes = [];
        $process = proc_open([PHP_BINARY, __FILE__, '--enroll', (string) $id, $student, $start],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, null, $environment);
        fclose($pipes[0]);
        $workers[] = [$process, $pipes];
    }
    $results = [];
    foreach ($workers as [$process, $pipes]) {
        $results[] = stream_get_contents($pipes[1]);
        $errors = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        $exitCode = proc_close($process);
        if ($exitCode !== 0) { throw new RuntimeException('Worker exit ' . $exitCode . ': ' . $errors . ' output: ' . end($results)); }
    }
    return $results;
}

try {
    $pdo->exec(file_get_contents($root . '/database/schema.sql'));
    seed();
    $terms = new TermFacade($db);
    $auth = new AccountAuthenticator($db, new Passwords);
    foreach (['student' => 'st', 'pedagog' => 'p'] as $role => $prefix) {
        for ($i = 1; $i <= 5; $i++) {
            $code = $prefix . sprintf('%03d', $i);
            check($auth->authenticate("$role:$code", 'Vyda2026!')->getRoles() === [$role], "Functional account $role $code");
            check(count($terms->getCourses($role, $code)) > 0, "Assigned courses $code");
            check(count($terms->getTerms($role, $code)) > 0, "Accessible terms $code");
        }
    }
    rejected(fn() => $auth->authenticate('student:st001', 'wrong'), 'Wrong password rejected');
    check(count($terms->getCourses('pedagog', 'p001')) === 2, 'Teacher sees only assigned courses');
    rejected(fn() => $terms->getTerm(7, 'student', 'st001'), 'Unassigned student cannot open term');
    rejected(fn() => $terms->getTerm(7, 'pedagog', 'p001'), 'Unassigned teacher cannot open term');
    rejected(fn() => $terms->enroll(1, 'st001'), 'Duplicate enrollment rejected');
    rejected(fn() => $terms->enroll(2, 'st001'), 'Second valid term of same course rejected');
    $terms->enroll(3, 'st001');
    check((int) $db->fetchField('SELECT COUNT(*) FROM zapsane_terminy WHERE id_terminu=3') === 2, 'Last available place filled');
    rejected(fn() => $terms->enroll(3, 'st003'), 'Unassigned course rejected');
    $terms->cancel(3, 'st001');
    check(!(bool) $db->fetchField('SELECT 1 FROM zapsane_terminy WHERE id_terminu=3 AND kod_studenta=?', 'st001'), 'Student can cancel');
    rejected(fn() => $terms->cancel(3, 'st001'), 'Cannot cancel absent registration');
    rejected(fn() => $terms->enroll(9, 'st004'), 'Past term enrollment rejected');
    rejected(fn() => $terms->cancel(9, 'st001'), 'Past term cancellation rejected');

    $data = ['zkratka_predmetu' => 'ALG2', 'zkratka_mistnosti' => 'A101',
        'datum_cas' => new DateTimeImmutable('+20 days'), 'max_pocet_prihlasenych' => 2, 'poznamka' => 'Integration test'];
    $id = $terms->save(null, 'p001', $data);
    check($terms->getTerm($id, 'pedagog', 'p001')->poznamka === 'Integration test', 'Teacher creates future term');
    $terms->save($id, 'p001', array_replace($data, ['poznamka' => 'Edited']));
    check($terms->getTerm($id, 'pedagog', 'p001')->poznamka === 'Edited', 'Teacher edits term');
    rejected(fn() => $terms->save(null, 'p003', $data), 'Teacher cannot create unassigned subject');
    rejected(fn() => $terms->save(null, 'p001', array_replace($data, ['datum_cas' => new DateTimeImmutable('-1 day')])), 'Cannot create past term');
    rejected(fn() => $terms->save(null, 'p001', array_replace($data, ['max_pocet_prihlasenych' => 25])), 'Room capacity respected');
    rejected(fn() => $terms->save(1, 'p001', array_replace($data, ['zkratka_predmetu' => 'VYDA', 'max_pocet_prihlasenych' => 2])), 'Cannot reduce capacity below occupancy');
    rejected(fn() => $terms->save(1, 'p001', $data), 'Cannot change occupied term subject');
    rejected(fn() => $terms->save(9, 'p001', array_replace($data, ['zkratka_predmetu' => 'VYDA'])), 'Cannot move past term into future');
    rejected(fn() => $terms->delete(7, 'p001'), 'Teacher cannot delete unassigned term');
    rejected(fn() => $terms->delete(9, 'p001'), 'Past term delete rejected');
    $terms->enroll($id, 'st001');
    $terms->delete($id, 'p001');
    check(!(bool) $db->fetchField('SELECT 1 FROM zapsane_terminy WHERE id_terminu=?', $id), 'Deleting term removes registrations');

    seed();
    $db->query('INSERT INTO studenti_predmety', ['kod_studenta' => 'st003', 'zkratka_predmetu' => 'ALG2']);
    $results = race([[3, 'st001'], [3, 'st003']]);
    check(count(array_filter($results, fn($r) => $r === 'enrolled')) === 1, 'Concurrent last-place enrollment has one winner');
    check(count(array_filter($results, fn($r) => str_contains($r, 'plně obsazen'))) === 1, 'Capacity loser gets domain rejection');
    check((int) $db->fetchField('SELECT COUNT(*) FROM zapsane_terminy WHERE id_terminu=3') === 2, 'Concurrent capacity never exceeded');
    seed();
    $results = race([[1, 'st004'], [2, 'st004']]);
    check(count(array_filter($results, fn($r) => $r === 'enrolled')) === 1, 'Concurrent same-course enrollment has one winner');
    check(count(array_filter($results, fn($r) => str_contains($r, 'jiný platný termín'))) === 1, 'Same-course loser gets domain rejection');
    check((int) $db->fetchField('SELECT COUNT(*) FROM zapsane_terminy WHERE kod_studenta=?', 'st004') === 2, 'One new valid registration for student');

    seed();
    mkdir($runtime, 0770, true);
    foreach (['app', 'www'] as $directory) { copyTree($root . '/' . $directory, $runtime . '/' . $directory); }
    mkdir($runtime . '/config'); mkdir($runtime . '/vendor');
    copy($root . '/config/common.neon', $runtime . '/config/common.neon');
    file_put_contents($runtime . '/config/local.neon', "database:\n    dsn: " . json_encode($dsn) . "\n    user: " . json_encode($user) . "\n    password: " . json_encode($password) . "\n");
    file_put_contents($runtime . '/vendor/autoload.php', '<?php $loader = require ' . var_export($root . '/vendor/autoload.php', true) . '; $loader->addPsr4("App\\\\", ' . var_export($runtime . '/app', true) . ', true);');
    $socket = stream_socket_server('tcp://127.0.0.1:0');
    $address = stream_socket_get_name($socket, false); fclose($socket);
    $base = 'http://' . $address;
    $server = proc_open([PHP_BINARY, '-S', $address, '-t', $runtime . '/www', $runtime . '/www/router.php'],
        [0 => ['file', PHP_OS_FAMILY === 'Windows' ? 'NUL' : '/dev/null', 'r'], 1 => ['file', $runtime . '/server.log', 'a'], 2 => ['file', $runtime . '/server.log', 'a']], $pipes, $runtime);
    $ready = false;
    for ($i = 0; $i < 50; $i++) {
        $probe = @stream_socket_client('tcp://' . $address, $errno, $error, 0.1);
        if ($probe) { fclose($probe); $ready = true; break; }
        usleep(100000);
    }
    check($ready, 'Isolated HTTP server started');
    $studentCookie = $runtime . '/student.cookie'; $teacherCookie = $runtime . '/teacher.cookie';
    foreach (['/', '/predmety', '/terminy', '/terminy/1', '/terminy/novy', '/terminy/1/upravit', '/terminy/1/smazat'] as $route) {
        $page = request($base . $route, $runtime . '/guest.cookie');
        check($page['status'] === 302 && str_contains($page['headers'], '/prihlaseni'), "Guest blocked $route");
    }
    login($base, $studentCookie, 'student', 'st001');
    check(str_contains(request($base . '/predmety', $studentCookie)['body'], 'Webové a databázové aplikace'), 'Student course page renders');
    $studentTerms = request($base . '/terminy', $studentCookie);
    check($studentTerms['status'] === 200 && !preg_match('~href="[^"]*/terminy/(9|10)(?:["?#])~', $studentTerms['body']), 'Student term list renders');
    foreach (['/terminy/novy', '/terminy/1/upravit', '/terminy/1/smazat'] as $route) {
        check(request($base . $route, $studentCookie)['status'] === 403, "Student cannot manage terms $route");
    }
    check(request($base . '/terminy/7', $studentCookie)['status'] === 404, 'HTTP unassigned course blocked');
    $detail = request($base . '/terminy/3', $studentCookie);
    $form = fields($detail['body'], 'frm-registrationForm');
    $enroll = request($base . '/terminy/3', $studentCookie, $form);
    check($enroll['status'] === 303, 'HTTP enrollment succeeds');
    $detail = request($base . '/terminy/3', $studentCookie);
    $cancel = request($base . '/terminy/3', $studentCookie, fields($detail['body'], 'frm-registrationForm'));
    check($cancel['status'] === 303, 'HTTP cancellation succeeds');
    login($base, $teacherCookie, 'pedagog', 'p001');
    $page = request($base . '/terminy/novy', $teacherCookie);
    $form = array_replace(fields($page['body'], 'frm-termForm'), ['zkratka_predmetu' => 'ALG2', 'zkratka_mistnosti' => 'A101',
        'datum_cas' => (new DateTimeImmutable('+25 days'))->format('Y-m-d\TH:i'), 'max_pocet_prihlasenych' => 5, 'poznamka' => 'HTTP created']);
    $created = request($base . '/terminy/novy', $teacherCookie, $form);
    check($created['status'] === 303, 'HTTP create term succeeds');
    $newId = (int) $db->fetchField('SELECT id_terminu FROM vypsane_terminy WHERE poznamka=?', 'HTTP created');
    check($newId > 0, 'HTTP created term persisted');
    $edit = request($base . "/terminy/$newId/upravit", $teacherCookie);
    $form = array_replace(fields($edit['body'], 'frm-termForm'), ['zkratka_predmetu' => 'ALG2', 'zkratka_mistnosti' => 'A101',
        'datum_cas' => (new DateTimeImmutable('+26 days'))->format('Y-m-d\TH:i'), 'max_pocet_prihlasenych' => 6, 'poznamka' => 'HTTP edited']);
    check(request($base . "/terminy/$newId/upravit", $teacherCookie, $form)['status'] === 303, 'HTTP edit term succeeds');
    check($db->fetchField('SELECT poznamka FROM vypsane_terminy WHERE id_terminu=?', $newId) === 'HTTP edited', 'HTTP edit persisted');
    $delete = request($base . "/terminy/$newId/smazat", $teacherCookie);
    check(request($base . "/terminy/$newId/smazat", $teacherCookie, fields($delete['body'], 'frm-deleteForm'))['status'] === 303, 'HTTP delete succeeds');
    check(!(bool) $db->fetchField('SELECT 1 FROM vypsane_terminy WHERE id_terminu=?', $newId), 'HTTP deletion persisted');
    echo "PASS: $checks checks; PHP " . PHP_VERSION . '; database ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    $db->disconnect(); $pdo = null;
    $admin->exec("DROP DATABASE `$database`");
    // Leave HTTP logs in the OS temporary directory for diagnosis; never touch application data.
    if (is_dir($runtime)) { echo "HTTP logs: $runtime\n"; }
}
