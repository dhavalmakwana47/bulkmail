<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\DebtorAttachment;
use App\Models\MailConfiguration;
use App\Models\MailRecipientLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];
        $user = auth()->user();
        
        if ($user->type == '1') {
            // Basic counts
            $data['contacts'] = Contact::where('user_id', $user->id)->count();
            $data['mail_configs'] = MailConfiguration::where('user_id', $user->id)->count();
            $data['attachments'] = DebtorAttachment::where('user_id', $user->id)->count();
            
            // Email metrics
            $mailLogs = MailRecipientLog::whereHas('mailConfiguration', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
            
            $data['emails_sent'] = $mailLogs->count();
            $delivered = $mailLogs->where('status', 'delivered')->count();
            $data['delivery_rate'] = $data['emails_sent'] > 0 ? ($delivered / $data['emails_sent']) * 100 : 0;
            
            // Recent campaigns
            $data['recent_campaigns'] = MailConfiguration::where('user_id', $user->id)
                ->withCount(['recipientLogs as recipients_count'])
                ->latest()
                ->take(5)
                ->get();
            
            // Chart data for last 30 days
            $chartData = [];
            $chartLabels = [];
            
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $chartLabels[] = $date->format('M d');
                
                $count = MailRecipientLog::whereHas('mailConfiguration', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->whereDate('created_at', $date)->count();
                
                $chartData[] = $count;
            }
            
            $data['chart_labels'] = $chartLabels;
            $data['chart_data'] = $chartData;
            
            // Status distribution
            $totalLogs = $mailLogs->count();
            if ($totalLogs > 0) {
                $delivered = $mailLogs->where('status', 'delivered')->count();
                $pending = $mailLogs->where('status', 'pending')->count();
                $failed = $mailLogs->where('status', 'failed')->count();
                
                $data['status_data'] = [
                    round(($delivered / $totalLogs) * 100),
                    round(($pending / $totalLogs) * 100),
                    round(($failed / $totalLogs) * 100)
                ];
            } else {
                $data['status_data'] = [0, 0, 0];
            }
            
            // Storage calculation (count of attachments)
            $data['storage_used'] = DebtorAttachment::where('user_id', $user->id)->count();
        }
  
        return view('app.index', $data);
    }
}