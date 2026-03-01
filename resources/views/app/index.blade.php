@extends('app.layout.app')
@section('page_title')
    Dashboard
@endsection

@section('header-script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content-body')
    @if(auth()->user()->type == '1')
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($contacts ?? 0) }}</h3>
                    <p>Total Contacts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('contacts.index') }}" class="small-box-footer">Manage Contacts <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($mail_configs ?? 0) }}</h3>
                    <p>Mail Campaigns</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <a href="{{ route('mail-configurations.index') }}" class="small-box-footer">View Campaigns <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($emails_sent ?? 0) }}</h3>
                    <p>Emails Sent</p>
                </div>
                <div class="icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <a href="{{ route('mail-configurations.index') }}" class="small-box-footer">View Reports <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="small-box-inner">
                    <h3>{{ number_format($delivery_rate ?? 0, 1) }}%</h3>
                    <p>Delivery Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="{{ route('activity-logs.index') }}" class="small-box-footer">View Analytics <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Email Campaign Performance (Last 30 Days)</h3>
                </div>
                <div class="card-body">
                    <canvas id="emailChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Email Status Distribution</h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Recent Campaign Activity</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Campaign</th>
                                    <th>Recipients</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_campaigns ?? [] as $campaign)
                                <tr>
                                    <td>{{ $campaign->subject }}</td>
                                    <td><span class="badge badge-info">{{ $campaign->recipients_count }}</span></td>
                                    <td>
                                        @if($campaign->status == 'sent' || $campaign->status == 1)
                                            <span class="badge badge-success">Completed</span>
                                        @elseif($campaign->status == 'draft' || $campaign->status == 0)
                                            <span class="badge badge-secondary">Draft</span>
                                        @elseif($campaign->status == 'processing')
                                            <span class="badge badge-warning">Processing</span>
                                        @else
                                            <span class="badge badge-info">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $campaign->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($campaign->status == 'sent' || $campaign->status == 1)
                                            <a href="{{ route('mail-configurations.report', $campaign->id) }}" class="btn btn-sm btn-outline-success">Report</a>
                                        @else
                                            <a href="{{ route('mail-configurations.edit', $campaign->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No recent campaigns</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('mail-configurations.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create New Campaign
                        </a>
                        <a href="{{ route('contacts.import') }}" class="btn btn-success">
                            <i class="fas fa-upload"></i> Import Contacts
                        </a>
                        <a href="{{ route('contacts.create') }}" class="btn btn-info">
                            <i class="fas fa-user-plus"></i> Add Contact
                        </a>
                        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-history"></i> View Activity Logs
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h3 class="card-title">System Status</h3>
                </div>
                <div class="card-body">
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-success"><i class="fas fa-server"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Mail Service</span>
                            <span class="info-box-number">Online</span>
                        </div>
                    </div>
                    <div class="info-box mb-2">
                        <span class="info-box-icon bg-info"><i class="fas fa-database"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Attachments</span>
                            <span class="info-box-number">{{ $storage_used ?? '0' }} Files</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @elseif(auth()->user()->type == '0' || auth()->user()->type == 'admin')
    <!-- Admin Dashboard -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ \App\Models\User::where('type', '1')->count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <a href="{{ route('corporate-debtors.index') }}" class="small-box-footer">Manage Users <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ \App\Models\MailConfiguration::count() }}</h3>
                    <p>Total Campaigns</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <a href="{{ route('mail-configurations.index') }}" class="small-box-footer">View All Campaigns <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ \App\Models\Contact::count() }}</h3>
                    <p>Total Contacts</p>
                </div>
                <div class="icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <a href="{{ route('contacts.index') }}" class="small-box-footer">View All Contacts <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\ActivityLog::count() }}</h3>
                    <p>System Activities</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
                <a href="{{ route('activity-logs.index') }}" class="small-box-footer">View Activity Logs <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Admin Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('corporate-debtors.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-user-plus"></i> Add New User
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('activity-logs.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-chart-bar"></i> System Reports
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('activity-logs.export') }}" class="btn btn-success btn-block">
                                <i class="fas fa-download"></i> Export Logs
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('corporate-debtors.index') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-cogs"></i> User Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <h4>Welcome to Bulk Mail System</h4>
                    <p class="text-muted">Contact your administrator for access permissions.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('footer-script')
<script>
@if(auth()->user()->type == '1')
// Email Performance Chart
const emailCtx = document.getElementById('emailChart').getContext('2d');
new Chart(emailCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chart_labels ?? []) !!},
        datasets: [{
            label: 'Emails Sent',
            data: {!! json_encode($chart_data ?? []) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Delivered', 'Pending', 'Failed'],
        datasets: [{
            data: {!! json_encode($status_data ?? [70, 20, 10]) !!},
            backgroundColor: ['#28a745', '#ffc107', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
@endif
</script>
@endsection
