<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    protected $userId;
    protected $allowDuplicates;
    protected $failures = [];
    protected $errors = [];
    protected $skipped = [];
    protected $rowNumber = 1;

    public function __construct($userId, $allowDuplicates = false)
    {
        $this->userId = $userId;
        $this->allowDuplicates = $allowDuplicates;
    }

    public function model(array $row)
    {
        $this->rowNumber++;
        
        $name = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['name'] ?? ''));
        $email = trim($row['email'] ?? '');

        if (empty($name) || empty($email)) {
            $this->skipped[] = [
                'name' => $name ?: 'N/A',
                'email' => $email ?: 'N/A',
                'reason' => 'Missing name or email'
            ];
            return null;
        }

        if (!$this->allowDuplicates && Contact::where('user_id', $this->userId)->where('email', $email)->exists()) {
            $this->skipped[] = [
                'name' => $name,
                'email' => $email,
                'reason' => 'Duplicate email'
            ];
            return null;
        }

        $contact = new Contact([
            'user_id' => $this->userId,
            'name' => $name,
            'email' => $email,
            'phone' => preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['phone'] ?? '')),
            'type' => 'SUBSCRIBED',
        ]);

        $contact->save();

        $attributes = [
            'attribute_1' => preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['attribute_1'] ?? '')),
            'attribute_2' => preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['attribute_2'] ?? '')),
            'attribute_3' => preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['attribute_3'] ?? '')),
            'attribute_4' => preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', trim($row['attribute_4'] ?? '')),
        ];

        foreach ($attributes as $key => $value) {
            if (!empty($value)) {
                $contact->attributes()->create(['key' => $key, 'value' => $value]);
            }
        }

        return $contact;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ];
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }

    public function onFailure(Failure ...$failures)
    {
        $this->failures = array_merge($this->failures, $failures);
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
