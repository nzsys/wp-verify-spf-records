<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer\App;

use Nzsys\EnvelopFixer\Core\HostnameInterface;

final class SPFRecord
{
    /** @param array<int, string> $record  */
    public function __construct(
        private readonly HostnameInterface $hostname,
        private array $record = [],
    ) {}

    public function hasRecord(
        ?string $recursionDomain = null,
    ): bool {
        $domain = $recursionDomain?: $this->hostname->domain();
        if (!checkdnsrr($domain, 'TXT')) {
            return false;
        }
        $records = dns_get_record($domain, DNS_TXT);
        if (!$records) {
            return false;
        }
        foreach ($records as $record) {
            $txt = $record['txt'];
            $this->record[] = $txt;
            if (!str_contains($txt, 'v=spf1')) {
                continue;
            }
            if (!str_contains($txt, 'include:')) {
                return true;
            }
            $domain = $this->getSpfDomain($txt);
            if ($this->hasRecord($domain)) {
                return true;
            }
        }
        return false;
    }

    public function isIpInclude(
        string $ipAddress,
    ): bool
    {
        return array_reduce($this->record, static function ($carry, $item) use ($ipAddress) {
            return $carry || str_contains($item, $ipAddress);
        }, false);
    }

    public function getRecord(): string
    {
        return implode(PHP_EOL, $this->record);
    }

    /** @return array<int, string>  */
    public function getRecords(): array
    {
        return $this->record;
    }

    private function getSpfDomain(
        string $txt
    ): string {
        $startPosition = (int) (strpos($txt, 'include:')) + 8;
        $endPosition = strpos($txt, ' ', $startPosition);
        if (false === $endPosition) {
            $endPosition = strlen($txt);
        }
        return substr($txt, $startPosition, $endPosition - $startPosition);
    }
}
