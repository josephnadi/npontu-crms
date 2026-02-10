# Leads Module - Package Contents

This zip file contains all files related to the Leads module in the CRM system.

## Package: leads-module.zip

### Frontend Files (Vue.js)

#### Pages (Inertia.js entry points)
- `resources/js/Pages/CRM/Leads/Index.vue` - Main leads listing page wrapper
- `resources/js/Pages/CRM/Leads/Create.vue` - Create lead page wrapper
- `resources/js/Pages/CRM/Leads/Edit.vue` - Edit lead page wrapper
- `resources/js/Pages/CRM/Leads/Show.vue` - View lead page wrapper

#### Views (Actual component implementations)
- `resources/js/views/CRM/Leads/LeadIndex.vue` - Leads listing with filters, pagination, and actions
- `resources/js/views/CRM/Leads/LeadCreate.vue` - Lead creation form
- `resources/js/views/CRM/Leads/LeadEdit.vue` - Lead editing form
- `resources/js/views/CRM/Leads/LeadShow.vue` - Lead details view
- `resources/js/views/CRM/Leads/LeadIndexTest.vue` - Test version of index
- `resources/js/views/CRM/Leads/LeadCreateTest.vue` - Test version of create

#### Components
- `resources/js/components/CRM/LeadConversionModal.vue` - Modal for converting leads to deals

### Backend Files (Laravel)

#### Controllers
- `app/Http/Controllers/CRM/LeadController.php` - Main controller handling all lead operations (CRUD, conversion)

#### Models
- `app/Models/Lead.php` - Lead model with relationships and business logic
- `app/Models/LeadSource.php` - Lead source model

#### Requests (Form Validation)
- `app/Http/Requests/StoreLeadRequest.php` - Validation rules for creating leads
- `app/Http/Requests/UpdateLeadRequest.php` - Validation rules for updating leads

#### Resources (API Transformers)
- `app/Http/Resources/LeadResource.php` - Resource transformer for lead data

#### Database Migrations
- `database/migrations/2026_02_09_121121_create_lead_sources_table.php` - Lead sources table schema
- `database/migrations/2026_02_09_122735_create_leads_table.php` - Leads table schema

#### Seeders
- `database/seeders/LeadSourceSeeder.php` - Seed lead sources (Website, Referral, etc.)
- `database/seeders/LeadSeeder.php` - Seed sample lead data

#### Routes
- `routes/web.php` - Contains lead routes (resource routes + conversion)

## Features Included

### Lead Management
- ✅ List leads with pagination
- ✅ Filter by status, source, and search
- ✅ Create new leads
- ✅ Edit existing leads
- ✅ View lead details
- ✅ Delete leads
- ✅ Lead scoring (0-100)
- ✅ Lead status tracking (new, contacted, qualified, converted, lost)

### Lead Conversion
- ✅ Convert leads to deals
- ✅ Create associated contact and company
- ✅ Automatically update lead status to "converted"

### Styling Updates
- ✅ Sidebar-style action icons (minimal, clean design)
- ✅ Hover effects matching sidebar menu items
- ✅ Responsive layout

## Installation Instructions

### 1. Extract Files
Extract the zip file to your Laravel project root directory.

### 2. Run Migrations
```bash
php artisan migrate
```

### 3. Run Seeders
```bash
php artisan db:seed --class=LeadSourceSeeder
php artisan db:seed --class=LeadSeeder
```

### 4. Install Frontend Dependencies (if needed)
```bash
npm install
npm run dev
```

### 5. Access the Module
Navigate to `/leads` in your application.

## Dependencies

### Backend (Laravel)
- Laravel 11+
- Inertia.js

### Frontend
- Vue 3 (Composition API)
- Vuetify 3
- Inertia.js Vue adapter

## Routes

The following routes are included:

```php
Route::resource('leads', LeadController::class);
Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
```

## Database Schema

### leads table
- id, first_name, last_name, email, phone, mobile
- company_name, job_title
- lead_source_id, status, score
- city, state, country, notes
- owner_id (assigned user)
- timestamps

### lead_sources table
- id, name, description
- timestamps

## Notes for Developer

1. The module uses Inertia.js for seamless SPA-like navigation
2. All validation is handled server-side in Request classes
3. The LeadResource formats data for frontend consumption
4. Lead conversion creates related Contact, Company, and Deal records
5. Action icons follow the sidebar design pattern for consistency

## Support

For questions or issues, contact the development team.

---
Generated: February 10, 2026
