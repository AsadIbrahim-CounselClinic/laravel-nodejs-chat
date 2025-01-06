<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class DecryptMessage implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!$value) {
            return null;
        }
    
        try {
            if (!str_contains($value, ':')) {
                throw new \Exception('Invalid message format');
            }
    
            [$iv, $cipherText] = explode(':', $value, 2);
    
            $appKey = config('app.key');
            if (str_starts_with($appKey, 'base64:')) {
                $appKey = substr($appKey, 7);
            }
    
            $decodedKey = base64_decode($appKey);
    
            if (strlen($decodedKey) !== 32) {
                throw new \Exception('Invalid key length: expected 32 bytes');
            }
    
            $decodedIV = base64_decode($iv);
            $decodedCipherText = base64_decode($cipherText);
    
            $decryptedMessage = openssl_decrypt(
                $decodedCipherText,
                'aes-256-cbc',
                $decodedKey,
                OPENSSL_RAW_DATA,
                $decodedIV
            );
    
            if ($decryptedMessage === false) {
                throw new \Exception(openssl_error_string());
            }
    
            return $decryptedMessage;
            
        } catch (\Exception $e) {
            Log::error('Error decrypting message: ' . $e->getMessage(), ['value' => $value]);
            return null;
        }
    }
    
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}