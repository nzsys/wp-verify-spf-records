<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer\Spec;

use InvalidArgumentException;
use Nzsys\EnvelopFixer\Core\HostnameInterface;

final class FQDN implements HostnameInterface
{
    public function __construct(
        private readonly string $domain,
    ) {
        if (!checkdnsrr($domain, 'MX')) {
            throw new InvalidArgumentException("The domain ({$domain}) is valid, but it doesn't have valid MX records on its domain.");
        }
    }

    public function domain(): string
    {
        return $this->domain;
    }
}
