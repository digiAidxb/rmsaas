<?php

namespace App\Services\Import\Parsers;

use App\Services\Import\Contracts\FileParserInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

abstract class BaseFileParser implements FileParserInterface
{
    protected array $supportedExtensions = [];
    protected array $supportedMimeTypes = [];
    protected int $maxFileSize;
    protected int $defaultChunkSize = 1000;

    public function __construct()
    {
        $this->maxFileSize = config('import.max_file_size', 50 * 1024 * 1024); // 50MB default
    }

    public function parseFile(UploadedFile $file, array $options = []): array
    {
        $this->validateFile($file);
        
        $format = $this->detectFormat($file);
        $parseOptions = array_merge($format, $options);
        
        return $this->performParsing($file, $parseOptions);
    }

    public function detectFormat(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        $format = [
            'extension' => $extension,
            'mime_type' => $mimeType,
            'encoding' => $this->detectEncoding($file),
            'delimiter' => $this->detectDelimiter($file),
            'quote_character' => $this->detectQuoteCharacter($file),
            'has_headers' => $this->detectHeaders($file),
            'confidence' => $this->calculateFormatConfidence($file)
        ];

        return $format;
    }

    public function getSampleRows(UploadedFile $file, int $limit = 10, int $offset = 0): array
    {
        $this->validateFile($file);
        
        $data = $this->performParsing($file, ['limit' => $limit + $offset]);
        
        return array_slice($data, $offset, $limit);
    }

    public function getHeaders(UploadedFile $file): array
    {
        $this->validateFile($file);
        
        $sampleData = $this->getSampleRows($file, 1, 0);
        
        if (empty($sampleData)) {
            return [];
        }

        if ($this->detectHeaders($file)) {
            return array_keys($sampleData[0]);
        }

        // Generate column names for files without headers
        $firstRow = reset($sampleData);
        return array_map(fn($i) => "Column " . ($i + 1), array_keys($firstRow));
    }

    public function getRowCount(UploadedFile $file): int
    {
        $this->validateFile($file);
        
        return $this->countRows($file);
    }

    public function validateFileFormat(UploadedFile $file): array
    {
        $errors = [];
        $warnings = [];

        // Check file size
        if ($file->getSize() > $this->maxFileSize) {
            $errors[] = "File size exceeds maximum allowed size of " . ($this->maxFileSize / 1024 / 1024) . "MB";
        }

        // Check file extension
        if (!$this->canParse($file)) {
            $errors[] = "Unsupported file format. Supported formats: " . implode(', ', $this->supportedExtensions);
        }

        // Check if file is empty
        if ($file->getSize() === 0) {
            $errors[] = "File is empty";
        }

        // Additional format-specific validation
        $formatValidation = $this->validateSpecificFormat($file);
        $errors = array_merge($errors, $formatValidation['errors']);
        $warnings = array_merge($warnings, $formatValidation['warnings']);

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
            'file_info' => [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension()
            ]
        ];
    }

    public function getSupportedExtensions(): array
    {
        return $this->supportedExtensions;
    }

    public function canParse(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        return in_array($extension, $this->supportedExtensions) || 
               in_array($mimeType, $this->supportedMimeTypes);
    }

    public function parseInChunks(UploadedFile $file, int $chunkSize = 1000, callable $callback = null): \Generator
    {
        $this->validateFile($file);
        
        $totalRows = $this->getRowCount($file);
        $offset = 0;
        
        while ($offset < $totalRows) {
            $chunk = $this->getSampleRows($file, $chunkSize, $offset);
            
            if ($callback) {
                $callback($chunk, $offset, $totalRows);
            }
            
            yield $chunk;
            
            $offset += $chunkSize;
        }
    }

    /**
     * Abstract methods to be implemented by specific parsers
     */
    abstract protected function performParsing(UploadedFile $file, array $options): array;
    abstract protected function countRows(UploadedFile $file): int;
    abstract protected function validateSpecificFormat(UploadedFile $file): array;

    /**
     * File validation
     */
    protected function validateFile(UploadedFile $file): void
    {
        if (!$file->isValid()) {
            throw new InvalidArgumentException('Invalid file upload');
        }

        if (!$this->canParse($file)) {
            throw new InvalidArgumentException('Unsupported file format');
        }

        if ($file->getSize() > $this->maxFileSize) {
            throw new InvalidArgumentException('File size exceeds maximum allowed size');
        }
    }

    /**
     * Detect file encoding
     */
    protected function detectEncoding(UploadedFile $file): string
    {
        $content = file_get_contents($file->getPathname(), false, null, 0, 1024);
        
        $encodings = ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'];
        
        foreach ($encodings as $encoding) {
            if (mb_check_encoding($content, $encoding)) {
                return $encoding;
            }
        }
        
        return 'UTF-8'; // Default fallback
    }

    /**
     * Detect delimiter for CSV files
     */
    protected function detectDelimiter(UploadedFile $file): string
    {
        $content = file_get_contents($file->getPathname(), false, null, 0, 2048);
        
        $delimiters = [',', ';', "\t", '|', ':'];
        $delimiterCounts = [];
        
        foreach ($delimiters as $delimiter) {
            $delimiterCounts[$delimiter] = substr_count($content, $delimiter);
        }
        
        $mostCommon = array_search(max($delimiterCounts), $delimiterCounts);
        
        return $mostCommon ?: ',';
    }

    /**
     * Detect quote character
     */
    protected function detectQuoteCharacter(UploadedFile $file): string
    {
        $content = file_get_contents($file->getPathname(), false, null, 0, 1024);
        
        $quotes = ['"', "'", '`'];
        $quoteCounts = [];
        
        foreach ($quotes as $quote) {
            $quoteCounts[$quote] = substr_count($content, $quote);
        }
        
        $mostCommon = array_search(max($quoteCounts), $quoteCounts);
        
        return $mostCommon ?: '"';
    }

    /**
     * Detect if file has headers
     */
    protected function detectHeaders(UploadedFile $file): bool
    {
        try {
            $firstTwoRows = $this->getSampleRows($file, 2, 0);
            
            if (count($firstTwoRows) < 2) {
                return true; // Assume headers if only one row
            }
            
            $firstRow = $firstTwoRows[0];
            $secondRow = $firstTwoRows[1];
            
            // Check if first row contains mostly text and second row contains mostly numbers
            $firstRowTextCount = 0;
            $secondRowNumericCount = 0;
            
            foreach ($firstRow as $value) {
                if (is_string($value) && !is_numeric($value)) {
                    $firstRowTextCount++;
                }
            }
            
            foreach ($secondRow as $value) {
                if (is_numeric($value)) {
                    $secondRowNumericCount++;
                }
            }
            
            $totalColumns = count($firstRow);
            $textRatio = $firstRowTextCount / $totalColumns;
            $numericRatio = $secondRowNumericCount / $totalColumns;
            
            // If first row is mostly text and second row has more numbers, likely has headers
            return $textRatio > 0.5 && $numericRatio > 0.3;
            
        } catch (\Exception $e) {
            return true; // Default to assuming headers
        }
    }

    /**
     * Calculate confidence score for format detection
     */
    protected function calculateFormatConfidence(UploadedFile $file): int
    {
        $confidence = 50; // Base confidence
        
        // Increase confidence based on file extension match
        if (in_array(strtolower($file->getClientOriginalExtension()), $this->supportedExtensions)) {
            $confidence += 30;
        }
        
        // Increase confidence based on MIME type match
        if (in_array($file->getMimeType(), $this->supportedMimeTypes)) {
            $confidence += 20;
        }
        
        return min(100, $confidence);
    }

    /**
     * Log parsing errors
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error("File parsing error: {$message}", $context);
    }

    /**
     * Get memory-safe file handle
     */
    protected function getFileHandle(UploadedFile $file, string $mode = 'r')
    {
        $handle = fopen($file->getPathname(), $mode);
        
        if ($handle === false) {
            throw new InvalidArgumentException('Unable to open file for reading');
        }
        
        return $handle;
    }

    /**
     * Clean and normalize cell value
     */
    protected function cleanCellValue($value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);
            
            // Remove BOM if present
            $value = str_replace("\xEF\xBB\xBF", '', $value);
            
            // Convert empty strings to null
            if ($value === '') {
                return null;
            }
            
            // Try to convert numeric strings to numbers
            if (is_numeric($value)) {
                if (strpos($value, '.') !== false) {
                    return (float) $value;
                } else {
                    return (int) $value;
                }
            }
        }
        
        return $value;
    }
}