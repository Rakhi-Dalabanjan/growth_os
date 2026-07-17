<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaOAuthService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $permissions;

    public function __construct()
    {
        $this->clientId = config('services.facebook.client_id') ?? '';
        $this->clientSecret = config('services.facebook.client_secret') ?? '';
        $this->redirectUri = config('services.facebook.redirect_uri') ?? '';
        $this->permissions = config('services.facebook.permissions') ?? '';
    }

    /**
     * Check if we are running in mock/simulator mode.
     */
    public function isMock(): bool
    {
        return empty($this->clientId) || $this->clientId === 'mock';
    }

    /**
     * Get the authorization URL to redirect the user to.
     */
    public function getAuthUrl(string $state): string
    {
        if ($this->isMock()) {
            return route('meta.simulator', ['state' => $state]);
        }

        return 'https://www.facebook.com/v20.0/dialog/oauth?' . http_build_query([
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'scope' => $this->permissions,
            'response_type' => 'code',
        ]);
    }

    /**
     * Swap the temporary OAuth code for access tokens.
     */
    public function exchangeCodeForTokens(string $code): array
    {
        if ($this->isMock()) {
            // Simulated token response
            return [
                'access_token' => 'mock_user_access_token_' . uniqid(),
                'expires_in' => 5184000, // 60 days
                'token_type' => 'bearer'
            ];
        }

        $response = Http::get('https://graph.facebook.com/v20.0/oauth/access_token', [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'code' => $code,
        ]);

        if ($response->failed()) {
            Log::error('Meta OAuth Token Exchange Failed', ['response' => $response->body()]);
            throw new \Exception('Failed to exchange authorization code: ' . ($response->json('error.message') ?? 'Unknown error'));
        }

        return $response->json();
    }

    /**
     * Fetch connected Facebook Pages and their linked Instagram Business Accounts.
     */
    public function fetchPages(string $userAccessToken): array
    {
        if ($this->isMock()) {
            // Mock pages
            return [
                [
                    'id' => '1001',
                    'name' => 'Acme Corp Facebook Page',
                    'access_token' => 'mock_page_token_acme_1001',
                    'instagram_business_account' => [
                        'id' => '5001',
                        'username' => 'acme_instagram',
                        'name' => 'Acme Business Instagram',
                    ]
                ],
                [
                    'id' => '1002',
                    'name' => 'GrowthOS Demo Page',
                    'access_token' => 'mock_page_token_growthos_1002',
                    'instagram_business_account' => null,
                ]
            ];
        }

        // Real Facebook Graph API implementation
        // 1. Fetch user's pages
        $pagesResponse = Http::get('https://graph.facebook.com/v20.0/me/accounts', [
            'access_token' => $userAccessToken,
            'fields' => 'id,name,access_token',
        ]);

        if ($pagesResponse->failed()) {
            Log::error('Meta Pages Retrieval Failed', ['response' => $pagesResponse->body()]);
            throw new \Exception('Failed to retrieve Facebook pages: ' . ($pagesResponse->json('error.message') ?? 'Unknown error'));
        }

        $pages = $pagesResponse->json('data') ?? [];
        $result = [];

        // 2. For each page, query if there is a linked Instagram Business Account
        foreach ($pages as $page) {
            $pageId = $page['id'];
            $pageAccessToken = $page['access_token'];

            $igResponse = Http::get("https://graph.facebook.com/v20.0/{$pageId}", [
                'fields' => 'instagram_business_account{id,username,name}',
                'access_token' => $pageAccessToken,
            ]);

            $instagramAccount = null;
            if ($igResponse->successful() && $igResponse->json('instagram_business_account')) {
                $instagramAccount = $igResponse->json('instagram_business_account');
            }

            $result[] = [
                'id' => $pageId,
                'name' => $page['name'],
                'access_token' => $pageAccessToken,
                'instagram_business_account' => $instagramAccount,
            ];
        }

        return $result;
    }
}
