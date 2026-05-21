<?php

namespace App\Exceptions;

use RuntimeException;

class ExcelPasswordRequiredException extends RuntimeException
{
    public function __construct(string $message = 'File Excel ini dilindungi password. Masukkan password untuk melanjutkan.')
    {
        parent::__construct($message);
    }
}
