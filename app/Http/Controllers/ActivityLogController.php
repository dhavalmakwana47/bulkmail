<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ActivityLog::with('user')
                ->when($request->module, fn($q) => $q->module($request->module))
                ->when($request->action, fn($q) => $q->action($request->action))
                ->when($request->user_id, fn($q) => $q->user($request->user_id))
                ->when($request->loggable_type, fn($q) => $q->loggableType($request->loggable_type))
                ->when($request->ip_address, fn($q) => $q->ipAddress($request->ip_address))
                ->when($request->date_from && $request->date_to, fn($q) => $q->dateRange($request->date_from, $request->date_to))
                ->latest();
            
            if ($request->debtor_id) {
                $query->where('user_id', $request->debtor_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('user_name', fn($row) => $row->user?->name ?? 'System')
                ->editColumn('action', fn($row) => $row->action)
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i:s'))
                ->make(true);
        }

        $modules = ActivityLog::distinct()->pluck('module');
        $users = User::select('id', 'name')->get();
        $loggableTypes = ActivityLog::distinct()->pluck('loggable_type');

        return view('app.activity-logs.list', compact('modules', 'users', 'loggableTypes'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user')
            ->when($request->module, fn($q) => $q->module($request->module))
            ->when($request->action, fn($q) => $q->action($request->action))
            ->when($request->user_id, fn($q) => $q->user($request->user_id))
            ->when($request->date_from && $request->date_to, fn($q) => $q->dateRange($request->date_from, $request->date_to))
            ->latest();

        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';

        return new StreamedResponse(function() use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Module', 'Action', 'User', 'Model Type', 'Model ID', 'IP Address', 'Date']);
            
            $query->chunk(1000, function($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->module,
                        $log->action,
                        $log->user?->name ?? 'System',
                        $log->loggable_type,
                        $log->loggable_id,
                        $log->ip_address,
                        $log->created_at->format('d-m-Y H:i:s')
                    ]);
                }
            });
            
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
