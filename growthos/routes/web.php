<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AIServiceController;
use App\Http\Controllers\AiGatewayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Organization
    Route::get('/organization', [OrganizationController::class, 'index'])
        ->name('organization.index');
    Route::get('/organization/create', [OrganizationController::class, 'create'])
        ->name('organization.create');
    Route::post('/organization', [OrganizationController::class, 'store'])
        ->name('organization.store');
    Route::get('/organization/{organization}', [OrganizationController::class, 'show'])
        ->name('organization.show');
    Route::get('/organization/{organization}/edit', [OrganizationController::class, 'edit'])
        ->name('organization.edit');
    Route::put('/organization/{organization}', [OrganizationController::class, 'update'])
        ->name('organization.update');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('settings.index');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])
        ->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])
        ->name('settings.password.update');

    // Brand Profile
    Route::get('/brand-profile', [\App\Http\Controllers\BrandProfileController::class, 'index'])
        ->name('brand-profile');
    Route::get('/brand-profile/create', [\App\Http\Controllers\BrandProfileController::class, 'create'])
        ->name('brand-profile.create');
    Route::post('/brand-profile', [\App\Http\Controllers\BrandProfileController::class, 'store'])
        ->name('brand-profile.store');
    Route::get('/brand-profile/{brandProfile}', [\App\Http\Controllers\BrandProfileController::class, 'show'])
        ->name('brand-profile.show');
    Route::get('/brand-profile/{brandProfile}/edit', [\App\Http\Controllers\BrandProfileController::class, 'edit'])
        ->name('brand-profile.edit');
    Route::put('/brand-profile/{brandProfile}', [\App\Http\Controllers\BrandProfileController::class, 'update'])
        ->name('brand-profile.update');

    // Brand Intelligence
    Route::get('/brand-intelligence', [\App\Http\Controllers\BrandIntelligenceController::class, 'show'])
        ->name('brand-intelligence');
    Route::post('/brand-intelligence/analyze', [\App\Http\Controllers\BrandIntelligenceController::class, 'analyze'])
        ->name('brand-intelligence.analyze');

    // Marketing Strategy
    Route::get('/marketing-strategy', [\App\Http\Controllers\MarketingStrategyController::class, 'show'])
        ->name('marketing-strategy');
    Route::post('/marketing-strategy/generate', [\App\Http\Controllers\MarketingStrategyController::class, 'generate'])
        ->name('marketing-strategy.generate');

    // Social Accounts
    Route::get('/social-accounts', [\App\Http\Controllers\SocialAccountsController::class, 'index'])
        ->name('social-accounts');
    Route::get('/social-accounts/connect', [\App\Http\Controllers\SocialAccountsController::class, 'connect'])
        ->name('social-accounts.connect');
    Route::get('/social-accounts/callback', [\App\Http\Controllers\SocialAccountsController::class, 'callback'])
        ->name('social-accounts.callback');
    Route::post('/social-accounts/{socialAccount}/disconnect', [\App\Http\Controllers\SocialAccountsController::class, 'disconnect'])
        ->name('social-accounts.disconnect');
    Route::post('/social-accounts/{socialAccount}/sync', [\App\Http\Controllers\SocialAccountsController::class, 'sync'])
        ->name('social-accounts.sync');
    Route::get('/mock/meta/auth', [\App\Http\Controllers\SocialAccountsController::class, 'simulator'])
        ->name('meta.simulator');
    // Content Calendar
    Route::get('/content-calendar', [\App\Http\Controllers\ContentCalendarController::class, 'index'])
        ->name('content-calendar');
    Route::post('/content-calendar/generate', [\App\Http\Controllers\ContentCalendarController::class, 'generate'])
        ->name('content-calendar.generate');
    Route::post('/content-calendar/store', [\App\Http\Controllers\ContentCalendarController::class, 'store'])
        ->name('content-calendar.store');
    Route::put('/content-calendar/{id}', [\App\Http\Controllers\ContentCalendarController::class, 'update'])
        ->name('content-calendar.update');
    Route::delete('/content-calendar/{id}', [\App\Http\Controllers\ContentCalendarController::class, 'destroy'])
        ->name('content-calendar.destroy');
    Route::post('/content-calendar/{id}/duplicate', [\App\Http\Controllers\ContentCalendarController::class, 'duplicate'])
        ->name('content-calendar.duplicate');
    Route::post('/content-calendar/bulk', [\App\Http\Controllers\ContentCalendarController::class, 'bulkAction'])
        ->name('content-calendar.bulk');
    // Caption Studio
    Route::get('/caption-studio', [\App\Http\Controllers\CaptionController::class, 'index'])
        ->name('caption.index');
    Route::get('/ai-studio', fn() => redirect()->route('caption.index'))
        ->name('ai-studio');
    Route::post('/caption-studio/generate/{calendarId}', [\App\Http\Controllers\CaptionController::class, 'generate'])
        ->name('caption.generate');
    Route::post('/caption-studio/regenerate/{id}', [\App\Http\Controllers\CaptionController::class, 'regenerate'])
        ->name('caption.regenerate');
    Route::put('/caption-studio/{id}', [\App\Http\Controllers\CaptionController::class, 'update'])
        ->name('caption.update');
    Route::post('/caption-studio/{id}/duplicate', [\App\Http\Controllers\CaptionController::class, 'duplicate'])
        ->name('caption.duplicate');
    Route::delete('/caption-studio/{id}', [\App\Http\Controllers\CaptionController::class, 'destroy'])
        ->name('caption.destroy');
    Route::post('/caption-studio/{id}/approve', [\App\Http\Controllers\CaptionController::class, 'approve'])
        ->name('caption.approve');
    Route::post('/caption-studio/{id}/reject', [\App\Http\Controllers\CaptionController::class, 'reject'])
        ->name('caption.reject');
    Route::post('/caption-studio/bulk', [\App\Http\Controllers\CaptionController::class, 'bulk'])
        ->name('caption.bulk');

    Route::get('/assets', fn() => view('coming-soon', ['feature' => 'Assets']))
        ->name('assets');
    Route::get('/analytics', fn() => view('coming-soon', ['feature' => 'Analytics']))
        ->name('analytics');

    // AI Service Integration
    Route::get('/ai-service', [AIServiceController::class, 'index'])->name('ai-service.index');
    Route::post('/ai-service/ping', [AIServiceController::class, 'ping'])->name('ai-service.ping');
    Route::post('/ai-service/health', [AIServiceController::class, 'health'])->name('ai-service.health');
    Route::post('/ai-service/echo', [AIServiceController::class, 'echo'])->name('ai-service.echo');

    // AI Gateway Integration
    Route::get('/ai-gateway', [AiGatewayController::class, 'index'])->name('ai-gateway.index');
    Route::post('/ai-gateway/health', [AiGatewayController::class, 'health'])->name('ai-gateway.health');
    Route::post('/ai-gateway/providers', [AiGatewayController::class, 'providers'])->name('ai-gateway.providers');
    Route::post('/ai-gateway/test', [AiGatewayController::class, 'testPrompt'])->name('ai-gateway.test');
});

require __DIR__ . '/auth.php';
