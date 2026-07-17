<x-app-layout>
    <x-slot name="title">AI Gateway Control Panel</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">AI Gateway</li>
        </ol>
    </nav>

    <!-- Alert notifications -->
    <div id="alert-container"></div>

    <!-- Gateway Health & Active Provider Row -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Gateway Connection</span>
                            <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-shield-check" style="color:#16a34a; font-size: 1.1rem;"></i>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div id="gateway-status-pulse">
                                @if(($gatewayHealth['gateway_status'] ?? 'offline') === 'online')
                                    <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                        <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-success opacity-75" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                        <span class="relative inline-flex rounded-circle h-15 w-15 bg-success" style="height: 15px; width: 15px; background-color: #16a34a; border-radius: 50%;"></span>
                                    </span>
                                @else
                                    <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                        <span class="relative inline-flex rounded-circle h-15 w-15 bg-danger" style="height: 15px; width: 15px; background-color: #dc2626; border-radius: 50%;"></span>
                                    </span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-0 text-capitalize" id="gateway-status-text" style="font-size: 1.5rem; color: #1e293b;">
                                {{ $gatewayHealth['gateway_status'] ?? 'offline' }}
                            </h3>
                        </div>

                        <div class="border-top pt-3" style="font-size: 0.85rem; color: #64748b;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Gateway Version:</span>
                                <strong id="val-gateway-version">{{ $gatewayHealth['version'] ?? '1.0.0' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Endpoint Base:</span>
                                <span class="badge bg-light text-dark font-monospace" style="font-size: 0.72rem;">{{ config('services.ai.url') }}/ai</span>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline-primary btn-sm w-100 mt-4" id="btn-refresh-gateway">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Gateway
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Active AI Provider</span>
                            <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-stars" style="color:#2563eb; font-size: 1.1rem;"></i>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div id="provider-badge-container">
                                @if($activeProvider === 'gemini')
                                    <span class="badge bg-primary text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #2563eb !important;">Gemini</span>
                                @elseif($activeProvider === 'openai')
                                    <span class="badge bg-success text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #16a34a !important;">OpenAI</span>
                                @elseif($activeProvider === 'claude')
                                    <span class="badge bg-warning text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #ea580c !important;">Claude</span>
                                @else
                                    <span class="badge bg-secondary text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem;">{{ $activeProvider }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="border-top pt-3" style="font-size: 0.85rem; color: #64748b;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Provider Health:</span>
                                <strong class="text-capitalize" id="val-provider-health">
                                    <span class="badge bg-{{ ($gatewayHealth['provider_health'] ?? 'offline') === 'online' ? 'success' : 'danger' }}">
                                        {{ $gatewayHealth['provider_health'] ?? 'offline' }}
                                    </span>
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Default Model:</span>
                                <span class="badge bg-light text-dark font-monospace" id="val-provider-model" style="font-size: 0.72rem;">
                                    @if($activeProvider === 'gemini') gemini-2.0-flash @elseif($activeProvider === 'openai') gpt-4o-mini @else claude-3-5-sonnet @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label" style="font-size: 0.75rem; font-weight: 600; color: #475569;">Switch Provider (UI Mock)</label>
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-switch-mock @if($activeProvider === 'gemini') active @endif" data-provider="gemini" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">Gemini</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-switch-mock @if($activeProvider === 'openai') active @endif" data-provider="openai" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">OpenAI</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary btn-switch-mock @if($activeProvider === 'claude') active @endif" data-provider="claude" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">Claude</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-12 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Provider Latency</span>
                            <div style="width:36px;height:36px;background:#f5f3ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-speedometer" style="color:#7c3aed; font-size: 1.1rem;"></i>
                            </div>
                        </div>

                        <div class="d-flex align-items-baseline gap-1 mb-2">
                            <h2 class="fw-bold mb-0" id="latency-val" style="font-size: 2.2rem; color: #1e293b;">
                                {{ $healthResult['latency'] ?? '0' }}
                            </h2>
                            <span class="text-secondary" style="font-size: 0.9rem;">ms</span>
                        </div>
                        <p style="font-size: 0.82rem; color: #64748b; line-height: 1.4;">
                            Measured round-trip ping time to load the AI provider status check endpoint.
                        </p>
                    </div>
                    <div class="progress" style="height: 6px; border-radius: 10px; margin-top: 1.5rem;">
                        <div id="latency-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: {{ min(($healthResult['latency'] ?? 0) / 10, 100) }}%; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Interactive Test Prompt Playground -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1e293b;">
                        <i class="bi bi-chat-left-dots me-2 text-primary"></i>Test AI Gateway Prompt
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label for="prompt-input" class="form-label" style="font-weight:600; color: #374151;">Input Prompt</label>
                        <textarea id="prompt-input" class="form-control" rows="3" placeholder="Enter prompt to send to active provider...">Hello AI</textarea>
                    </div>

                    <div class="d-flex align-items-center justify-content-between">
                        <span class="text-muted" style="font-size: 0.78rem;">
                            Sends request to the active provider via FastAPI.
                        </span>
                        <button id="btn-run-prompt" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                            <span id="spinner-prompt" class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <i class="bi bi-lightning-charge-fill" id="icon-prompt"></i> Run Test Prompt
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Response Output Display -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1e293b;">
                        <i class="bi bi-cpu me-2 text-success"></i>Gateway Response
                    </h5>
                    <span id="response-time-badge" class="badge bg-light text-dark font-monospace" style="font-size: 0.72rem; display:none;"></span>
                </div>
                <div class="card-body p-4 d-flex flex-column justify-content-between bg-light" style="min-height: 200px;">
                    <div id="response-output-container" class="font-monospace" style="font-size: 0.85rem; color: #334155; white-space: pre-wrap; line-height: 1.5;">
                        <span class="text-muted">// Click "Run Test Prompt" to query response.</span>
                    </div>
                    <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-center" style="font-size: 0.78rem; color: #64748b;">
                        <span>Powered by FastAPI AI Gateway</span>
                        <span id="response-provider-badge" class="badge bg-secondary text-capitalize" style="display:none;"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Providers Status List -->
    <div class="card shadow-sm mt-4" style="border-radius: 12px; border: 1px solid #e2e8f0;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1e293b;">
                <i class="bi bi-list-task me-2 text-primary"></i>Available AI Providers Register
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size: 0.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="px-4 py-3 text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing:0.8px;">Provider Name</th>
                            <th class="px-4 py-3 text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing:0.8px;">Model Class</th>
                            <th class="px-4 py-3 text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing:0.8px;">Health status</th>
                            <th class="px-4 py-3 text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing:0.8px;">Mode</th>
                        </tr>
                    </thead>
                    <tbody id="providers-list-body">
                        @forelse($providers as $prov)
                            <tr>
                                <td class="px-4 py-3 fw-semibold text-capitalize">{{ $prov['name'] }}</td>
                                <td class="px-4 py-3 font-monospace" style="font-size:0.75rem;">
                                    @if($prov['name'] === 'gemini') gemini-2.0-flash @elif($prov['name'] === 'openai') gpt-4o-mini @else claude-3-5-sonnet @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if($prov['status'] === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @elseif($prov['status'] === 'inactive')
                                        <span class="badge bg-secondary">Inactive</span>
                                    @else
                                        <span class="badge bg-light text-secondary">Not Implemented</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-uppercase text-muted" style="font-size: 0.75rem;">{{ $prov['mode'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No providers loaded. Verify FastAPI server is running.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Triggers
            const btnRefresh = document.getElementById('btn-refresh-gateway');
            const btnRunPrompt = document.getElementById('btn-run-prompt');
            const btnSwitchMocks = document.querySelectorAll('.btn-switch-mock');

            // Spinner/Icons
            const spinnerPrompt = document.getElementById('spinner-prompt');
            const iconPrompt = document.getElementById('icon-prompt');

            // Output container
            const alertContainer = document.getElementById('alert-container');
            const responseContainer = document.getElementById('response-output-container');
            const responseTimeBadge = document.getElementById('response-time-badge');
            const responseProviderBadge = document.getElementById('response-provider-badge');

            // Metrics
            const gatewayStatusPulse = document.getElementById('gateway-status-pulse');
            const gatewayStatusText = document.getElementById('gateway-status-text');
            const valGatewayVersion = document.getElementById('val-gateway-version');
            const valProviderHealth = document.getElementById('val-provider-health');
            const valProviderModel = document.getElementById('val-provider-model');
            const latencyVal = document.getElementById('latency-val');
            const latencyProgressBar = document.getElementById('latency-progress-bar');
            const providersListBody = document.getElementById('providers-list-body');
            const providerBadgeContainer = document.getElementById('provider-badge-container');

            // Alert function
            function showAlert(type, message) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
                        <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i>
                        <div>${message}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            }

            // Switch Provider Mock Handler
            btnSwitchMocks.forEach(btn => {
                btn.addEventListener('click', function () {
                    const provider = this.getAttribute('data-provider');
                    btnSwitchMocks.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Show alert explaining it is dynamic configuration but mocked on Switch button
                    showAlert('warning', `Provider switched to <strong>${provider.toUpperCase()}</strong> (UI Simulation). To change the actual server provider, update the <code>DEFAULT_AI_PROVIDER</code> variable in your <code>.env</code> file.`);
                });
            });

            // Refresh gateway AJAX
            async function refreshGatewayStatus() {
                btnRefresh.disabled = true;
                
                try {
                    const healthResponse = await fetch("{{ route('ai-gateway.health') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const healthResult = await healthResponse.json();

                    const providersResponse = await fetch("{{ route('ai-gateway.providers') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const providersResult = await providersResponse.json();

                    if (healthResult.success) {
                        const healthData = healthResult.data;
                        gatewayStatusPulse.innerHTML = `
                            <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-success opacity-75" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                <span class="relative inline-flex rounded-circle h-15 w-15 bg-success" style="height: 15px; width: 15px; background-color: #16a34a; border-radius: 50%;"></span>
                            </span>
                        `;
                        gatewayStatusText.innerText = 'online';
                        gatewayStatusText.style.color = '#16a34a';
                        valGatewayVersion.innerText = healthData.version || '1.0.0';

                        valProviderHealth.innerHTML = `<span class="badge bg-${healthData.provider_health === 'online' ? 'success' : 'danger'}">${healthData.provider_health}</span>`;
                        latencyVal.innerText = healthResult.latency;
                        latencyProgressBar.style.width = Math.min(healthResult.latency / 10, 100) + '%';
                        latencyProgressBar.className = 'progress-bar bg-success';
                    } else {
                        throw new Error(healthResult.error || "Gateway returned fault");
                    }

                    if (providersResult.success) {
                        const active = providersResult.data.active_provider;
                        // update active provider card
                        let badgeHtml = '';
                        let modelName = '';
                        if (active === 'gemini') {
                            badgeHtml = '<span class="badge bg-primary text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #2563eb !important;">Gemini</span>';
                            modelName = 'gemini-2.0-flash';
                        } else if (active === 'openai') {
                            badgeHtml = '<span class="badge bg-success text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #16a34a !important;">OpenAI</span>';
                            modelName = 'gpt-4o-mini';
                        } else {
                            badgeHtml = '<span class="badge bg-warning text-capitalize px-3 py-2" id="active-provider-name" style="font-size: 1rem; background-color: #ea580c !important;">Claude</span>';
                            modelName = 'claude-3-5-sonnet';
                        }
                        providerBadgeContainer.innerHTML = badgeHtml;
                        valProviderModel.innerText = modelName;

                        // update providers table
                        let tableRows = '';
                        providersResult.data.providers.forEach(p => {
                            let statusBadge = '';
                            let modelName = '';
                            if (p.name === 'gemini') modelName = 'gemini-2.0-flash';
                            else if (p.name === 'openai') modelName = 'gpt-4o-mini';
                            else modelName = 'claude-3-5-sonnet';

                            if (p.status === 'active') statusBadge = '<span class="badge bg-success">Active</span>';
                            else if (p.status === 'inactive') statusBadge = '<span class="badge bg-secondary">Inactive</span>';
                            else statusBadge = '<span class="badge bg-light text-secondary">Not Implemented</span>';

                            tableRows += `
                                <tr>
                                    <td class="px-4 py-3 fw-semibold text-capitalize">${p.name}</td>
                                    <td class="px-4 py-3 font-monospace" style="font-size:0.75rem;">${modelName}</td>
                                    <td class="px-4 py-3">${statusBadge}</td>
                                    <td class="px-4 py-3"><span class="text-uppercase text-muted" style="font-size: 0.75rem;">${p.mode}</span></td>
                                </tr>
                            `;
                        });
                        providersListBody.innerHTML = tableRows;
                    }
                    showAlert('success', 'AI Gateway connection and provider statistics updated.');

                } catch (e) {
                    gatewayStatusPulse.innerHTML = `
                        <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                            <span class="relative inline-flex rounded-circle h-15 w-15 bg-danger" style="height: 15px; width: 15px; background-color: #dc2626; border-radius: 50%;"></span>
                        </span>
                    `;
                    gatewayStatusText.innerText = 'offline';
                    gatewayStatusText.style.color = '#dc2626';
                    valProviderHealth.innerHTML = `<span class="badge bg-danger">offline</span>`;
                    latencyVal.innerText = '0';
                    latencyProgressBar.style.width = '0%';
                    latencyProgressBar.className = 'progress-bar bg-danger';

                    showAlert('danger', `Failed to sync AI Gateway: ${e.message}`);
                } finally {
                    btnRefresh.disabled = false;
                }
            }

            btnRefresh.addEventListener('click', refreshGatewayStatus);

            // Run Test Prompt AJAX
            btnRunPrompt.addEventListener('click', async function () {
                const promptVal = document.getElementById('prompt-input').value;
                if (!promptVal.trim()) {
                    showAlert('danger', 'Prompt cannot be empty.');
                    return;
                }

                // Loading visual states
                btnRunPrompt.disabled = true;
                spinnerPrompt.classList.remove('d-none');
                iconPrompt.classList.add('d-none');
                responseContainer.innerHTML = `<span class="text-muted">// Querying AI Gateway active provider...</span>`;
                responseTimeBadge.style.display = 'none';
                responseProviderBadge.style.display = 'none';

                try {
                    const response = await fetch("{{ route('ai-gateway.test') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ prompt: promptVal })
                    });
                    const result = await response.json();

                    if (result.success) {
                        // Display text
                        responseContainer.innerText = result.data.text;
                        responseContainer.style.color = '#1e293b';

                        // Display metadata
                        responseTimeBadge.innerText = `${result.execution_time}s`;
                        responseTimeBadge.style.display = 'inline-block';
                        responseProviderBadge.innerText = result.provider;
                        responseProviderBadge.style.display = 'inline-block';
                        responseProviderBadge.className = `badge bg-${result.provider === 'gemini' ? 'primary' : (result.provider === 'openai' ? 'success' : 'warning')} text-capitalize`;
                    } else {
                        responseContainer.innerText = `Error: ${result.message || 'Unknown generation error'}`;
                        responseContainer.style.color = '#dc2626';
                    }
                } catch (e) {
                    responseContainer.innerText = `Connection Exception: ${e.message}`;
                    responseContainer.style.color = '#dc2626';
                    showAlert('danger', `Failed to connect to gateway server: ${e.message}`);
                } finally {
                    btnRunPrompt.disabled = false;
                    spinnerPrompt.classList.add('d-none');
                    iconPrompt.classList.remove('d-none');
                }
            });
        });
    </script>
</x-app-layout>
