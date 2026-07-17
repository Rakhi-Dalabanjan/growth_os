<x-app-layout>
    <x-slot name="title">Create Organization</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Create Organization</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-building" style="color:#2563eb;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.95rem;">Set Up Your Organization</div>
                            <div style="font-size:0.78rem;color:#94a3b8;">This information helps GrowthOS personalize your experience.</div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
                            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                            <div>
                                <div class="fw-semibold mb-1">Please fix the following errors:</div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li style="font-size:0.85rem;">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('organization.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Section: Basic Info -->
                        <h6 class="fw-semibold mb-3 pb-2" style="color:#374151;border-bottom:1px solid #e2e8f0;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.8px;">
                            Basic Information
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required
                                       placeholder="Acme Corporation">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="industry" class="form-label">Industry</label>
                                <select id="industry" name="industry"
                                        class="form-select @error('industry') is-invalid @enderror">
                                    <option value="">— Select Industry —</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry }}" {{ old('industry') === $industry ? 'selected' : '' }}>
                                            {{ $industry }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('industry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" id="website" name="website"
                                       class="form-control @error('website') is-invalid @enderror"
                                       value="{{ old('website') }}"
                                       placeholder="https://example.com">
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Section: Contact -->
                        <h6 class="fw-semibold mb-3 pb-2" style="color:#374151;border-bottom:1px solid #e2e8f0;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.8px;">
                            Contact Information
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="business_email" class="form-label">Business Email</label>
                                <input type="email" id="business_email" name="business_email"
                                       class="form-control @error('business_email') is-invalid @enderror"
                                       value="{{ old('business_email') }}"
                                       placeholder="info@company.com">
                                @error('business_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       value="{{ old('phone') }}"
                                       placeholder="+1 (555) 000-0000">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Section: Location -->
                        <h6 class="fw-semibold mb-3 pb-2" style="color:#374151;border-bottom:1px solid #e2e8f0;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.8px;">
                            Location & Timezone
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="country" class="form-label">Country</label>
                                <select id="country" name="country"
                                        class="form-select @error('country') is-invalid @enderror">
                                    <option value="">— Select Country —</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country }}" {{ old('country') === $country ? 'selected' : '' }}>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select id="timezone" name="timezone"
                                        class="form-select @error('timezone') is-invalid @enderror">
                                    <option value="">— Select Timezone —</option>
                                    @foreach($timezones as $tz)
                                        <option value="{{ $tz }}" {{ old('timezone', 'UTC') === $tz ? 'selected' : '' }}>
                                            {{ $tz }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('timezone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Section: Branding -->
                        <h6 class="fw-semibold mb-3 pb-2" style="color:#374151;border-bottom:1px solid #e2e8f0;font-size:0.85rem;text-transform:uppercase;letter-spacing:0.8px;">
                            Branding
                        </h6>

                        <div class="mb-4">
                            <label for="logo" class="form-label">Company Logo</label>
                            <input type="file" id="logo" name="logo"
                                   class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                            <div class="form-text">JPEG, PNG, GIF, WEBP — Max 2MB</div>
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 justify-content-end pt-2" style="border-top:1px solid #e2e8f0;">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Create Organization
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
