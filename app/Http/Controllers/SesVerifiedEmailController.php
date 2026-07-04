<?php

namespace App\Http\Controllers;

use App\Http\Requests\SesVerifiedEmailRequest;
use App\Models\SesVerifiedEmail;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SesVerifiedEmailController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SesVerifiedEmail::query();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('active_status', function ($row) {
                    $badge = $row->active_status === 'Y' ? 'success' : 'danger';
                    $label = $row->active_status === 'Y' ? 'Active' : 'Inactive';
                    return '<span class="badge badge-' . $badge . '">' . $label . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('ses-verified-emails.edit', $row->id) . '" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a> 
                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteItem(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>';
                })
                ->rawColumns(['active_status', 'action'])
                ->make(true);
        }

        return view('app.ses-verified-emails.list');
    }

    public function create()
    {
        return view('app.ses-verified-emails.addedit');
    }

    public function store(SesVerifiedEmailRequest $request)
    {
        SesVerifiedEmail::create($request->validated());

        return redirect()->route('ses-verified-emails.index')->with('success', 'SES Verified Email added successfully.');
    }

    public function edit(SesVerifiedEmail $sesVerifiedEmail)
    {
        return view('app.ses-verified-emails.addedit', compact('sesVerifiedEmail'));
    }

    public function update(SesVerifiedEmailRequest $request, SesVerifiedEmail $sesVerifiedEmail)
    {
        $sesVerifiedEmail->update($request->validated());

        return redirect()->route('ses-verified-emails.index')->with('success', 'SES Verified Email updated successfully.');
    }

    public function destroy(SesVerifiedEmail $sesVerifiedEmail)
    {
        $sesVerifiedEmail->delete();

        return response()->json(['success' => true, 'message' => 'SES Verified Email deleted successfully.']);
    }
}
