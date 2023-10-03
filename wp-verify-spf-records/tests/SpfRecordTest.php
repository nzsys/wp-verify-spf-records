<?php

declare(strict_types=1);

namespace Nzsys\EnvelopFixer;

use InvalidArgumentException;
use Nzsys\EnvelopFixer\App\SPFRecord;
use Nzsys\EnvelopFixer\App\IpConverter;
use Nzsys\EnvelopFixer\Spec\Email;
use PHPUnit\Framework\TestCase;

final class SpfRecordTest extends TestCase
{
    private Email $email;
    private Email $myEmail;

    public function setUp(): void
    {
        $this->myEmail = new Email('my-email@example.com');
    }

    public function tearDown(): void {}

    public function testEmailValidationSuccess(): void
    {
        $email = 'my-email@example.com';
        $this->email = new Email($email);
        $this->assertSame($this->email->email(), $email);
    }

    public function testEmailValidationFailure(): void
    {
        $email = 'my-email@examplecom';
        $exception = new InvalidArgumentException("This ({$email}) email address is considered invalid.");
        try {
            $this->email = new Email($email);
        } catch (InvalidArgumentException $e) {
            $this->assertSame($e->getMessage(), $exception->getMessage());
        }
    }

    public function testDomainValidationFailure(): void
    {
        $email = 'my-email@exampleeeeee.com';
        $exception = new InvalidArgumentException("The email ({$email}) is valid, but it doesn't have valid MX records on its domain.");
        try {
            $this->email = new Email($email);
        } catch (InvalidArgumentException $e) {
            $this->assertSame($e->getMessage(), $exception->getMessage());
        }
    }

    public function testDeterminationSpfRecord(): void
    {
        $spfRecord = new SpfRecord($this->myEmail);
        $this->assertTrue($spfRecord->hasRecord());
    }

    public function testConvertDomainToIp(): void
    {
        $converter = new IpConverter($this->myEmail);
        $this->assertSame('93.184.216.34', $converter->ipAddress());
    }

    public function testIncludedSPFRecord(): void
    {
        $spfRecord = new SpfRecord($this->myEmail);
        $this->assertTrue($spfRecord->hasRecord());
        $converter = new IpConverter($this->myEmail);
        $this->assertFalse($spfRecord->isIpInclude($converter->ipAddress()));
    }
}
