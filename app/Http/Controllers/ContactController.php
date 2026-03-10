<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Http\Requests\ContactImportRequest;
use App\Models\Contact;
use App\Models\User;
use App\Imports\ContactsImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
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
                    return '<a href="'.route('contacts.show', $row->id).'" class="btn btn-sm btn-secondary" title="View"><i class="fa fa-eye"></i></a> 
                            <a href="'.route('contacts.edit', $row->id).'" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a> 
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteContact('.$row->id.')" title="Delete"><i class="fa fa-trash"></i></button>';
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

    public function show(Contact $contact)
    {
        $contact->load(['user', 'attributes']);
        return view('app.contacts.view', compact('contact'));
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(['success' => true, 'message' => 'Contact deleted successfully.']);
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
        $headers = ['name', 'email', 'phone', 'attribute_1', 'attribute_2', 'attribute_3', 'attribute_4'];
        $sample = [
            ['John Doe', 'john@example.com', '1234567890', 'Value1', 'Value2', 'Value3', 'Value4'],
            ['Jane Smith', 'jane@example.com', '0987654321', 'Value1', 'Value2', '', ''],
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
        
        Contact::$disableActivityLog = true;
        $import = new ContactsImport($userId, $allowDuplicates);
        Excel::import($import, $file);
        Contact::$disableActivityLog = false;

        $failures = $import->getFailures();
        $errors = $import->getErrors();
        $skipped = $import->getSkipped();
        
        $imported = Contact::where('user_id', $userId)->count();

        foreach ($failures as $failure) {
            $skipped[] = [
                'name' => $failure->values()['name'] ?? 'N/A',
                'email' => $failure->values()['email'] ?? 'N/A',
                'reason' => implode(', ', $failure->errors())
            ];
        }

        $message = "Successfully imported contacts.";
        if (!empty($skipped)) {
            session()->flash('skipped', $skipped);
            $message .= " " . count($skipped) . " records were skipped.";
        }

        activity_log('Contact', \App\Enums\ActionType::IMPORT, null, null, ['imported' => $imported, 'skipped' => count($skipped)]);

        return redirect()->route('contacts.index')->with('success', $message);
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

    public function export(Request $request)
    {
        $query = Contact::with(['user', 'attributes'])
            ->whereHas('user', function($q) {
                $q->where('type', '1');
            });
        
        if ($request->debtor_id) {
            $query->where('user_id', $request->debtor_id);
        }
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contacts.name', 'like', "%{$search}%")
                  ->orWhere('contacts.email', 'like', "%{$search}%")
                  ->orWhere('contacts.phone', 'like', "%{$search}%")
                  ->orWhereHas('attributes', function($q) use ($search) {
                      $q->where('value', 'like', "%{$search}%");
                  });
            });
        }
        
        $contacts = $query->get();
        
        $filename = 'contacts_export_' . now()->format('YmdHis') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $headers = ['ID', 'Corporate Debtor', 'Name', 'Email', 'Phone', 'Type', 'Attribute 1', 'Attribute 2', 'Attribute 3', 'Attribute 4'];
        fputcsv($handle, $headers);
        
        foreach ($contacts as $contact) {
            fputcsv($handle, [
                $contact->id,
                $contact->user->name,
                $contact->name,
                $contact->email,
                $contact->phone,
                is_string($contact->type) ? $contact->type : $contact->type->value,
                $contact->attributes->where('key', 'attribute_1')->first()->value ?? '',
                $contact->attributes->where('key', 'attribute_2')->first()->value ?? '',
                $contact->attributes->where('key', 'attribute_3')->first()->value ?? '',
                $contact->attributes->where('key', 'attribute_4')->first()->value ?? '',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}
