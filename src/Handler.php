<?php 

namespace CipherX;

class Handler
{
    private string $cipher = 'aes-256-cbc';
    private string $key;
    private string $iv;
    private string $hmacAlgo = 'sha256';

    public function __construct(?string $key = null)
    {
        if (!extension_loaded('openssl')) {
            throw new \RuntimeException('OpenSSL extension is required.');
        }

        if ($key === null) {
            $key = $this->generateKey();
        } elseif (strlen($key) !== 32) {
            throw new \InvalidArgumentException('Key must be exactly 32 bytes long.');
        }

        $this->key = $key;
        $this->iv = $this->generateIv();
    }

    public function encrypt(string $data): string
    {
        $encryptedData = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv);
        if ($encryptedData === false) {
            throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        $hmac = hash_hmac($this->hmacAlgo, $this->iv . $encryptedData, $this->key, true);
        
        return base64_encode($this->iv . $encryptedData . $hmac);
    }

    public function decrypt(string $data): string
    {
        $decodedData = base64_decode($data, true);
        if ($decodedData === false) {
            throw new \InvalidArgumentException('Invalid base64 encoded data.');
        }


        $ivLength = 16;
        $hmacLength = 32; 
        $dataLength = strlen($decodedData);

        if ($dataLength < ($ivLength + $hmacLength)) {
            throw new \InvalidArgumentException('Data is too short.');
        }

        $iv = substr($decodedData, 0, $ivLength);
        $encryptedData = substr($decodedData, $ivLength, $dataLength - $ivLength - $hmacLength);
        $storedHmac = substr($decodedData, -$hmacLength);

        $calculatedHmac = hash_hmac($this->hmacAlgo, $iv . $encryptedData, $this->key, true);
        if (!hash_equals($calculatedHmac, $storedHmac)) {
            throw new \RuntimeException('HMAC verification failed. Data may have been tampered with.');
        }

        $decryptedData = openssl_decrypt($encryptedData, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
        if ($decryptedData === false) {
            throw new \RuntimeException('Decryption failed: ' . openssl_error_string());
        }

        return $decryptedData;
    }

    public function setIv(string $iv): void
    {
        if (strlen($iv) !== 16) {
            throw new \InvalidArgumentException('IV must be exactly 16 bytes long.');
        }
        $this->iv = $iv;
    }

    private function generateKey(): string
    {
        $key = openssl_random_pseudo_bytes(32, $strong);
        if ($key === false || !$strong) {
            throw new \RuntimeException('Failed to generate a secure encryption key.');
        }
        return $key;
    }

    private function generateIv(): string
    {
        $iv = openssl_random_pseudo_bytes(16, $strong);
        if ($iv === false || !$strong) {
            throw new \RuntimeException('Failed to generate a secure IV.');
        }
        return $iv;
    }
}
