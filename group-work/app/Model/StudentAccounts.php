<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Connection;
use Nette\Security\Authenticator;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

final class StudentAccounts implements Authenticator
{
    public function __construct(private Connection $database, private Passwords $passwords)
    {
    }

    public function authenticate(string $user, string $password): IIdentity
    {
        $student = $this->database->fetch(
            'SELECT kod_studenta, jmeno, prijmeni, email, password FROM studenti WHERE email = ?',
            $user,
        );

        if (!is_string($student?->password) || !$this->passwords->verify($password, $student->password)) {
            throw new AuthenticationException;
        }

        return new SimpleIdentity($student->kod_studenta, 'student', [
            'name' => $student->jmeno . ' ' . $student->prijmeni,
            'email' => $student->email,
        ]);
    }

    public function changePassword(string $studentId, string $oldPassword, string $newPassword): bool
    {
        $hash = $this->database->fetchField('SELECT password FROM studenti WHERE kod_studenta = ?', $studentId);
        if (!is_string($hash) || !$this->passwords->verify($oldPassword, $hash)) {
            return false;
        }

        return $this->database->query(
            'UPDATE studenti SET password = ? WHERE kod_studenta = ? AND password = ?',
            $this->passwords->hash($newPassword),
            $studentId,
            $hash,
        )->getRowCount() === 1;
    }
}
