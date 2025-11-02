<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class EncryptionService
{
    /**
     * Encrypt sensitive data
     * 
     * @param string|null $value
     * @return string|null
     */
    public static function encrypt($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::encryptString($value);
        } catch (\Exception $e) {
            \Log::error('Encryption failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Decrypt sensitive data
     * 
     * @param string|null $value
     * @return string|null
     */
    public static function decrypt($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException $e) {
            \Log::error('Decryption failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Hash data for searching (one-way)
     * Used for creating searchable hashes of encrypted data
     * 
     * @param string $value
     * @return string
     */
    public static function hash($value)
    {
        return hash('sha256', strtolower(trim($value)));
    }

    /**
     * Mask phone number for display
     * Example: 01234567890 -> 012****7890
     * 
     * @param string $phone
     * @return string
     */
    public static function maskPhone($phone)
    {
        if (empty($phone)) {
            return '';
        }

        $length = strlen($phone);
        if ($length < 4) {
            return str_repeat('*', $length);
        }

        $start = substr($phone, 0, 3);
        $end = substr($phone, -4);
        $middle = str_repeat('*', $length - 7);

        return $start . $middle . $end;
    }

    /**
     * Mask address for display
     * Shows only first 20 characters
     * 
     * @param string $address
     * @return string
     */
    public static function maskAddress($address)
    {
        if (empty($address)) {
            return '';
        }

        if (strlen($address) <= 20) {
            return $address;
        }

        return substr($address, 0, 20) . '...';
    }
}
