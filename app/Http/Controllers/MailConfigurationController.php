<?php

namespace App\Http\Controllers;

use App\Http\Requests\MailConfigurationRequest;
use App\Models\MailConfiguration;
use App\Models\MailRecipientLog;
use App\Models\User;
use App\Services\MailConfigurationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MailConfigurationController extends Controller
{
    public function __construct(private MailConfigurationService $service)
    {}

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MailConfiguration::with('user')
                ->whereHas('user', function($q) {
                    $q->where('type', '1');
                });
            
            if ($request->debtor_id) {
                $data->where('user_id', $request->debtor_id);
            }
            
            $data->select('mail_configurations.*');
            
            return DataTables::of($data)
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" value="'.$row->id.'" class="item-checkbox">';
                })
                ->addColumn('corporate_debtor', function($row) {
                    return $row->user->name;
                })
                ->addColumn('send_type', function($row) {
                    return is_string($row->send_type) ? $row->send_type : $row->send_type->value;
                })
                ->editColumn('scheduled_at', function($row) {
                    return $row->scheduled_at ? Carbon::parse($row->scheduled_at)->format('d-m-Y H:i') : '-';
                })
                ->addColumn('action', function($row) {
                    $button = '';
                    if ($row->status == 2) {
                        $button = '<a href="'.route('mail-configurations.report', $row->id).'" class="btn btn-sm btn-success">Report</a>';
                    }
                    $deleteButton = '<button type="button" class="btn btn-sm btn-danger" onclick="deleteItem('.$row->id.')">Delete</button>';

                    if ($row->status == 1) {
                        $editLink = '<a href="'.route('mail-configurations.edit', $row->id).'" class="btn btn-sm btn-info">Edit</a>';
                        $button .= $editLink;
                    }

                    $button .= $deleteButton;
                    return $button;
                })
                ->rawColumns(['checkbox', 'action'])
                ->make(true);
        }
        
        return view('app.mail-configurations.list');
    }

    public function create()
    {
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.mail-configurations.addedit', compact('corporateDebtors'));
    }

    public function store(MailConfigurationRequest $request)
    {
        $validated = $request->validated();
        $attachments = $validated['attachments'] ?? [];
        unset($validated['attachments']);
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }
        
        $mailConfig = MailConfiguration::create($validated);
        
        if (!empty($attachments)) {
            $this->service->syncAttachments($mailConfig, $attachments);
        }

        $isDispatched = $this->service->dispatchIfNow($mailConfig);

        return redirect()->route('mail-configurations.index')->with('success', 'Mail Configuration created successfully. ' . ($isDispatched ? 'Emails are being sent.' : 'Emails will be sent at scheduled time.'));
    }

    public function edit(MailConfiguration $mailConfiguration)
    {
        if ($mailConfiguration->status == 1 || $mailConfiguration->status == 2) {
            return redirect()->route('mail-configurations.index')->with('error', 'Cannot edit mail configuration that is already processing.');
        }
        
        $mailConfiguration->load('configurationAttachments');
        $corporateDebtors = User::where('type', '1')->where('is_active', 1)->get();
        return view('app.mail-configurations.addedit', compact('mailConfiguration', 'corporateDebtors'));
    }

    public function update(MailConfigurationRequest $request, MailConfiguration $mailConfiguration)
    {
        $validated = $request->validated();
        $attachments = $validated['attachments'] ?? [];
        unset($validated['attachments']);
        
        if (auth()->user()->type == '1') {
            $validated['user_id'] = auth()->id();
        }
        
        $mailConfiguration->update($validated);
        
        if (!empty($attachments)) {
            $this->service->syncAttachments($mailConfiguration, $attachments);
        } else {
            $mailConfiguration->configurationAttachments()->delete();
        }

        $isDispatched = $this->service->dispatchIfNow($mailConfiguration);

        return redirect()->route('mail-configurations.index')->with('success', 'Mail Configuration updated successfully. ' . ($isDispatched ? 'Emails are being sent.' : ''));
    }

    public function destroy(MailConfiguration $mailConfiguration)
    {
        if ($mailConfiguration->status == 1) {
            return redirect()->route('mail-configurations.index')->with('error', 'Cannot delete mail configuration that is already processing.');
        }
        $mailConfiguration->delete();

        return redirect()->route('mail-configurations.index')->with('success', 'Mail Configuration deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mail_configurations,id',
        ]);

        $query = MailConfiguration::whereIn('id', $validated['ids'])
            ->where('status', 0)
            ->whereHas('user', function($q) {
                $q->where('type', '1');
            });
        
        if ($request->debtor_id) {
            $query->where('user_id', $request->debtor_id);
        }
        
        $query->delete();

        return response()->json(['success' => true]);
    }

    public function report(Request $request, MailConfiguration $mailConfiguration)
    {
        if ($request->ajax()) {
            $data = $mailConfiguration->recipientLogs()->with('contact');
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('contact_name', function($row) {
                    return $row->contact->name;
                })
                ->addColumn('contact_email', function($row) {
                    return $row->contact->email;
                })
                ->editColumn('status', function($row) {
                    $status = is_string($row->status) ? $row->status : $row->status->value;
                    $badge = $status === 'SENT' ? 'success' : 'danger';
                    return '<span class="badge badge-'.$badge.'">'.$status.'</span>';
                })
                ->editColumn('sent_at', function($row) {
                    return $row->sent_at ? (is_string($row->sent_at) ? $row->sent_at : $row->sent_at->format('d-m-Y H:i:s')) : '-';
                })
                ->editColumn('delivered_at', function($row) {
                    return $row->delivered_at ? (is_string($row->delivered_at) ? $row->delivered_at : $row->delivered_at->format('d-m-Y H:i:s')) : '-';
                })
                ->editColumn('message_id', function($row) {
                    return $row->message_id ?? '-';
                })
                ->editColumn('error_message', function($row) {
                    return $row->error_message ?? '-';
                })
                ->editColumn('bounce_reason', function($row) {
                    return $row->bounce_reason ?? '-';
                })
                ->addColumn('action', function($row) {
                    return '<button type="button" class="btn btn-sm btn-primary" onclick="resendMail('.$row->id.')">Resend</button>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        
        $mailConfiguration->load('user');
        
        $stats = [
            'total' => $mailConfiguration->recipientLogs()->count(),
            'sent' => $mailConfiguration->recipientLogs()->where('status', 'SENT')->count(),
            'failed' => $mailConfiguration->recipientLogs()->where('status', 'FAILED')->count(),
        ];
        
        return view('app.mail-configurations.report', compact('mailConfiguration', 'stats'));
    }

    public function resendMail(Request $request)
    {
        $validated = $request->validate([
            'log_id' => 'required|exists:mail_recipient_logs,id',
        ]);

        try {
            $log = MailRecipientLog::findOrFail($validated['log_id']);
            app(\App\Services\MailService::class)->resendMail($log);
            return response()->json(['success' => true, 'message' => 'Mail resent successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
