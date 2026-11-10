<?php declare(strict_types=1);

use App\Model\PostFacade;
use Nette\Caching\Storages\MemoryStorage;
use Nette\Database;
use Tester\Assert;

require __DIR__ . '/../vendor/autoload.php';
Tester\Environment::setup();

$connection = new Database\Connection('sqlite::memory:');
$structure = new Database\Structure($connection, new MemoryStorage);
$database = new Database\Explorer($connection, $structure);
$connection->query('CREATE TABLE posts (id INTEGER PRIMARY KEY, title TEXT NOT NULL, content TEXT NOT NULL, created_at DATETIME NOT NULL)');
$facade = new PostFacade($database);

Assert::same([], $facade->getPublicArticles()->fetchAll());

for ($i = 1; $i <= 7; $i++) {
	$database->table('posts')->insert([
		'id' => $i,
		'title' => 'Publikovaný článek ' . $i,
		'content' => 'Text článku',
		'created_at' => new DateTimeImmutable('-' . $i . ' days'),
	]);
}
$database->table('posts')->insert([
	'id' => 8,
	'title' => 'Budoucí článek',
	'content' => 'Tento článek se ještě nemá zobrazit.',
	'created_at' => new DateTimeImmutable('+1 day'),
]);

Assert::count(7, $facade->getPublicArticles()->fetchAll());
Assert::same([
	1 => 'Publikovaný článek 1',
	2 => 'Publikovaný článek 2',
	3 => 'Publikovaný článek 3',
	4 => 'Publikovaný článek 4',
	5 => 'Publikovaný článek 5',
], $facade->getPublicArticles()->limit(5)->fetchPairs('id', 'title'));
