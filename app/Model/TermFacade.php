<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Connection;
use Nette\Database\Row;

final class TermFacade
{
    public function __construct(private Connection $database)
    {
    }

    public function getCourses(string $role, string $code): array
    {
        if ($role === 'pedagog') {
            return $this->database->fetchAll('SELECT p.* FROM predmety p
                JOIN pedagogove_predmety pp USING (zkratka_predmetu)
                WHERE pp.kod_pedagoga = ? ORDER BY p.zkratka_predmetu', $code);
        }
        return $this->database->fetchAll('SELECT p.* FROM predmety p
            JOIN studenti_predmety sp USING (zkratka_predmetu)
            WHERE sp.kod_studenta = ? ORDER BY p.zkratka_predmetu', $code);
    }

    public function getTerms(string $role, string $code): array
    {
        $sql = 'SELECT t.*, p.nazev, p.ukonceni, CONCAT(pe.jmeno, " ", pe.prijmeni) AS pedagog,
            (SELECT COUNT(*) FROM zapsane_terminy z WHERE z.id_terminu = t.id_terminu) AS obsazeno,
            (SELECT COUNT(*) FROM zapsane_terminy z WHERE z.id_terminu = t.id_terminu AND z.kod_studenta = ?) AS zapsan
            FROM vypsane_terminy t JOIN predmety p USING (zkratka_predmetu)
            JOIN pedagogove pe USING (kod_pedagoga)';
        if ($role === 'pedagog') {
            return $this->database->fetchAll($sql . '
                JOIN pedagogove_predmety pp ON pp.zkratka_predmetu = t.zkratka_predmetu
                WHERE pp.kod_pedagoga = ? ORDER BY t.datum_cas', '', $code);
        }
        return $this->database->fetchAll($sql . '
            JOIN studenti_predmety sp ON sp.zkratka_predmetu = t.zkratka_predmetu
            WHERE sp.kod_studenta = ? ORDER BY t.datum_cas', $code, $code);
    }

    public function getTerm(int $id, string $role, string $code): Row
    {
        foreach ($this->getTerms($role, $code) as $term) {
            if ($term->id_terminu === $id) {
                return $term;
            }
        }
        throw new \RuntimeException('Termín neexistuje nebo k němu nemáte přístup.');
    }

    public function getRooms(): array
    {
        return $this->database->fetchPairs('SELECT zkratka_mistnosti,
            CONCAT(zkratka_mistnosti, " (", kapacita, " míst)") FROM mistnosti ORDER BY zkratka_mistnosti');
    }

    public function enroll(int $id, string $student): void
    {
        $this->database->transaction(function () use ($id, $student): void {
            // Zámek studenta brání souběžnému zápisu dvou termínů stejného předmětu.
            $this->database->fetch('SELECT kod_studenta FROM studenti WHERE kod_studenta = ? FOR UPDATE', $student);
            $term = $this->lockTerm($id);
            $this->checkSubject('student', $student, $term->zkratka_predmetu);
            $this->checkFuture($term->datum_cas);
            if ($this->database->fetch('SELECT 1 FROM zapsane_terminy WHERE id_terminu = ? AND kod_studenta = ?', $id, $student)) {
                throw new \RuntimeException('Na tento termín už jste přihlášeni.');
            }
            if ($this->database->fetch('SELECT 1 FROM zapsane_terminy z JOIN vypsane_terminy t USING (id_terminu)
                WHERE z.kod_studenta = ? AND t.zkratka_predmetu = ? AND t.datum_cas > ?', $student, $term->zkratka_predmetu, new \DateTimeImmutable)) {
                throw new \RuntimeException('Z tohoto předmětu už máte zapsaný jiný platný termín.');
            }
            if ($this->countStudents($id) >= $term->max_pocet_prihlasenych) {
                throw new \RuntimeException('Termín je již plně obsazen.');
            }
            $this->database->query('INSERT INTO zapsane_terminy', ['id_terminu' => $id, 'kod_studenta' => $student]);
        });
    }

    public function cancel(int $id, string $student): void
    {
        $this->database->transaction(function () use ($id, $student): void {
            $this->database->fetch('SELECT kod_studenta FROM studenti WHERE kod_studenta = ? FOR UPDATE', $student);
            $term = $this->lockTerm($id);
            $this->checkSubject('student', $student, $term->zkratka_predmetu);
            $this->checkFuture($term->datum_cas);
            $result = $this->database->query('DELETE FROM zapsane_terminy WHERE id_terminu = ? AND kod_studenta = ?', $id, $student);
            if ($result->getRowCount() === 0) {
                throw new \RuntimeException('Na tento termín nejste přihlášeni.');
            }
        });
    }

    public function save(?int $id, string $teacher, array $data): int
    {
        return $this->database->transaction(function () use ($id, $teacher, $data): int {
            // Před kontrolou obsazenosti zamkneme termín i při úpravě.
            $old = $id !== null ? $this->lockTerm($id) : null;
            $this->checkSubject('pedagog', $teacher, $data['zkratka_predmetu']);
            $this->checkFuture($data['datum_cas']);
            $room = $this->database->fetch('SELECT * FROM mistnosti WHERE zkratka_mistnosti = ?', $data['zkratka_mistnosti']);
            if (!$room || $data['max_pocet_prihlasenych'] < 1 || $data['max_pocet_prihlasenych'] > $room->kapacita) {
                throw new \RuntimeException('Kapacita musí být alespoň 1 a nesmí překročit kapacitu místnosti.');
            }
            if ($old) {
                $this->checkSubject('pedagog', $teacher, $old->zkratka_predmetu);
                $this->checkFuture($old->datum_cas);
                if ($data['max_pocet_prihlasenych'] < $this->countStudents($id)) {
                    throw new \RuntimeException('Kapacita nesmí být nižší než počet přihlášených studentů.');
                }
                if ($old->zkratka_predmetu !== $data['zkratka_predmetu'] && $this->countStudents($id) > 0) {
                    throw new \RuntimeException('U obsazeného termínu nelze změnit předmět.');
                }
                $this->database->query('UPDATE vypsane_terminy SET', $data + ['kod_pedagoga' => $teacher], 'WHERE id_terminu = ?', $id);
                return $id;
            }
            $this->database->query('INSERT INTO vypsane_terminy', $data + ['kod_pedagoga' => $teacher]);
            return (int) $this->database->getInsertId();
        });
    }

    public function delete(int $id, string $teacher): void
    {
        $this->database->transaction(function () use ($id, $teacher): void {
            $term = $this->lockTerm($id);
            $this->checkSubject('pedagog', $teacher, $term->zkratka_predmetu);
            $this->checkFuture($term->datum_cas);
            $this->database->query('DELETE FROM vypsane_terminy WHERE id_terminu = ?', $id);
        });
    }

    private function lockTerm(int $id): Row
    {
        // Zámek termínu zajistí, že poslední volné místo získá jen jeden student.
        $term = $this->database->fetch('SELECT * FROM vypsane_terminy WHERE id_terminu = ? FOR UPDATE', $id);
        if (!$term) {
            throw new \RuntimeException('Termín už neexistuje.');
        }
        return $term;
    }

    private function countStudents(int $id): int
    {
        return (int) $this->database->fetchField('SELECT COUNT(*) FROM zapsane_terminy WHERE id_terminu = ?', $id);
    }

    private function checkSubject(string $role, string $code, string $subject): void
    {
        $allowed = $role === 'pedagog'
            ? $this->database->fetch('SELECT 1 FROM pedagogove_predmety WHERE kod_pedagoga = ? AND zkratka_predmetu = ?', $code, $subject)
            : $this->database->fetch('SELECT 1 FROM studenti_predmety WHERE kod_studenta = ? AND zkratka_predmetu = ?', $code, $subject);
        if (!$allowed) {
            throw new \RuntimeException('K tomuto předmětu nemáte přístup.');
        }
    }

    private function checkFuture(\DateTimeInterface $date): void
    {
        if ($date <= new \DateTimeImmutable) {
            throw new \RuntimeException('Termín musí být v budoucnosti. Proběhlý termín už nelze měnit ani zapisovat.');
        }
    }
}
