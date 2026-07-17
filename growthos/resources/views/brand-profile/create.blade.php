<x-app-layout>
    <x-slot name="title">Create Brand Profile</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Create Brand Profile</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h2 class="fw-bold mb-1" style="font-size:1.4rem;color:#1e293b;">Set Up Brand Profile</h2>
                    <p class="mb-0" style="color:#64748b;font-size:0.875rem;">
                        Define your brand parameters. This will guide content and strategic AI generation.
                    </p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger d-flex align-items-start gap-2 mb-4">
                    <i class="bi bi-exclamation-triangle-fill mt-1"></i>
                    <div>
                        <div class="fw-semibold mb-1">Please fix the following errors:</div>
                        <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('brand-profile.store') }}" method="POST">
                @csrf

                <!-- SECTION 1: Brand Identity -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#eff6ff;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-tag-fill text-primary"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">1. Brand Identity</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="brand_name" class="form-label">Brand Name <span class="text-danger">*</span></label>
                                <input type="text" id="brand_name" name="brand_name" 
                                       class="form-control @error('brand_name') is-invalid @enderror" 
                                       value="{{ old('brand_name') }}" required placeholder="Acme Corp">
                                @error('brand_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="tagline" class="form-label">Tagline</label>
                                <input type="text" id="tagline" name="tagline" 
                                       class="form-control @error('tagline') is-invalid @enderror" 
                                       value="{{ old('tagline') }}" placeholder="Simplifying complex processes.">
                                @error('tagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="business_description" class="form-label">Business Description</label>
                                <textarea id="business_description" name="business_description" rows="3" 
                                          class="form-control @error('business_description') is-invalid @enderror"
                                          placeholder="Provide a brief summary of what your business does..."></textarea>
                                @error('business_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Business -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-briefcase-fill text-success"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">2. Business & Market</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mission" class="form-label">Mission</label>
                                <textarea id="mission" name="mission" rows="2" 
                                          class="form-control @error('mission') is-invalid @enderror"
                                          placeholder="What is your business's core purpose?"></textarea>
                                @error('mission') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="vision" class="form-label">Vision</label>
                                <textarea id="vision" name="vision" rows="2" 
                                          class="form-control @error('vision') is-invalid @enderror"
                                          placeholder="Where does your business want to go in the future?"></textarea>
                                @error('vision') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="primary_market" class="form-label">Primary Market</label>
                                <input type="text" id="primary_market" name="primary_market" 
                                       class="form-control @error('primary_market') is-invalid @enderror" 
                                       value="{{ old('primary_market') }}" placeholder="North America, B2B SaaS, Retail, etc.">
                                @error('primary_market') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Audience -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#fef3c7;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-people-fill text-warning"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">3. Target Audience</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-2">
                            <label for="target_audience" class="form-label">Audience Demographics & Psychographics</label>
                            <textarea id="target_audience" name="target_audience" rows="3" 
                                      class="form-control @error('target_audience') is-invalid @enderror"
                                      placeholder="Describe your ideal customers (e.g. Small business owners, aged 30-50, seeking budget friendly automation)..."></textarea>
                            @error('target_audience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Brand Voice -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#fdf4ff;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-chat-quote-fill text-purple" style="color:#a855f7;"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">4. Brand Voice</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="brand_tone" class="form-label">Brand Tone</label>
                                <input type="text" id="brand_tone" name="brand_tone" 
                                       class="form-control @error('brand_tone') is-invalid @enderror" 
                                       value="{{ old('brand_tone') }}" placeholder="Friendly, Professional, Bold, Quirky">
                                @error('brand_tone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="formality" class="form-label">Formality Level</label>
                                <select id="formality" name="formality" class="form-select @error('formality') is-invalid @enderror">
                                    <option value="">— Select Formality —</option>
                                    <option value="Casual" {{ old('formality') === 'Casual' ? 'selected' : '' }}>Casual / Friendly</option>
                                    <option value="Neutral" {{ old('formality') === 'Neutral' ? 'selected' : '' }}>Neutral</option>
                                    <option value="Formal" {{ old('formality') === 'Formal' ? 'selected' : '' }}>Formal / Corporate</option>
                                </select>
                                @error('formality') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="language" class="form-label">Language</label>
                                <input type="text" id="language" name="language" 
                                       class="form-control @error('language') is-invalid @enderror" 
                                       value="{{ old('language', 'English') }}" placeholder="English (US), Spanish, Bilingual">
                                @error('language') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="emoji_style" class="form-label">Emoji Style</label>
                                <select id="emoji_style" name="emoji_style" class="form-select @error('emoji_style') is-invalid @enderror">
                                    <option value="">— Select Emoji Style —</option>
                                    <option value="None" {{ old('emoji_style') === 'None' ? 'selected' : '' }}>None (No Emojis)</option>
                                    <option value="Minimal" {{ old('emoji_style') === 'Minimal' ? 'selected' : '' }}>Minimal (1-2 per post)</option>
                                    <option value="Moderate" {{ old('emoji_style') === 'Moderate' ? 'selected' : '' }}>Moderate (Used contextually)</option>
                                    <option value="Heavy" {{ old('emoji_style') === 'Heavy' ? 'selected' : '' }}>Heavy (Expressive & bold)</option>
                                </select>
                                @error('emoji_style') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 5: Brand Style -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#ecfdf5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-palette-fill text-teal" style="color:#0d9488;"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">5. Visual Identity & Brand Style</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="primary_color" class="form-label">Primary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control-color border-end-0 rounded-start" 
                                           id="primary_color_picker" value="#2563eb" title="Choose color"
                                           oninput="document.getElementById('primary_color').value = this.value">
                                    <input type="text" id="primary_color" name="primary_color" 
                                           class="form-control @error('primary_color') is-invalid @enderror" 
                                           value="{{ old('primary_color', '#2563eb') }}" placeholder="#2563eb"
                                           onchange="document.getElementById('primary_color_picker').value = this.value">
                                </div>
                                @error('primary_color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="secondary_color" class="form-label">Secondary Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control-color border-end-0 rounded-start" 
                                           id="secondary_color_picker" value="#64748b" title="Choose color"
                                           oninput="document.getElementById('secondary_color').value = this.value">
                                    <input type="text" id="secondary_color" name="secondary_color" 
                                           class="form-control @error('secondary_color') is-invalid @enderror" 
                                           value="{{ old('secondary_color', '#64748b') }}" placeholder="#64748b"
                                           onchange="document.getElementById('secondary_color_picker').value = this.value">
                                </div>
                                @error('secondary_color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="accent_color" class="form-label">Accent Color</label>
                                <div class="input-group">
                                    <input type="color" class="form-control-color border-end-0 rounded-start" 
                                           id="accent_color_picker" value="#f59e0b" title="Choose color"
                                           oninput="document.getElementById('accent_color').value = this.value">
                                    <input type="text" id="accent_color" name="accent_color" 
                                           class="form-control @error('accent_color') is-invalid @enderror" 
                                           value="{{ old('accent_color', '#f59e0b') }}" placeholder="#f59e0b"
                                           onchange="document.getElementById('accent_color_picker').value = this.value">
                                </div>
                                @error('accent_color') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="primary_font" class="form-label">Primary Font</label>
                                <input type="text" id="primary_font" name="primary_font" 
                                       class="form-control @error('primary_font') is-invalid @enderror" 
                                       value="{{ old('primary_font') }}" placeholder="Inter, Roboto, sans-serif">
                                @error('primary_font') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_font" class="form-label">Secondary Font</label>
                                <input type="text" id="secondary_font" name="secondary_font" 
                                       class="form-control @error('secondary_font') is-invalid @enderror" 
                                       value="{{ old('secondary_font') }}" placeholder="Georgia, Merriweather, serif">
                                @error('secondary_font') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 6: Marketing -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#fef2f2;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-megaphone-fill text-danger"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">6. Marketing Parameters</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="primary_cta" class="form-label">Primary Call to Action (CTA)</label>
                                <input type="text" id="primary_cta" name="primary_cta" 
                                       class="form-control @error('primary_cta') is-invalid @enderror" 
                                       value="{{ old('primary_cta') }}" placeholder="Sign Up Free, Book a Demo">
                                @error('primary_cta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="secondary_cta" class="form-label">Secondary Call to Action (CTA)</label>
                                <input type="text" id="secondary_cta" name="secondary_cta" 
                                       class="form-control @error('secondary_cta') is-invalid @enderror" 
                                       value="{{ old('secondary_cta') }}" placeholder="Learn More, Read Case Studies">
                                @error('secondary_cta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="preferred_words" class="form-label">Preferred Words / Phrases</label>
                                <input type="text" id="preferred_words" name="preferred_words" 
                                       class="form-control @error('preferred_words') is-invalid @enderror" 
                                       value="{{ old('preferred_words') }}" placeholder="seamless, scalable, cutting-edge (comma-separated)">
                                <div class="form-text">Comma-separated terms that align with your branding.</div>
                                @error('preferred_words') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="restricted_words" class="form-label">Restricted Words / Jargon to Avoid</label>
                                <input type="text" id="restricted_words" name="restricted_words" 
                                       class="form-control @error('restricted_words') is-invalid @enderror" 
                                       value="{{ old('restricted_words') }}" placeholder="cheap, best-in-class, buy now (comma-separated)">
                                <div class="form-text">Comma-separated terms you want to avoid in content generation.</div>
                                @error('restricted_words') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="competitor_names" class="form-label">Competitors</label>
                                <input type="text" id="competitor_names" name="competitor_names" 
                                       class="form-control @error('competitor_names') is-invalid @enderror" 
                                       value="{{ old('competitor_names') }}" placeholder="Competitor A, Competitor B (comma-separated)">
                                <div class="form-text">Comma-separated competitor brands.</div>
                                @error('competitor_names') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 7: Compliance -->
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center gap-2">
                        <div style="width:30px;height:30px;background:#f8fafc;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-shield-check text-secondary"></i>
                        </div>
                        <h5 class="mb-0 fw-semibold" style="font-size:0.95rem;">7. Compliance & Claims</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="approved_claims" class="form-label">Approved Value Claims</label>
                                <textarea id="approved_claims" name="approved_claims" rows="2" 
                                          class="form-control @error('approved_claims') is-invalid @enderror"
                                          placeholder="FDA compliant, ISO 9001 certified, Built in USA (comma-separated)"></textarea>
                                <div class="form-text">Claims or certifications that can be safely made.</div>
                                @error('approved_claims') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="restricted_claims" class="form-label">Restricted / Forbidden Claims</label>
                                <textarea id="restricted_claims" name="restricted_claims" rows="2" 
                                          class="form-control @error('restricted_claims') is-invalid @enderror"
                                          placeholder="Guaranteed cure, 100% risk free, Beats all rivals (comma-separated)"></textarea>
                                <div class="form-text">Claims that must never be generated.</div>
                                @error('restricted_claims') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="legal_disclaimer" class="form-label">Legal Disclaimer / Boilerplate</label>
                            <textarea id="legal_disclaimer" name="legal_disclaimer" rows="3" 
                                      class="form-control @error('legal_disclaimer') is-invalid @enderror"
                                      placeholder="Standard footer legal boilerplate or disclosures..."></textarea>
                            @error('legal_disclaimer') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit / Actions -->
                <div class="card mb-4" style="background:#f8fafc;border-style:dashed;">
                    <div class="card-body p-4 d-flex justify-content-end gap-2">
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i> Save Brand Profile
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
