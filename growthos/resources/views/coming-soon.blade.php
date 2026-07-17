<x-app-layout>
    <x-slot name="title">{{ $feature ?? 'Feature' }} — Coming Soon</x-slot>

    <div class="d-flex align-items-center justify-content-center" style="min-height:60vh;">
        <div class="text-center" style="max-width:480px;">
            <div style="width:80px;height:80px;background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;box-shadow:0 8px 24px rgba(37,99,235,0.3);">
                <i class="bi bi-stars text-white" style="font-size:2rem;"></i>
            </div>
            <h2 class="fw-bold mb-2" style="color:#1e293b;font-size:1.5rem;">{{ $feature ?? 'Feature' }}</h2>
            <p class="mb-1" style="font-size:1.1rem;font-weight:600;color:#2563eb;">Coming Soon</p>
            <p class="mb-4" style="color:#64748b;font-size:0.9rem;line-height:1.6;">
                We're working hard to bring you this feature. It will be part of the full GrowthOS AI Social Media Operating System.
            </p>
            <div class="d-flex gap-2 justify-content-center">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-grid-1x2-fill me-1"></i> Back to Dashboard
                </a>
                <a href="{{ route('organization.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-building me-1"></i> Organization
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
