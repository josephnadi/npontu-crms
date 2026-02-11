<?php

use App\Http\Controllers\CRM\CommunicationController;
use App\Http\Controllers\CRM\EngagementController;
use App\Http\Controllers\CRM\DealController;
use App\Http\Controllers\CRM\ActivityController;
use App\Http\Controllers\CRM\DashboardController;
use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\CRM\ContactController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\ProjectController;
use App\Http\Controllers\CRM\TaskController;
use App\Http\Controllers\CRM\InvoiceController;
use App\Http\Controllers\CRM\TicketController;
use App\Http\Controllers\CRM\PartnerController;
use App\Http\Controllers\CRM\MarketingAutomationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Guest routes (login, register, etc.)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login1', fn () => Inertia::render('Auth/Login1'));
    Route::get('/register1', fn () => Inertia::render('Auth/Register1'));
    Route::get('/forgot-pwd1', fn () => Inertia::render('Auth/ForgotPwd1'));
});

/*
|--------------------------------------------------------------------------
| Authenticated routes – dashboard and app pages (session-based navigation)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Main dashboard at /dashboard (and / for convenience)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', fn () => redirect()->route('dashboard'));

    // Legacy URL: redirect to main dashboard
    Route::get('/dashboard/default', fn () => redirect()->route('dashboard'));

    // Other app pages (sidebar navigation)
    Route::get('/starter', fn () => Inertia::render('Starter'))->name('starter');
    Route::get('/utils/typography', fn () => Inertia::render('Utils/Typography'))->name('utils.typography');
    Route::get('/utils/colors', fn () => Inertia::render('Utils/Colors'))->name('utils.colors');
    Route::get('/utils/shadows', fn () => Inertia::render('Utils/Shadows'))->name('utils.shadows');

    // CRM
    Route::prefix('crm')->group(function () {
        // Deals
        Route::post('/deals/{deal}/convert', [DealController::class, 'convert'])->name('crm.deals.convert');
        Route::post('/deals/{deal}/convert-to-lead', [DealController::class, 'convertToLead'])->name('crm.deals.convert-to-lead');
        Route::post('/deals/{deal}/convert-to-ticket', [DealController::class, 'convertToTicket'])->name('crm.deals.convert-to-ticket');
        Route::post('/deals/{deal}/convert-to-engagement', [DealController::class, 'convertToEngagement'])->name('crm.deals.convert-to-engagement');
        Route::get('/deals-pipeline', [DealController::class, 'pipeline'])->name('crm.deals.pipeline');
        Route::put('/deals/{deal}/stage', [DealController::class, 'updateStage'])->name('crm.deals.updateStage');
        Route::get('/deals-export', [DealController::class, 'export'])->name('crm.deals.export');
        Route::resource('deals', DealController::class)->names('crm.deals');

        // Activities
        Route::get('/activities-calendar', [ActivityController::class, 'calendar'])->name('crm.activities.calendar');
        Route::put('/activities/{activity}/complete', [ActivityController::class, 'complete'])->name('crm.activities.complete');
        Route::post('/activities/bulk-update', [ActivityController::class, 'bulkUpdate'])->name('crm.activities.bulkUpdate');
        Route::post('/activities/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('crm.activities.bulkDestroy');
        Route::resource('activities', ActivityController::class)->names('crm.activities');

        // Clients
        Route::post('/clients/{client}/convert', [ClientController::class, 'convert'])->name('crm.clients.convert');
        Route::post('/clients/{client}/convert-to-partner', [ClientController::class, 'convertToPartner'])->name('crm.clients.convert-to-partner');
        Route::post('/clients/{client}/convert-to-ticket', [ClientController::class, 'convertToTicket'])->name('crm.clients.convert-to-ticket');
        Route::resource('clients', ClientController::class)->names('crm.clients');

    // Contacts
    Route::post('/contacts/{contact}/convert', [ContactController::class, 'convert'])->name('crm.contacts.convert');
    Route::post('/contacts/{contact}/convert-to-ticket', [ContactController::class, 'convertToTicket'])->name('crm.contacts.convert-to-ticket');
    Route::resource('contacts', ContactController::class)->names('crm.contacts');

        // Leads
        Route::post('/leads/{lead}/convert', [LeadController::class, 'convert'])->name('crm.leads.convert');
        Route::post('/leads/{lead}/convert-to-partner', [LeadController::class, 'convertToPartner'])->name('crm.leads.convert-to-partner');
        Route::post('/leads/{lead}/convert-to-ticket', [LeadController::class, 'convertToTicket'])->name('crm.leads.convert-to-ticket');
        Route::resource('leads', LeadController::class)->names('crm.leads');

        // Projects
        Route::resource('projects', ProjectController::class)->names('crm.projects');

        // Tasks
        Route::resource('tasks', TaskController::class)->names('crm.tasks');

        // Invoices
        Route::resource('invoices', InvoiceController::class)->names('crm.invoices');

        // Tickets
        Route::post('/tickets/{ticket}/convert-to-lead', [TicketController::class, 'convertToLead'])->name('crm.tickets.convert-to-lead');
        Route::post('/tickets/{ticket}/convert-to-deal', [TicketController::class, 'convertToDeal'])->name('crm.tickets.convert-to-deal');
        Route::post('/tickets/{ticket}/convert-to-contact', [TicketController::class, 'convertToContact'])->name('crm.tickets.convert-to-contact');
        Route::resource('tickets', TicketController::class)->names('crm.tickets');

        // Partners
        Route::post('/partners/{partner}/convert-to-lead', [PartnerController::class, 'convertToLead'])->name('crm.partners.convert-to-lead');
        Route::post('/partners/{partner}/convert-to-ticket', [PartnerController::class, 'convertToTicket'])->name('crm.partners.convert-to-ticket');
        Route::resource('partners', PartnerController::class)->names('crm.partners');

        // Marketing Automations
        Route::resource('marketing-automations', MarketingAutomationController::class)->names('crm.marketing-automations');

        // Communications (Unified Inbox)
        Route::resource('communications', CommunicationController::class)->names('crm.communications');

        // Engagements
        Route::post('/engagements/{engagement}/convert-to-deal', [EngagementController::class, 'convertToDeal'])->name('crm.engagements.convert-to-deal');
        Route::post('/engagements/{engagement}/convert-to-ticket', [EngagementController::class, 'convertToTicket'])->name('crm.engagements.convert-to-ticket');
        Route::resource('engagements', EngagementController::class)->names('crm.engagements');
    });

        // Profile
        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| Redirect /welcome to dashboard (authenticated users) or login (guests)
|--------------------------------------------------------------------------
*/
Route::get('/welcome', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Public (no auth)
|--------------------------------------------------------------------------
*/
Route::get('/error', fn () => Inertia::render('Error404'))->name('error.404');
