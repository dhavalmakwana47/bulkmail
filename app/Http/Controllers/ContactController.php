<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\ContactImportRequest;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Contact::with(['user', 'attributes'])
                ->whereHas('user', function($q) {
                    $q->where('type', '1');
                });
            
            if ($request->debtor_id) {
                $data->where('user_id', $request->debtor_id);
            }
            
            return DataTables::of($data)
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" value="'.$row->id.'" class="contact-checkbox">';
                })
                ->addColumn('corporate_debtor', function($row) {
                    return $row->user->name;
                })
                ->addColumn('type', function($row) {
                    return is_string($row->type) ? $row->type : $row->type->value;
                })
                ->addColumn('attribute_1', function($row) {
                    return $row->attributes->where('key', 'attribute_1')->first()->value ?? '-';
                })
                ->addColumn('attribute_2', function($row) {
                    return $row->attributes->where('key', 'attribute_2')->first()->value ?? '-';
                })
                ->addColumn('attribute_3', function($row) {
                    return $row->attributes->where('key', 'attribute_3')->first()->value ?? '-';
                })
                ->addColumn('attribute_4', function($row) {
                    return $row->attributes->where('key', 'attribute_4')->first()->value ?? '-';
                })
                ->addColumn('action', function($row) {
                    return '<a href="'.route('contacts.edit', $row->id).'" class="btn btn-sm btn-info">Edit</a> 
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteContact('.$row->id.')">Delete</button>';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->get('search')['value']) {
                        $query->where(function($q) use ($search) {
                            $q->where('contacts.name', 'like', "%{$search}%")
                              ->orWhere('contacts.email', 'like', "%{$search}%")
                              ->orWhere('contacts.phone', 'like', "%{$search}%")
                              ->orWhere('contacts.type', 'like', "%{$search}%")
                              ->orWhereHas('user', function($q) use ($search) {
                                  $q->where('name', 'like', "%{$search}%");
                              })
                              ->orWhereHas('attributes', function($q) use ($search) {
                                  $q->where('value', 'like', "%{$search}%");
                              });
                        });
                    }
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
        
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.contacts.list', compact('corporateDebtors'));
    }

    public function create()
    {
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.contacts.addedit', compact('corporateDebtors'));
    }

    public function store(ContactRequest $request)
    {
        $validated = $request->validated();
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }
        
        $contact = Contact::create($validated);

        if (!empty($validated['attributes'])) {
            foreach ($validated['attributes'] as $key => $value) {
                if (!empty($value)) {
                    $contact->attributes()->create(['key' => $key, 'value' => $value]);
                }
            }
        }

        return redirect()->route('contacts.index')->with('success', 'Contact created successfully.');
    }

    public function edit(Contact $contact)
    {
        $contact->load('attributes');
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.contacts.addedit', compact('contact', 'corporateDebtors'));
    }

    public function update(ContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }
        
        $contact->update($validated);

        if (!empty($validated['attributes'])) {
            foreach ($validated['attributes'] as $key => $value) {
                if (!empty($value)) {
                    $contact->attributes()->updateOrCreate(
                        ['key' => $key],
                        ['value' => $value]
                    );
                } else {
                    $contact->attributes()->where('key', $key)->delete();
                }
            }
        }

        return redirect()->route('contacts.index')->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Contact deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contacts,id',
        ]);

        $query = Contact::whereIn('id', $validated['ids'])
            ->whereHas('user', function($q) {
                $q->where('type', '1');
            });
        
        if ($request->debtor_id) {
            $query->where('user_id', $request->debtor_id);
        }
        
        $query->delete();

        return response()->json(['success' => true]);
    }

    public function import()
    {
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.contacts.import', compact('corporateDebtors'));
    }

    public function downloadSample()
    {
        $headers = ['name', 'email', 'phone', 'type', 'attribute_1', 'attribute_2', 'attribute_3', 'attribute_4'];
        $sample = [
            ['John Doe', 'john@example.com', '1234567890', 'MEMBER', 'Value1', 'Value2', 'Value3', 'Value4'],
            ['Jane Smith', 'jane@example.com', '0987654321', 'OTHER', 'Value1', 'Value2', '', ''],
        ];

        $filename = 'contacts_sample.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, $headers);
        foreach ($sample as $row) {
            fputcsv($handle, $row);
        }
        
        fclose($handle);
        exit;
    }

    public function processImport(ContactImportRequest $request)
    {
        $validated = $request->validated();
        $userId = auth()->user()->type == '1' ? auth()->id() : $validated['user_id'];
        $user = User::findOrFail($userId);
        $allowDuplicates = $user->duplicate_email;

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $imported = 0;
        $skipped = [];
        $chunk = [];
        $chunkSize = 100;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) continue;

            $email = trim($row[1] ?? '');
            if (empty($email)) continue;

            if (!$allowDuplicates) {
                $exists = Contact::where('user_id', $userId)->where('email', $email)->exists();
                if ($exists) {
                    $skipped[] = ['name' => $row[0] ?? '', 'email' => $email, 'reason' => 'Duplicate email'];
                    continue;
                }
            }

            $chunk[] = [
                'name' => trim($row[0] ?? ''),
                'email' => $email,
                'phone' => trim($row[2] ?? ''),
                'type' => in_array(strtoupper(trim($row[3] ?? '')), ['MEMBER', 'OTHER']) ? strtoupper(trim($row[3])) : 'OTHER',
                'attribute_1' => trim($row[4] ?? ''),
                'attribute_2' => trim($row[5] ?? ''),
                'attribute_3' => trim($row[6] ?? ''),
                'attribute_4' => trim($row[7] ?? ''),
            ];

            if (count($chunk) >= $chunkSize) {
                $this->saveChunk($chunk, $userId);
                $imported += count($chunk);
                $chunk = [];
            }
        }

        if (!empty($chunk)) {
            $this->saveChunk($chunk, $userId);
            $imported += count($chunk);
        }

        fclose($handle);

        $message = "Successfully imported {$imported} contacts.";
        if (!empty($skipped)) {
            session()->flash('skipped', $skipped);
            $message .= " " . count($skipped) . " records were skipped due to duplicate emails.";
        }

        activity_log('Contact', \App\Enums\ActionType::IMPORT, null, null, ['imported' => $imported, 'skipped' => count($skipped)]);

        return redirect()->route('contacts.index')->with('success', $message);
    }

    private function saveChunk(array $chunk, int $userId)
    {
        DB::transaction(function () use ($chunk, $userId) {
            Contact::$disableActivityLog = true;
            
            foreach ($chunk as $data) {
                $attributes = [
                    'attribute_1' => $data['attribute_1'],
                    'attribute_2' => $data['attribute_2'],
                    'attribute_3' => $data['attribute_3'],
                    'attribute_4' => $data['attribute_4'],
                ];
                unset($data['attribute_1'], $data['attribute_2'], $data['attribute_3'], $data['attribute_4']);

                $data['user_id'] = $userId;
                $contact = Contact::create($data);

                foreach ($attributes as $key => $value) {
                    if (!empty($value)) {
                        $contact->attributes()->create(['key' => $key, 'value' => $value]);
                    }
                }
            }
            
            Contact::$disableActivityLog = false;
        });
    }

    public function getContactsByDebtor(Request $request)
    {
        $userId = $request->user_id;
        $contacts = Contact::where('user_id', $userId)
            ->select('name', 'email')
            ->get();
        
        return response()->json([
            'count' => $contacts->count(),
            'contacts' => $contacts
        ]);
    }
}
