<?php

namespace App\Http\Controllers;

use App\Models\DebtorAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;

class DebtorAttachmentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DebtorAttachment::with('user')
                ->whereHas('user', function($q) {
                    $q->where('type', '1');
                });
            
            if ($request->debtor_id) {
                $data->where('user_id', $request->debtor_id);
            }
            
            return DataTables::of($data)
                ->addColumn('corporate_debtor', function($row) {
                    return $row->user->name;
                })
                ->addColumn('action', function($row) {
                    return '<a href="'.route('debtor-attachments.edit', $row->id).'" class="btn btn-sm btn-info">Edit</a> 
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('.$row->id.')">Delete</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('app.debtor-attachments.list');
    }

    public function create()
    {
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.debtor-attachments.addedit', compact('corporateDebtors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'file' => 'required|file|max:10240',
        ]);
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('attachments', $fileName, 'public');

        DebtorAttachment::create([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
        ]);

        return redirect()->route('debtor-attachments.index')->with('success', 'Attachment created successfully.');
    }

    public function edit(DebtorAttachment $debtorAttachment)
    {
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.debtor-attachments.addedit', compact('debtorAttachment', 'corporateDebtors'));
    }

    public function update(Request $request, DebtorAttachment $debtorAttachment)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'file' => 'nullable|file|max:10240',
        ]);
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($debtorAttachment->file_path);
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
        }

        $debtorAttachment->update($validated);

        return redirect()->route('debtor-attachments.index')->with('success', 'Attachment updated successfully.');
    }

    public function destroy(DebtorAttachment $debtorAttachment)
    {
        Storage::disk('public')->delete($debtorAttachment->file_path);
        $debtorAttachment->delete();

        return redirect()->route('debtor-attachments.index')->with('success', 'Attachment deleted successfully.');
    }

    public function download($encryptedId)
    {
        try {
            $id = Crypt::decryptString($encryptedId);
            $attachment = DebtorAttachment::findOrFail($id);
            return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
        } catch (\Exception $e) {
            abort(404);
        }
    }

    public function getByDebtor(Request $request)
    {
        $attachments = DebtorAttachment::where('user_id', $request->user_id)
            ->select('id', 'name')
            ->get();
        
        return response()->json($attachments);
    }
}
