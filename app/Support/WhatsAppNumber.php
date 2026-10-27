<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Canonical handling of Nicaraguan WhatsApp numbers.
 *
 * Students type the 8-digit national number (`[578]\d{7}`); the application
 * stores it with the 505 country code so every `wa.me` link stays valid even
 * when the number is copied from a phone book or pasted with separators.
 *
 * This class is the single source of truth shared by the registration and
 * profile FormRequests, the Eloquent accessors and the Blade component that
 * renders the field.
 */
final class WhatsAppNumber
{
    public const string COUNTRY_CODE = '505';

    public const int NATIONAL_LENGTH = 8;

    /**
     * National mobile numbers: 8 digits starting with 5, 7 or 8.
     */
    public const string NATIONAL_PATTERN = '/^[578]\d{7}$/';

    /**
     * Canonical storage format: country code plus national number.
     */
    public const string STORED_PATTERN = '/^505[578]\d{7}$/';

    /**
     * Reduce any user-provided value to the canonical stored format.
     *
     * Non-digit characters are dropped, an explicit 505 prefix is removed and
     * re-applied only when the remaining value has exactly 8 digits. Anything
     * else is returned as-is so the validation rule can reject it instead of
     * silently fixing it.
     */
    public static function normalize(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        $digits = preg_replace('/\D+/', '', $value) ?? '';
        $digits = self::stripCountryCode($digits);

        return strlen($digits) === self::NATIONAL_LENGTH
            ? self::COUNTRY_CODE.$digits
            : $digits;
    }

    /**
     * Extract the 8-digit national part for display or form prefill.
     */
    public static function national(mixed $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        return self::stripCountryCode($digits);
    }

    /**
     * Determine whether the value already matches the canonical format.
     */
    public static function isValid(mixed $value): bool
    {
        return is_string($value) && preg_match(self::STORED_PATTERN, $value) === 1;
    }

    /**
     * Remove a leading 505 when the value carries both parts.
     */
    private static function stripCountryCode(string $digits): string
    {
        $fullLength = self::NATIONAL_LENGTH + strlen(self::COUNTRY_CODE);

        if (strlen($digits) === $fullLength && str_starts_with($digits, self::COUNTRY_CODE)) {
            return substr($digits, strlen(self::COUNTRY_CODE));
        }

        return $digits;
    }
}
