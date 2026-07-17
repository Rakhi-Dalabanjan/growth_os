<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">

            <!-- Profile Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-person" style="color:#2563eb;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.95rem;">Profile Information</div>
                            <div style="font-size:0.78rem;color:#94a3b8;">Update your name and email address.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    @if(session('success') && !session('password_success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name"
                                   class="form-control @error('name', 'updateProfile') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}" required>
                            @error('name', 'updateProfile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email"
                                   class="form-control @error('email', 'updateProfile') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}" required>
                            @error('email', 'updateProfile')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Save Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-shield-lock" style="color:#d97706;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.95rem;">Change Password</div>
                            <div style="font-size:0.78rem;color:#94a3b8;">Use a strong password of at least 8 characters.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    @if(session('password_success'))
                        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                            <i class="bi bi-check-circle-fill"></i> {{ session('password_success') }}
                            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('settings.password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" id="current_password" name="current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   required autocomplete="current-password"
                                   placeholder="Your current password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   required autocomplete="new-password"
                                   placeholder="Min. 8 characters">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control"
                                   required autocomplete="new-password"
                                   placeholder="Repeat new password">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning text-white fw-semibold">
                                <i class="bi bi-shield-check me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Account Info Card -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-info-circle" style="color:#16a34a;"></i>
                        </div>
                        <div class="fw-semibold" style="font-size:0.95rem;">Account Information</div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Account Created</div>
                            <div style="font-weight:500;color:#1e293b;font-size:0.875rem;">{{ auth()->user()->created_at->format('F j, Y') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Organization</div>
                            <div style="font-weight:500;color:#1e293b;font-size:0.875rem;">
                                @if(auth()->user()->hasOrganization())
                                    <a href="{{ route('organization.show', auth()->user()->organization_id) }}" class="text-decoration-none">
                                        {{ auth()->user()->organization->name }}
                                    </a>
                                @else
                                    <span class="text-muted">Not set up</span>
                                    <a href="{{ route('organization.create') }}" class="btn btn-sm btn-outline-primary ms-2" style="font-size:0.75rem;">Setup</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
