<x-app-layout>
    <x-slot name="title">AI Service Integration</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">AI Service</li>
        </ol>
    </nav>

    <!-- Connection Status Summary Card -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Service Status</span>
                            <div style="width:36px;height:36px;background:#f5f3ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-cpu" style="color:#7c3aed; font-size: 1.1rem;"></i>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div id="status-pulse-container">
                                @if(($status['status'] ?? 'offline') === 'online')
                                    <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                        <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-success opacity-75" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                        <span class="relative inline-flex rounded-circle h-15 w-15 bg-success" style="height: 15px; width: 15px; background-color: #16a34a; border-radius: 50%;"></span>
                                    </span>
                                @elseif(($status['status'] ?? 'offline') === 'error')
                                    <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                        <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-warning opacity-75" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                        <span class="relative inline-flex rounded-circle h-15 w-15 bg-warning" style="height: 15px; width: 15px; background-color: #d97706; border-radius: 50%;"></span>
                                    </span>
                                @else
                                    <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                        <span class="relative inline-flex rounded-circle h-15 w-15 bg-danger" style="height: 15px; width: 15px; background-color: #dc2626; border-radius: 50%;"></span>
                                    </span>
                                @endif
                            </div>
                            <h3 class="fw-bold mb-0 text-capitalize" id="status-text" style="font-size: 1.5rem; color: #1e293b;">
                                {{ $status['status'] ?? 'offline' }}
                            </h3>
                        </div>

                        <div class="border-top pt-3" style="font-size: 0.85rem; color: #64748b;">
                            <div class="d-flex justify-content-between mb-2">
                                <span>API Version:</span>
                                <strong id="val-version">{{ $status['version'] ?? '—' }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Uptime:</span>
                                <strong id="val-uptime">
                                    @if(isset($status['uptime']))
                                        {{ number_format($status['uptime'], 1) }}s
                                    @else
                                        —
                                    @endif
                                </strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Base URL:</span>
                                <span class="badge bg-light text-dark font-monospace" style="font-size: 0.72rem;">{{ config('services.ai.url') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn btn-outline-primary btn-sm w-100 mt-4" id="btn-refresh-status">
                        <i class="bi bi-arrow-clockwise me-1"></i> Refresh Connection Status
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Round-trip Latency</span>
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-speedometer2" style="color:#2563eb; font-size: 1.1rem;"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-baseline gap-1 mb-2">
                        <h2 class="fw-bold mb-0" id="latency-text" style="font-size: 2.2rem; color: #1e293b;">
                            {{ $status['latency'] ?? '0' }}
                        </h2>
                        <span class="text-secondary" style="font-size: 0.9rem;">ms</span>
                    </div>
                    <p style="font-size: 0.82rem; color: #64748b; margin-bottom: 2rem;">
                        Round-trip connection time to the FastAPI service. Overrides timeout threshold at {{ config('services.ai.timeout') }}s.
                    </p>
                    <div class="progress" style="height: 6px; border-radius: 10px;">
                        <div id="latency-bar" class="progress-bar bg-success" role="progressbar" style="width: {{ min(($status['latency'] ?? 0) / 5, 100) }}%; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-uppercase fw-bold text-secondary" style="font-size: 0.75rem; letter-spacing: 0.8px;">Communication Config</span>
                            <div style="width:36px;height:36px;background:#ecfdf5;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-shield-check" style="color:#059669; font-size: 1.1rem;"></i>
                            </div>
                        </div>
                        <p style="font-size: 0.82rem; color: #64748b; line-height: 1.45;">
                            Outgoing Laravel requests pass a secure shared token header <code class="font-monospace" style="color: #059669;">X-API-Token</code> configured dynamically from the host environment variables.
                        </p>
                    </div>
                    <div class="bg-light p-3 rounded" style="border: 1px solid #e2e8f0; font-size: 0.8rem;">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Retries Configuration:</span>
                            <strong>3 Attempts (100ms delay)</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>HTTP Timeout:</span>
                            <strong>{{ config('services.ai.timeout') }}s</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Playground Actions & Live Console -->
    <div class="row g-4">
        <!-- Interactive Actions Form -->
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0" style="font-size: 0.95rem; color: #1e293b;">
                        <i class="bi bi-terminal me-2 text-primary"></i>Service Command Playground
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Ping Card Action -->
                    <div class="mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.85rem; color: #1e293b;">POST /ping</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.75rem;">Verifies secure shared secret header authentication check.</p>
                            </div>
                            <button id="btn-ping" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
                                <i class="bi bi-send-fill" style="font-size: 0.75rem;"></i> Ping
                            </button>
                        </div>
                    </div>

                    <!-- Health Check Action -->
                    <div class="mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h6 class="fw-bold mb-0" style="font-size: 0.85rem; color: #1e293b;">POST /health (Laravel wrapper)</h6>
                                <p class="text-secondary mb-0" style="font-size: 0.75rem;">Queries status, current uptime, and service API build version.</p>
                            </div>
                            <button id="btn-health" class="btn btn-sm btn-success d-flex align-items-center gap-1 text-white">
                                <i class="bi bi-heart-pulse-fill" style="font-size: 0.75rem;"></i> Health Check
                            </button>
                        </div>
                    </div>

                    <!-- Echo Action -->
                    <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                        <div class="mb-3">
                            <h6 class="fw-bold mb-1" style="font-size: 0.85rem; color: #1e293b;">POST /echo</h6>
                            <p class="text-secondary mb-0" style="font-size: 0.75rem;">Sends a JSON request payload and verifies returning loop output.</p>
                        </div>
                        <div class="mb-3">
                            <label for="echo-payload" class="form-label" style="font-size:0.75rem; font-weight: 600; color: #475569;">Echo Custom JSON Payload</label>
                            <textarea id="echo-payload" class="form-control font-monospace" rows="4" style="font-size: 0.78rem; border-color: #cbd5e1;">{
  "message": "Hello from GrowthOS Laravel App!",
  "environment": "development",
  "data": {
    "module": "AI Integration Layer",
    "verified": true
  }
}</textarea>
                            <div class="invalid-feedback" id="json-error" style="font-size: 0.72rem;">Invalid JSON format.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button id="btn-echo" class="btn btn-sm btn-purple d-flex align-items-center gap-1 text-white" style="background-color: #7c3aed;">
                                <i class="bi bi-arrow-left-right" style="font-size: 0.75rem;"></i> Echo Payload
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Glassmorphism Developer Console -->
        <div class="col-12 col-xl-7">
            <div class="card h-100 shadow-sm border-0 d-flex flex-column" style="border-radius: 12px; overflow: hidden; background: #0f172a; min-height: 480px;">
                <!-- Console Header -->
                <div class="px-4 py-3 d-flex align-items-center justify-content-between" style="background: #1e293b; border-bottom: 1px solid #334155;">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle" style="width: 10px; height: 10px; background: #ef4444;"></span>
                        <span class="rounded-circle" style="width: 10px; height: 10px; background: #eab308;"></span>
                        <span class="rounded-circle" style="width: 10px; height: 10px; background: #22c55e;"></span>
                        <h6 class="fw-semibold mb-0 text-white font-monospace ms-2" style="font-size: 0.85rem;">developer_console_log</h6>
                    </div>
                    <button class="btn btn-sm text-secondary p-0" id="btn-clear-console" title="Clear Console" style="background: none; border: none;">
                        <i class="bi bi-trash3 text-secondary font-monospace" style="font-size: 0.9rem;"> [Clear]</i>
                    </button>
                </div>
                <!-- Console Log Output -->
                <div class="card-body p-4 flex-grow-1 font-monospace" id="console-logs" style="color: #38bdf8; font-size: 0.78rem; overflow-y: auto; max-height: 420px; line-height: 1.5;">
                    <div style="color: #64748b;">// GrowthOS AI Service Console initialized.</div>
                    <div style="color: #64748b;">// Trigger commands on the playground to debug incoming and outgoing payloads.</div>
                    <div style="color: #64748b;">// Ready...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Styling overrides for animations and custom layouts -->
    <x-slot name="styles">
        <style>
            @keyframes pulse {
                0%, 100% {
                    opacity: 1;
                    transform: scale(1);
                }
                50% {
                    opacity: .5;
                    transform: scale(1.6);
                }
            }
            .font-monospace {
                font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace !important;
            }
            #console-logs::-webkit-scrollbar {
                width: 6px;
            }
            #console-logs::-webkit-scrollbar-track {
                background: #0f172a;
            }
            #console-logs::-webkit-scrollbar-thumb {
                background: #334155;
                border-radius: 4px;
            }
            #console-logs::-webkit-scrollbar-thumb:hover {
                background: #475569;
            }
        </style>
    </x-slot>

    <!-- Console Playground Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const consoleLogs = document.getElementById('console-logs');
            const btnClear = document.getElementById('btn-clear-console');

            // Interactive Buttons
            const btnPing = document.getElementById('btn-ping');
            const btnHealth = document.getElementById('btn-health');
            const btnEcho = document.getElementById('btn-echo');
            const btnRefresh = document.getElementById('btn-refresh-status');

            // Metrics Elements
            const statusPulseContainer = document.getElementById('status-pulse-container');
            const statusText = document.getElementById('status-text');
            const valVersion = document.getElementById('val-version');
            const valUptime = document.getElementById('val-uptime');
            const latencyText = document.getElementById('latency-text');
            const latencyBar = document.getElementById('latency-bar');

            // Helper to get Timestamp
            function getTimestamp() {
                const now = new Date();
                return now.toTimeString().split(' ')[0] + '.' + String(now.getMilliseconds()).padStart(3, '0');
            }

            // Write Line to Developer Console
            function writeToConsole(type, message, details = null) {
                const logRow = document.createElement('div');
                logRow.className = 'mb-2';
                
                let prefixColor = '#38bdf8'; // blue
                let label = 'INFO';

                if (type === 'request') {
                    prefixColor = '#a855f7'; // purple
                    label = 'OUT';
                } else if (type === 'response-success') {
                    prefixColor = '#22c55e'; // green
                    label = 'IN ';
                } else if (type === 'response-error') {
                    prefixColor = '#ef4444'; // red
                    label = 'ERR';
                }

                logRow.innerHTML = `[<span style="color: #64748b;">${getTimestamp()}</span>] <span style="color: ${prefixColor}; fw-bold;">${label}:</span> ${message}`;
                
                if (details) {
                    const pre = document.createElement('pre');
                    pre.className = 'mt-1 p-2 rounded';
                    pre.style.background = 'rgba(30, 41, 59, 0.5)';
                    pre.style.color = type === 'response-error' ? '#fca5a5' : '#cbd5e1';
                    pre.style.fontSize = '0.72rem';
                    pre.style.border = '1px solid #1e293b';
                    pre.textContent = typeof details === 'string' ? details : JSON.stringify(details, null, 2);
                    logRow.appendChild(pre);
                }

                consoleLogs.appendChild(logRow);
                consoleLogs.scrollTop = consoleLogs.scrollHeight;
            }

            // Clear Console
            btnClear.addEventListener('click', function () {
                consoleLogs.innerHTML = `<div style="color: #64748b;">// Console cleared.</div>`;
            });

            // Action API caller
            async function callService(endpoint, body = null) {
                const url = `{{ url('/ai-service') }}/${endpoint}`;
                writeToConsole('request', `POST ${url}`, body);

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: body ? JSON.stringify({ payload: body }) : null
                    });

                    const data = await response.json();

                    if (data.success) {
                        writeToConsole('response-success', `200 OK (latency: ${data.latency}ms)`, data.data);
                    } else {
                        writeToConsole('response-error', `FAIL Status: ${data.status} (latency: ${data.latency}ms)`, data.error || data.data);
                    }
                    return data;
                } catch (e) {
                    writeToConsole('response-error', `Exception: ${e.message}`);
                    return null;
                }
            }

            // Button Click Handlers
            btnPing.addEventListener('click', async function () {
                btnPing.disabled = true;
                await callService('ping');
                btnPing.disabled = false;
            });

            btnHealth.addEventListener('click', async function () {
                btnHealth.disabled = true;
                await callService('health');
                btnHealth.disabled = false;
            });

            btnEcho.addEventListener('click', async function () {
                const textarea = document.getElementById('echo-payload');
                const errorBox = document.getElementById('json-error');
                let payload = {};

                try {
                    payload = JSON.parse(textarea.value);
                    textarea.classList.remove('is-invalid');
                    errorBox.style.display = 'none';
                } catch (e) {
                    textarea.classList.add('is-invalid');
                    errorBox.style.display = 'block';
                    return;
                }

                btnEcho.disabled = true;
                await callService('echo', payload);
                btnEcho.disabled = false;
            });

            // Refresh Status Handler
            async function updateSystemStatus() {
                btnRefresh.disabled = true;
                writeToConsole('request', 'Querying live connection status wrapper...');
                
                try {
                    const response = await fetch("{{ url('/ai-service/health') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        const payload = data.data;
                        statusPulseContainer.innerHTML = `
                            <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-circle bg-success opacity-75" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                <span class="relative inline-flex rounded-circle h-15 w-15 bg-success" style="height: 15px; width: 15px; background-color: #16a34a; border-radius: 50%;"></span>
                            </span>
                        `;
                        statusText.innerText = 'online';
                        statusText.style.color = '#16a34a';
                        
                        valVersion.innerText = payload.version || '1.0.0';
                        valUptime.innerText = payload.uptime ? parseFloat(payload.uptime).toFixed(1) + 's' : '0s';
                        
                        latencyText.innerText = data.latency;
                        latencyBar.style.width = Math.min(data.latency / 5, 100) + '%';
                        latencyBar.className = 'progress-bar bg-success';
                        
                        writeToConsole('response-success', `Connection Active. Latency: ${data.latency}ms`);
                    } else {
                        statusPulseContainer.innerHTML = `
                            <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                                <span class="relative inline-flex rounded-circle h-15 w-15 bg-danger" style="height: 15px; width: 15px; background-color: #dc2626; border-radius: 50%;"></span>
                            </span>
                        `;
                        statusText.innerText = 'offline';
                        statusText.style.color = '#dc2626';
                        
                        valVersion.innerText = '—';
                        valUptime.innerText = '—';
                        
                        latencyText.innerText = '—';
                        latencyBar.style.width = '0%';
                        latencyBar.className = 'progress-bar bg-danger';
                        
                        writeToConsole('response-error', `Connection Failed: ${data.error || 'Server Offline'}`);
                    }
                } catch (e) {
                    statusPulseContainer.innerHTML = `
                        <span class="position-relative d-flex" style="height: 15px; width: 15px;">
                            <span class="relative inline-flex rounded-circle h-15 w-15 bg-danger" style="height: 15px; width: 15px; background-color: #dc2626; border-radius: 50%;"></span>
                        </span>
                    `;
                    statusText.innerText = 'offline';
                    statusText.style.color = '#dc2626';
                    
                    latencyText.innerText = '—';
                    latencyBar.style.width = '0%';
                    latencyBar.className = 'progress-bar bg-danger';
                    
                    writeToConsole('response-error', `Connection Exception: ${e.message}`);
                } finally {
                    btnRefresh.disabled = false;
                }
            }

            btnRefresh.addEventListener('click', updateSystemStatus);
        });
    </script>
</x-app-layout>
