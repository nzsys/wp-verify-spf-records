<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer\App;

use InvalidArgumentException;
use Nzsys\EnvelopFixer\Core\HostnameInterface;

final class IpConverter
{
    private string $ipAddress;

    public function __construct(
        private readonly HostnameInterface $hostname,
    ) {
        $this->ipAddress = gethostbyname($this->hostname->domain());
        if ($this->ipAddress === $this->hostname->domain()) {
            throw new InvalidArgumentException('Unable to resolve domain name.');
        }
    }

    public function ipAddress(): string
    {
        return $this->ipAddress;
    }
}
