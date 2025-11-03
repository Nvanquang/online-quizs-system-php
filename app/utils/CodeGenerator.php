<?php

class CodeGenerator
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Tạo UUID v4 ngẫu nhiên (36 ký tự hex với dấu gạch ngang).
     */
    public function generateUUID(): string
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);  // Set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);  // Set variant to 10

        // Format thành UUID string
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Tạo mã PIN 6 chữ số ngẫu nhiên (từ 000000 đến 999999).
     */
    public function generatePIN(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Tạo cả UUID và PIN.
     */
    public function generateSessionCodes(): array
    {
        return [
            'uuid' => $this->generateUUID(),
            'pin' => $this->generatePIN()
        ];
    }
}

?>