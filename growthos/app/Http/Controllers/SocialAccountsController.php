<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Services\MetaOAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SocialAccountsController extends Controller
{
    protected MetaOAuthService $metaService;

    public function __construct(MetaOAuthService $metaService)
    {
        $this->metaService = $metaService;
    }

    /**
     * Display connected social accounts for the organization.
     */
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        $socialAccounts = $user->organization->socialAccounts()
            ->orderBy('platform')
            ->orderBy('account_name')
            ->get();

        return view('social-accounts.index', compact('socialAccounts'));
    }

    /**
     * Redirect to Meta OAuth login or Simulator.
     */
    public function connect(): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        // Generate state token for CSRF protection
        $state = bin2hex(random_bytes(16));
        session(['oauth_state' => $state]);

        return redirect($this->metaService->getAuthUrl($state));
    }

    /**
     * Handle the callback return from Meta OAuth or Simulator.
     */
    public function callback(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        // 1. Validate OAuth State
        $state = $request->query('state');
        $sessionState = session('oauth_state');
        session()->forget('oauth_state');

        if (!$state || !$sessionState || $state !== $sessionState) {
            return redirect()->route('social-accounts')
                ->with('error', 'Invalid OAuth state validation failed. Potential CSRF attack.');
        }

        // 2. Validate Callback Errors
        if ($request->query('error') === 'access_denied' || $request->query('denied')) {
            return redirect()->route('social-accounts')
                ->with('error', 'Authorization denied by the user.');
        }

        if ($request->query('error')) {
            return redirect()->route('social-accounts')
                ->with('error', 'Meta Error: ' . $request->query('error_description', $request->query('error')));
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect()->route('social-accounts')
                ->with('error', 'OAuth authorization code missing.');
        }

        // 3. Process connection depending on Mock/Simulator vs. Real OAuth
        try {
            if ($this->metaService->isMock()) {
                // Parse mock selection from simulator
                $selectedPages = array_filter(explode(',', $request->query('pages', '')));
                $selectedInstagrams = array_filter(explode(',', $request->query('instagrams', '')));
                
                $connectedCount = 0;
                $orgId = $user->organization_id;

                if (in_array('1001', $selectedPages)) {
                    // Connect page 1001
                    SocialAccount::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'platform' => 'facebook',
                            'page_id' => '1001',
                        ],
                        [
                            'platform_user_id' => 'mock_user_123',
                            'page_name' => 'Acme Corp Facebook Page',
                            'account_name' => 'Acme Corp Facebook Page',
                            'access_token' => 'mock_page_token_acme_1001',
                            'permissions' => ['pages_show_list', 'pages_read_engagement', 'pages_manage_posts'],
                            'connected_at' => now(),
                            'status' => 'connected',
                            'last_sync' => now(),
                        ]
                    );
                    $connectedCount++;
                }

                if (in_array('1002', $selectedPages)) {
                    // Connect page 1002
                    SocialAccount::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'platform' => 'facebook',
                            'page_id' => '1002',
                        ],
                        [
                            'platform_user_id' => 'mock_user_123',
                            'page_name' => 'GrowthOS Demo Page',
                            'account_name' => 'GrowthOS Demo Page',
                            'access_token' => 'mock_page_token_growthos_1002',
                            'permissions' => ['pages_show_list', 'pages_read_engagement', 'pages_manage_posts'],
                            'connected_at' => now(),
                            'status' => 'connected',
                            'last_sync' => now(),
                        ]
                    );
                    $connectedCount++;
                }

                if (in_array('5001', $selectedInstagrams)) {
                    // Connect Instagram 5001
                    SocialAccount::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'platform' => 'instagram',
                            'instagram_business_id' => '5001',
                        ],
                        [
                            'platform_user_id' => 'mock_user_123',
                            'page_id' => '1001',
                            'page_name' => 'Acme Corp Facebook Page',
                            'account_name' => '@acme_instagram',
                            'access_token' => 'mock_page_token_acme_1001',
                            'permissions' => ['instagram_basic', 'instagram_content_publish', 'business_management'],
                            'connected_at' => now(),
                            'status' => 'connected',
                            'last_sync' => now(),
                        ]
                    );
                    $connectedCount++;
                }

                if ($connectedCount === 0) {
                    return redirect()->route('social-accounts')
                        ->with('error', 'No social accounts were selected for connection.');
                }

                return redirect()->route('social-accounts')
                    ->with('success', "Successfully connected {$connectedCount} simulated account(s)!");
            } else {
                // Real OAuth exchange flow
                $tokens = $this->metaService->exchangeCodeForTokens($code);
                $pages = $this->metaService->fetchPages($tokens['access_token']);
                
                $connectedCount = 0;
                $orgId = $user->organization_id;

                foreach ($pages as $page) {
                    // Create/Update Facebook Page connection
                    SocialAccount::updateOrCreate(
                        [
                            'organization_id' => $orgId,
                            'platform' => 'facebook',
                            'page_id' => $page['id']
                        ],
                        [
                            'platform_user_id' => $tokens['user_id'] ?? null,
                            'page_name' => $page['name'],
                            'account_name' => $page['name'],
                            'access_token' => $page['access_token'],
                            'permissions' => explode(',', config('services.facebook.permissions')),
                            'connected_at' => now(),
                            'status' => 'connected',
                            'last_sync' => now(),
                        ]
                    );
                    $connectedCount++;

                    // If linked Instagram business account exists, connect it as well
                    if (!empty($page['instagram_business_account'])) {
                        $ig = $page['instagram_business_account'];
                        SocialAccount::updateOrCreate(
                            [
                                'organization_id' => $orgId,
                                'platform' => 'instagram',
                                'instagram_business_id' => $ig['id']
                            ],
                            [
                                'platform_user_id' => $tokens['user_id'] ?? null,
                                'page_id' => $page['id'],
                                'page_name' => $page['name'],
                                'account_name' => '@' . ($ig['username'] ?? $ig['name']),
                                'access_token' => $page['access_token'],
                                'permissions' => explode(',', config('services.facebook.permissions')),
                                'connected_at' => now(),
                                'status' => 'connected',
                                'last_sync' => now(),
                            ]
                        );
                        $connectedCount++;
                    }
                }

                return redirect()->route('social-accounts')
                    ->with('success', "Successfully connected {$connectedCount} Meta account(s)!");
            }
        } catch (\Exception $e) {
            return redirect()->route('social-accounts')
                ->with('error', 'Connection failed: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect a social account (clear token values, preserve metadata, set status).
     */
    public function disconnect(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $this->authorize('delete', $socialAccount);

        $socialAccount->update([
            'access_token' => null,
            'refresh_token' => null,
            'status' => 'disconnected',
        ]);

        return redirect()->route('social-accounts')
            ->with('success', "Disconnected {$socialAccount->account_name} successfully.");
    }

    /**
     * Handle data sync request (Coming Soon placeholder).
     */
    public function sync(Request $request, SocialAccount $socialAccount): RedirectResponse
    {
        $this->authorize('update', $socialAccount);

        return redirect()->route('social-accounts')
            ->with('info', 'Sync features are Coming Soon.');
    }

    /**
     * Show the Simulated Meta login dialog view.
     */
    public function simulator(Request $request): View|RedirectResponse
    {
        $state = $request->query('state');
        if (!$state) {
            return redirect()->route('social-accounts')
                ->with('error', 'Missing state parameter for OAuth simulator.');
        }

        return view('social-accounts.simulator', compact('state'));
    }
}
