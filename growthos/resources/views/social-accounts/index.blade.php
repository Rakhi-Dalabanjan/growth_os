<x-app-layout>
    <x-slot name="title">Social Accounts</x-slot>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="font-size:1.4rem;color:#1e293b;">
                Social Account Connections
            </h2>
            <p class="mb-0" style="color:#64748b;font-size:0.875rem;">
                Connect and manage your social platform integrations securely.
            </p>
        </div>
        <div>
            <a href="{{ route('social-accounts.connect') }}" class="btn btn-primary d-flex align-items-center gap-2" style="background:#2563eb;border-color:#2563eb;">
                <i class="bi bi-plus-lg"></i> Connect Facebook Page
            </a>
        </div>
    </div>

    <!-- Alert details if coming soon is triggered -->
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            {{ session('info') }}
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card" style="border-radius:12px;overflow:hidden;">
        <div class="card-header bg-white py-3">
            <span class="fw-semibold" style="font-size:0.95rem;color:#1e293b;">
                <i class="bi bi-share me-2 text-primary"></i>Connected Accounts
            </span>
        </div>
        <div class="card-body p-0">
            @if($socialAccounts->isEmpty())
                <div class="text-center py-5">
                    <div style="width:64px;height:64px;background:#f1f5f9;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                        <i class="bi bi-share text-secondary" style="font-size:2rem;"></i>
                    </div>
                    <h5 class="fw-semibold text-dark mb-1">No Accounts Connected</h5>
                    <p class="text-muted mb-4" style="font-size:0.85rem;max-width:380px;margin:0 auto;">
                        Connect your Facebook Page or Instagram Business Account to start scheduling posts and viewing metrics.
                    </p>
                    <a href="{{ route('social-accounts.connect') }}" class="btn btn-primary btn-sm">
                        Connect Your First Page
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" style="font-size:0.875rem;">
                        <thead>
                            <tr style="background:#f8fafc;">
                                <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Platform</th>
                                <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Account / Page Name</th>
                                <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Business ID / Details</th>
                                <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Connected At</th>
                                <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Status</th>
                                <th class="px-4 py-3 text-end" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;color:#94a3b8;border-bottom:1px solid #e2e8f0;width:240px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($socialAccounts as $account)
                                <tr>
                                    <!-- Platform -->
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($account->platform === 'facebook')
                                                <div style="width:32px;height:32px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <i class="bi bi-facebook" style="color:#1877f2;font-size:1.1rem;"></i>
                                                </div>
                                                <span class="fw-medium">Facebook</span>
                                            @elseif($account->platform === 'instagram')
                                                <div style="width:32px;height:32px;background:#fff0f0;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <i class="bi bi-instagram" style="color:#e1306c;font-size:1.1rem;"></i>
                                                </div>
                                                <span class="fw-medium">Instagram</span>
                                            @else
                                                <div style="width:32px;height:32px;background:#f1f5f9;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                                    <i class="bi bi-globe" style="color:#64748b;font-size:1.1rem;"></i>
                                                </div>
                                                <span class="fw-medium">{{ ucfirst($account->platform) }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Account Name -->
                                    <td class="px-4 py-3">
                                        <div class="fw-semibold text-dark">{{ $account->account_name }}</div>
                                        @if($account->platform === 'instagram')
                                            <div class="text-muted" style="font-size:0.75rem;">Linked Page: {{ $account->page_name }}</div>
                                        @endif
                                    </td>

                                    <!-- Business ID / Details -->
                                    <td class="px-4 py-3 text-muted" style="font-size:0.8rem;">
                                        @if($account->platform === 'facebook')
                                            <div><strong>Page ID:</strong> <code>{{ $account->page_id }}</code></div>
                                        @elseif($account->platform === 'instagram')
                                            <div><strong>IG Business ID:</strong> <code>{{ $account->instagram_business_id }}</code></div>
                                        @endif
                                    </td>

                                    <!-- Connected At -->
                                    <td class="px-4 py-3 text-muted">
                                        {{ $account->connected_at ? $account->connected_at->format('M d, Y H:i') : '—' }}
                                    </td>

                                    <!-- Status Badge -->
                                    <td class="px-4 py-3">
                                        @php
                                            $status = strtolower($account->status);
                                            $badgeClass = 'bg-secondary';
                                            if ($status === 'connected') $badgeClass = 'bg-success';
                                            elseif ($status === 'disconnected') $badgeClass = 'bg-secondary';
                                            elseif ($status === 'expired') $badgeClass = 'bg-warning text-dark';
                                            elseif ($status === 'error') $badgeClass = 'bg-danger';
                                            elseif ($status === 'pending') $badgeClass = 'bg-info text-dark';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-2.5 py-1.5" style="font-size:0.75rem;">
                                            {{ ucfirst($account->status) }}
                                        </span>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex align-items-center justify-content-end gap-1">
                                            <!-- Sync Button -->
                                            <form action="{{ route('social-accounts.sync', $account) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-light text-dark border-secondary-subtle" title="Sync Account">
                                                    <i class="bi bi-arrow-repeat text-muted"></i> Sync
                                                </button>
                                            </form>

                                            @if($status === 'connected')
                                                <!-- Disconnect Button -->
                                                <form action="{{ route('social-accounts.disconnect', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to disconnect this account? Sensitive tokens will be removed.');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-x-circle"></i> Disconnect
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Reconnect Button -->
                                                <a href="{{ route('social-accounts.connect') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-bootstrap-reboot"></i> Reconnect
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
