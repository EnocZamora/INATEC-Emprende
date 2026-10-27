<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\WhatsAppNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class WhatsAppNumberTest extends TestCase
{
    #[DataProvider('userInput')]
    public function test_it_normalizes_to_the_canonical_stored_format(string $input, string $expected): void
    {
        $this->assertSame($expected, WhatsAppNumber::normalize($input));
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function userInput(): array
    {
        return [
            'plain national number' => ['88888888', '50588888888'],
            'grouped with spaces' => ['8888 8888', '50588888888'],
            'grouped with dashes' => ['7777-7777', '50577777777'],
            'international prefix' => ['+505 8888 8888', '50588888888'],
            'already stored' => ['50588888888', '50588888888'],
            'too few digits' => ['8888888', '8888888'],
            'too many digits' => ['888888888', '888888888'],
            'letters only' => ['llámame', ''],
            'empty' => ['', ''],
        ];
    }

    public function test_it_extracts_the_national_part_for_prefill(): void
    {
        $this->assertSame('88888888', WhatsAppNumber::national('50588888888'));
        $this->assertSame('88888888', WhatsAppNumber::national('+505 8888 8888'));
        $this->assertSame('88888888', WhatsAppNumber::national('88888888'));
        $this->assertSame('', WhatsAppNumber::national(null));
        $this->assertSame('', WhatsAppNumber::national(''));
    }

    public function test_it_validates_only_nicaraguan_mobiles_in_storage_format(): void
    {
        $this->assertTrue(WhatsAppNumber::isValid('50588888888'));
        $this->assertTrue(WhatsAppNumber::isValid('50557777777'));
        $this->assertTrue(WhatsAppNumber::isValid('50571111111'));

        $this->assertFalse(WhatsAppNumber::isValid('50528888888'));
        $this->assertFalse(WhatsAppNumber::isValid('88888888'));
        $this->assertFalse(WhatsAppNumber::isValid('5058888888'));
        $this->assertFalse(WhatsAppNumber::isValid(null));
    }

    public function test_non_string_input_never_throws(): void
    {
        $this->assertSame('', WhatsAppNumber::normalize(null));
        $this->assertSame('', WhatsAppNumber::normalize(['88888888']));
        $this->assertSame('', WhatsAppNumber::normalize(88888888));
        $this->assertFalse(WhatsAppNumber::isValid(88888888));
    }
}
