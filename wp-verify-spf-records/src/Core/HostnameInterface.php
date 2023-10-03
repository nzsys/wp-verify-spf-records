<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer\Core;

interface HostnameInterface
{
    public function domain(): string;
}
