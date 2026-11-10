<?php declare(strict_types=1);

namespace App\Model;

use Nette\Database\Connection;
use Nette\Security\{Authenticator, AuthenticationException, IIdentity, Passwords, SimpleIdentity};

final class AccountAuthenticator implements Authenticator
{
    public function __construct(private Connection $database, private Passwords $passwords)
    {
    }

    public function authenticate(string $username, string $password): IIdentity
    {
        [$role, $code] = array_pad(explode(':', $username, 2), 2, '');
        $account = match ($role) {
            'student' => $this->database->fetch('SELECT * FROM studenti WHERE kod_studenta = ?', $code),
            'pedagog' => $this->database->fetch('SELECT * FROM pedagogove WHERE kod_pedagoga = ?', $code),
            default => null,
        };
        if (!$account || !$this->passwords->verify($password, $account->heslo)) {
            throw new AuthenticationException('Zkontrolujte typ účtu, přihlašovací jméno a heslo.');
        }

        return new SimpleIdentity($role . ':' . $code, [$role], [
            'code' => $code,
            'name' => $account->jmeno . ' ' . $account->prijmeni,
        ]);
    }
}
