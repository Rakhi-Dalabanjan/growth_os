<x-guest-layout>
    <x-slot name="title">Login</x-slot>

    <h5 class="text-center mb-4 fw-semibold" style="color:#1e293b;">Sign in to your account</h5>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success d-flex align-items-center gap-2 mb-3" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus autocomplete="username"
                   placeholder="you@company.com">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <label for="password" class="form-label mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="auth-link" href="{{ route('password.request') }}" style="font-size:0.8rem;">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password"
                   class="form-control mt-1 @error('password') is-invalid @enderror"
                   required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input id="remember_me" type="checkbox" name="remember" class="form-check-input">
            <label class="form-check-label" for="remember_me" style="font-size:0.875rem;">
                Remember me
            </label>
        </div>

        <div class="d-grid">
            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                Sign In
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="text-center mt-4 mb-0" style="font-size:0.875rem;color:#64748b;">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-link fw-semibold">Create one</a>
        </p>
    @endif
</x-guest-layout>
