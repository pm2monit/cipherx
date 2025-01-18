<?php 

namespace CipherX;

/**
 * Encryption handler using AES-256-CBC with HMAC authentication
 */
class Handler
{
    private string $cipher = 'aes-256-cbc';
    private string $key;
    private string $iv;
    private string $hmacAlgo = 'sha256';

    /**
     * Initialize encryption handler
     *
     * @param string|null $key Optional encryption key (32 bytes)
     * @throws \RuntimeException If OpenSSL extension is not available
     */
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

    /**
     * Encrypt data using AES-256-CBC with HMAC authentication
     *
     * @param string $data Data to encrypt
     * @return string Base64 encoded encrypted data with HMAC
     * @throws \RuntimeException If encryption fails
     */
    public function encrypt(string $data): string
    {
        $encryptedData = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $this->iv);
        if ($encryptedData === false) {
            throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        // Generate HMAC for authentication
        $hmac = hash_hmac($this->hmacAlgo, $this->iv . $encryptedData, $this->key, true);
        
        // Combine IV + encrypted data + HMAC
        return base64_encode($this->iv . $encryptedData . $hmac);
    }

    /**
     * Decrypt data and verify HMAC
     *
     * @param string $data Base64 encoded encrypted data with HMAC
     * @return string Decrypted data
     * @throws \InvalidArgumentException If data format is invalid
     * @throws \RuntimeException If decryption fails or HMAC verification fails
     */
    public function decrypt(string $data): string
    {
        $decodedData = base64_decode($data, true);
        if ($decodedData === false) {
            throw new \InvalidArgumentException('Invalid base64 encoded data.');
        }

        // Extract IV, encrypted data and HMAC
        $ivLength = 16;
        $hmacLength = 32; // SHA256 produces 32 byte hash
        $dataLength = strlen($decodedData);

        if ($dataLength < ($ivLength + $hmacLength)) {
            throw new \InvalidArgumentException('Data is too short.');
        }

        $iv = substr($decodedData, 0, $ivLength);
        $encryptedData = substr($decodedData, $ivLength, $dataLength - $ivLength - $hmacLength);
        $storedHmac = substr($decodedData, -$hmacLength);

        // Verify HMAC
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

    /**
     * Set custom IV for encryption
     *
     * @param string $iv 16-byte initialization vector
     * @throws \InvalidArgumentException If IV length is invalid
     */
    public function setIv(string $iv): void
    {
        if (strlen($iv) !== 16) {
            throw new \InvalidArgumentException('IV must be exactly 16 bytes long.');
        }
        $this->iv = $iv;
    }

    /**
     * Generate a cryptographically secure random key
     *
     * @return string 32-byte random key
     * @throws \RuntimeException If key generation fails
     */
    private function generateKey(): string
    {
        $key = openssl_random_pseudo_bytes(32, $strong);
        if ($key === false || !$strong) {
            throw new \RuntimeException('Failed to generate a secure encryption key.');
        }
        return $key;
    }

    /**
     * Generate a cryptographically secure random IV
     *
     * @return string 16-byte initialization vector
     * @throws \RuntimeException If IV generation fails
     */
    private function generateIv(): string
    {
        $iv = openssl_random_pseudo_bytes(16, $strong);
        if ($iv === false || !$strong) {
            throw new \RuntimeException('Failed to generate a secure IV.');
        }
        return $iv;
    }
}
