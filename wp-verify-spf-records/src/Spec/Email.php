<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer\Spec;

use InvalidArgumentException;
use Nzsys\EnvelopFixer\Core\HostnameInterface;

final class Email implements HostnameInterface
{
    private string $domain;

    public function __construct(
        private readonly string $email,
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("This ({$email}) email address is considered invalid.");
        }
        $this->domain = substr((string) strrchr($email, "@"), 1);
        if (!checkdnsrr($this->domain, 'MX')) {
            throw new InvalidArgumentException("The email ({$email}) is valid, but it doesn't have valid MX records on its domain.");
        }
    }

    public function email(): string
    {
        return $this->email;
    }

    public function domain(): string
    {
        return $this->domain;
    }
}
