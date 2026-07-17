<x-guest-layout>
    <x-slot name="title">Forgot Password</x-slot>

    <h5 class="text-center mb-2 fw-semibold" style="color:#1e293b;">Forgot your password?</h5>
    <p class="text-center mb-4" style="font-size:0.85rem;color:#64748b;">
        Enter your email and we'll send you a password reset link.
    </p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus
                   placeholder="you@company.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary fw-semibold">
                Send Reset Link
            </button>
        </div>
    </form>

    <p class="text-center mt-4 mb-0" style="font-size:0.875rem;color:#64748b;">
        <a href="{{ route('login') }}" class="auth-link">
            <i class="bi bi-arrow-left me-1"></i> Back to login
        </a>
    </p>
</x-guest-layout>
