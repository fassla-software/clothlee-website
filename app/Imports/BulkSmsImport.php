<?php
namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class BulkSmsImport implements ToCollection, WithHeadingRow
{
    private $rows = [];
    private $defaultMessage;
    
    public function __construct($defaultMessage = null)
    {
        $this->defaultMessage = $defaultMessage;
    }
    
    public function collection(Collection $rows)
    {
        Log::info('Processing import collection', ['row_count' => $rows->count()]);
        
        foreach ($rows as $index => $row) {
            // FIXED: Better debugging
            Log::info("Processing row {$index}", ['row_data' => $row->toArray()]);
            
            $phone = $this->normalizePhone($this->getValue($row, ['phone', 'mobile', 'number', 'contact']));
            $message = $this->getValue($row, ['message', 'sms', 'text', 'content']) ?: $this->defaultMessage;
            
            // FIXED: Only add if we have both phone and message
            if ($phone && $message) {
                $this->rows[] = [
                    'phone' => $phone,
                    'message' => $message
                ];
                Log::info("Added row", ['phone' => $phone, 'message_length' => strlen($message)]);
            } else {
                Log::warning("Skipped row {$index}", [
                    'phone' => $phone, 
                    'has_message' => !empty($message),
                    'row_keys' => array_keys($row->toArray())
                ]);
            }
        }
        
        Log::info('Import collection processed', ['total_valid_rows' => count($this->rows)]);
    }
    
    public function getRows()
    {
        return $this->rows;
    }
    
    // FIXED: Better key matching
    private function getValue($row, $possibleKeys)
    {
        $rowArray = $row->toArray();
        
        foreach ($possibleKeys as $key) {
            // Exact match first
            if (isset($rowArray[$key]) && !empty($rowArray[$key])) {
                return trim($rowArray[$key]);
            }
            
            // Case-insensitive match
            foreach ($rowArray as $rowKey => $value) {
                if (strtolower(trim($rowKey)) === strtolower($key) && !empty($value)) {
                    return trim($value);
                }
            }
        }
        return null;
    }
    
    private function normalizePhone($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Convert to string and trim
        $phone = trim(strval($phone));
        
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Skip if empty after cleaning
        if (empty($phone)) {
            return null;
        }
        
        // Add Egypt country code if needed (optional)
        if (strlen($phone) === 10 && strpos($phone, '0') === 0) {
            $phone = '2' . $phone; 
        }
        
        return $phone;
    }
}