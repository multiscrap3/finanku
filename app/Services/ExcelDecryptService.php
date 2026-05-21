<?php

namespace App\Services;

use App\Exceptions\ExcelPasswordRequiredException;
use InvalidArgumentException;

class ExcelDecryptService
{
    private string $tempDir;

    public function __construct()
    {
        $this->tempDir = storage_path('app/temp');

        if (! is_dir($this->tempDir)) {
            mkdir($this->tempDir, 0755, true);
        }
    }

    /**
     * Detects encrypted xlsx by checking OLE2 magic bytes (D0CF11E0...).
     * Normal xlsx are ZIP files starting with "PK".
     * Does NOT require ext-zip.
     */
    public function isEncrypted(string $filePath): bool
    {
        $handle = fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        $header = fread($handle, 8);
        fclose($handle);

        // OLE2 Compound Document signature = encrypted Office file
        return str_starts_with($header, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1");
    }

    /**
     * Decrypts an encrypted xlsx using Python msoffcrypto-tool.
     * Returns path to decrypted temp file — caller must delete it.
     *
     * @throws ExcelPasswordRequiredException
     * @throws InvalidArgumentException on wrong password or Python error
     */
    public function decrypt(string $encryptedPath, string $password): string
    {
        $decryptedPath = $this->tempDir . DIRECTORY_SEPARATOR . 'dec_' . uniqid() . '.xlsx';
        $scriptPath    = $this->tempDir . DIRECTORY_SEPARATOR . 'msoff_' . uniqid() . '.py';

        // Write Python script as a file — avoids path escaping issues
        $pythonScript = $this->buildPythonScript();
        file_put_contents($scriptPath, $pythonScript);

        $cmd = sprintf(
            'python %s %s %s %s 2>&1',
            escapeshellarg($scriptPath),
            escapeshellarg($encryptedPath),
            escapeshellarg($decryptedPath),
            escapeshellarg($password)
        );

        $output   = [];
        $exitCode = 0;
        exec($cmd, $output, $exitCode);

        @unlink($scriptPath);

        $outputText = implode("\n", $output);

        if ($exitCode !== 0) {
            if (file_exists($decryptedPath)) {
                @unlink($decryptedPath);
            }

            if (stripos($outputText, 'wrong password') !== false
                || stripos($outputText, 'InvalidKeyError') !== false
                || stripos($outputText, 'incorrect') !== false
                || stripos($outputText, 'bad decrypt') !== false) {
                throw new InvalidArgumentException('Password yang dimasukkan salah. Periksa kembali password file Excel Mandiri.');
            }

            throw new InvalidArgumentException('Gagal mendekripsi file Excel: ' . $outputText);
        }

        if (! file_exists($decryptedPath)) {
            throw new InvalidArgumentException(
                'Dekripsi selesai namun file tidak ditemukan di: ' . $decryptedPath
                . ' | Output: ' . $outputText
            );
        }

        return $decryptedPath;
    }

    private function buildPythonScript(): string
    {
        return <<<'PYTHON'
import msoffcrypto, sys

if len(sys.argv) < 4:
    print("Usage: script.py <input> <output> <password>", file=sys.stderr)
    sys.exit(1)

input_path  = sys.argv[1]
output_path = sys.argv[2]
password    = sys.argv[3]

try:
    with open(input_path, 'rb') as f:
        office = msoffcrypto.OfficeFile(f)
        office.load_key(password=password)
        with open(output_path, 'wb') as out:
            office.decrypt(out)
    print('OK')
except Exception as e:
    print(str(e), file=sys.stderr)
    sys.exit(1)
PYTHON;
    }
}
