# CRM Module Implementation Prompts
**Project:** Laravel + Vue + Inertia + Vuetify 3 CRM (Able Pro theme)  
**Use these prompts sequentially to build the complete CRM module**

## ⚙️ This project (Able Admin Archive)
- **Stack:** Laravel 11+, Vue 3 (Composition API), Inertia.js, **Vuetify 3**, TypeScript in places.
- **Layout:** Use `DashboardLayout` from `@/layouts/dashboard/DashboardLayout.vue`. Content goes in the default slot.
- **UI:** Use **Vuetify** components (`v-card`, `v-table`, `v-btn`, `v-text-field`, `v-select`, etc.) to match existing pages (e.g. `views/dashboards/default/DefaultDashboard.vue`). No Bootstrap classes.
- **Sidebar:** Menu is defined in `resources/js/layouts/dashboard/vertical-sidebar/sidebarItem.ts` (see `menu` interface: header, title, icon, to, children).
- **Icons:** Project uses `vue-tabler-icons` and custom icons (e.g. `custom-home-trend`). Use the same pattern for CRM items.
- **Database:** Default is SQLite (`.env`). PostgreSQL is optional; use Laravel migrations with `$table->json()` for JSON fields (portable across SQLite/MySQL/PostgreSQL).

---

## 📋 How to Use This Guide

1. **Copy each prompt** from this list
2. **Tag the recommended files** using @ mentions (e.g., @routes/web.php)
3. **Paste it to your AI assistant** (cursor, chatbot, etc.)
4. **Review the implementation** before moving to the next prompt
5. **Test each feature** as you go
6. **Mark completed prompts** with a ✅

## 📎 Context Management

Each prompt includes a **"Files to Tag"** section listing which files to include in your AI context using @ mentions. This ensures:
- ✅ Relevant code is in context
- ✅ AI understands existing patterns
- ✅ Consistent styling with your codebase
- ✅ Faster, more accurate implementations

**Example:**
```
📎 Files to Tag: @app/Models/User.php @routes/web.php
```
Then in your prompt, type: `@app/Models/User.php @routes/web.php [paste prompt here]`

**📖 Full Context Management Guide:** See `CRM-CONTEXT-GUIDE.md` for complete file tagging patterns for all 81 prompts.

---

## PHASE 0: Project Setup (2 Days)

### ✅ Prompt 0.1: Database Setup
**📎 Files to Tag:** @.env.example @config/database.php

```
Ensure the database is configured for the CRM module. This project defaults to SQLite; if you prefer PostgreSQL, create a database (e.g. "crm_db"), update .env with DB_CONNECTION=pgsql and credentials, and test with php artisan migrate. Otherwise keep SQLite and run migrations as usual.
```

### ✅ Prompt 0.2: Install Required Packages
**📎 Files to Tag:** @package.json @composer.json

```
Install any missing packages for the CRM module. This project already has apexcharts, vue3-apexcharts, vue-tabler-icons, and Vuetify. Add:
- Backend: intervention/image (optional, for avatar uploads)
- Frontend: vuedraggable@next (kanban), dayjs (dates)

Show the exact npm/composer commands. Do not duplicate existing deps.
```

### ✅ Prompt 0.3: Create Folder Structure
**📎 Files to Tag:** None (just creating directories)

```
Create the complete folder structure for the CRM module:
- Backend: Controllers, Requests, Resources, Policies in app/Http/Controllers/CRM/, app/Http/Requests/CRM/, app/Http/Resources/CRM/, app/Policies/CRM/
- Frontend: Pages in resources/js/Pages/CRM/ (e.g. Contacts, Clients, Leads, Deals, Activities, Dashboard). Components in resources/js/components/CRM/ (or under a shared components path used by this project).
- Database: Seeders in database/seeders/CRM/

Match existing project structure (e.g. layouts under resources/js/layouts/, Pages under resources/js/Pages/).
```

### ✅ Prompt 0.4: Setup CRM Routes
**📎 Files to Tag:** @routes/web.php @bootstrap/app.php

```
Create routes/crm.php with all CRM routes (dashboard, contacts, clients, leads, deals, activities). Register it in bootstrap/app.php: inside withRouting(), add a then: function() that registers Route::middleware(['web','auth'])->prefix('crm')->group(base_path('routes/crm.php')). Laravel 11 uses withRouting(web: ..., commands: ..., health: ..., then: ...). Keep existing web and middleware setup.
```

### ✅ Prompt 0.5: Update Sidebar Navigation
**📎 Files to Tag:** @resources/js/layouts/dashboard/vertical-sidebar/sidebarItem.ts

```
Update the sidebar menu in resources/js/layouts/dashboard/vertical-sidebar/sidebarItem.ts to add a CRM section. Use the existing menu interface (header, title, icon, to, children). Add items: CRM (header), Dashboard (/crm/dashboard), Contacts (/crm/contacts), Clients (/crm/clients), Leads (/crm/leads), Deals (/crm/deals-pipeline or /crm/deals), Activities (/crm/activities). Use the same icon pattern as existing items (e.g. custom-* or vue-tabler-icons names used in this project).
```

---

## PHASE 1: Contact Management (3-4 Days)

### ✅ Prompt 1.1: Create Contacts Migration
**📎 Files to Tag:** @database/migrations/ (any existing migration as reference)

```
Create a comprehensive migration for the contacts table with all fields from the CRM roadmap including:
- Personal info (first_name, last_name, email, phone, mobile)
- Job details (job_title, company_name)
- Address fields (address, city, state, country, postal_code)
- Relationships (client_id, owner_id, created_by, updated_by)
- Additional fields (status, avatar_url, notes, tags as JSONB, custom_fields as JSONB)
- Soft deletes and timestamps
- All necessary indexes for performance

Use $table->json() for JSON columns (portable across SQLite/MySQL/PostgreSQL).
```

### ✅ Prompt 1.2: Create Contact Model
**📎 Files to Tag:** @app/Models/User.php @CRM-MODULE-ROADMAP.md

```
Create the Contact model in app/Models/Contact.php with:
- All fillable fields
- Relationships: belongsTo(Client), belongsTo(User as owner), hasMany(Deal), morphMany(Activity)
- Casts for JSONB fields (tags, custom_fields)
- Accessor for full_name
- Scopes: active(), search()
- Soft deletes

Follow Laravel 11+ best practices.
```

### ✅ Prompt 1.3: Create Contact Form Requests
**📎 Files to Tag:** @app/Models/Contact.php

```
Create two form request classes:
1. app/Http/Requests/CRM/StoreContactRequest.php - for creating contacts
2. app/Http/Requests/CRM/UpdateContactRequest.php - for updating contacts

Include proper validation rules:
- first_name and last_name required
- email unique and valid format
- phone/mobile optional
- all other fields optional with appropriate validation

Include authorization logic.
```

### ✅ Prompt 1.4: Create Contact Resource
**📎 Files to Tag:** @app/Models/Contact.php

```
Create app/Http/Resources/CRM/ContactResource.php to format contact data for API responses. Include:
- All contact fields
- Conditional loading of relationships (client, owner, deals, activities)
- Formatted dates
- Default avatar if none exists

Follow Laravel Resource conventions.
```

### ✅ Prompt 1.5: Create Contact Controller
**📎 Files to Tag:** @app/Models/Contact.php @app/Http/Requests/CRM/StoreContactRequest.php @app/Http/Requests/CRM/UpdateContactRequest.php @app/Http/Resources/CRM/ContactResource.php @app/Http/Controllers/Controller.php

```
Create app/Http/Controllers/CRM/ContactController.php with full CRUD operations:
- index() - List contacts with search, filters, pagination
- create() - Show create form
- store() - Save new contact
- show() - Display contact with relationships
- edit() - Show edit form
- update() - Update existing contact
- destroy() - Soft delete contact

Use Inertia::render() for views, eager load relationships to avoid N+1 queries, and return proper flash messages.
```

### ✅ Prompt 1.6: Create Contact Index Page
**📎 Files to Tag:** @resources/js/layouts/dashboard/DashboardLayout.vue @resources/js/Pages/Dashboard/Default.vue @resources/js/views/dashboards/default/DefaultDashboard.vue

```
Create resources/js/Pages/CRM/Contacts/Index.vue:
- Use DashboardLayout from @/layouts/dashboard/DashboardLayout.vue
- Page header with breadcrumbs (e.g. BaseBreadcrumb or similar) and "Add Contact" button
- Search input and status filter
- Data table (Vuetify v-table) with columns: Name (with avatar), Email, Phone, Company, Status, Owner, Actions
- Pagination (v-pagination or existing pattern)
- Action buttons: View, Edit, Delete (with confirmation)
- Use Inertia Link for navigation
- Use Vuetify components (v-card, v-btn, v-text-field, v-select) matching existing dashboard patterns

Use Vue 3 Composition API with <script setup>.
```

### ✅ Prompt 1.7: Create Contact Form Component
**📎 Files to Tag:** @resources/js/Pages/Dashboard/Index.vue (for form styling reference)

```
Create resources/js/Components/CRM/ContactForm.vue as a reusable form for both creating and editing contacts:
- Props: contact (for edit mode), isEdit boolean
- Use Inertia useForm
- Include all fields: first_name, last_name, email, phone, mobile, job_title, company_name, status, address, city, state, country, postal_code, notes
- Show validation errors
- Submit button with loading state
- Cancel button
- Use Vuetify form components (v-text-field, v-select, v-textarea) and validation error display. Match existing form patterns in the project.

Two-column layout (v-row/v-col) for better UX.
```

### ✅ Prompt 1.8: Create Contact Create Page
**📎 Files to Tag:** @resources/js/Pages/CRM/Contacts/Index.vue @resources/js/Components/CRM/ContactForm.vue

```
Create resources/js/Pages/CRM/Contacts/Create.vue:
- Use DashboardLayout
- PageHeader with breadcrumbs
- Import and use ContactForm component
- Pass isEdit=false

Keep it simple and clean.
```

### ✅ Prompt 1.9: Create Contact Edit Page
**📎 Files to Tag:** @resources/js/Pages/CRM/Contacts/Create.vue @resources/js/Components/CRM/ContactForm.vue

```
Create resources/js/Pages/CRM/Contacts/Edit.vue:
- Use DashboardLayout
- PageHeader with breadcrumbs
- Import and use ContactForm component
- Pass contact prop and isEdit=true

Similar to Create page but for editing.
```

### ✅ Prompt 1.10: Create Contact Show Page
**📎 Files to Tag:** @resources/js/Pages/CRM/Contacts/Index.vue @resources/js/Pages/Dashboard/Index.vue

```
Create resources/js/Pages/CRM/Contacts/Show.vue for contact detail view:
- Use DashboardLayout
- Display contact info in a card with avatar
- Show all contact details organized in sections (Personal Info, Address, Notes)
- Action buttons: Edit, Delete
- Placeholder sections for related Deals and Activities (we'll populate these later)
- Use Vuetify v-card and spacing consistent with views/dashboards/default.

Make it visually appealing with proper spacing and icons (vue-tabler-icons or project icons).
```

### ✅ Prompt 1.11: Test Contact Module
**📎 Files to Tag:** None (testing phase)

```
Run migrations and test the contact module:
1. Run php artisan migrate
2. Test creating a new contact through the UI
3. Test viewing the contact list
4. Test searching and filtering
5. Test editing a contact
6. Test viewing contact details
7. Test deleting a contact

Report any issues found.
```

---

## PHASE 2: Client Management (2-3 Days)

### ✅ Prompt 2.1: Create Clients Migration
**📎 Files to Tag:** @database/migrations/[contacts_migration].php @CRM-MODULE-ROADMAP.md

```
Create a migration for the clients table with fields:
- Company info (name, industry, website, email, phone)
- Address fields (address, city, state, country, postal_code)
- Business details (annual_revenue, employee_count)
- Status (prospect/active/inactive)
- Additional (logo_url, description, tags JSONB, custom_fields JSONB)
- Relationships (owner_id, created_by, updated_by)
- Soft deletes and timestamps
- Appropriate indexes

Use Laravel migration conventions and indexes for foreign keys and frequently filtered columns.
```

### ✅ Prompt 2.2: Create Client Model and Resources
**📎 Files to Tag:** @app/Models/Contact.php @app/Http/Controllers/CRM/ContactController.php @app/Http/Resources/CRM/ContactResource.php

```
Create:
1. Client model (app/Models/Client.php) with:
   - Relationships: hasMany(Contact), hasMany(Deal), morphMany(Activity), belongsTo(User as owner)
   - Scopes for filtering
   - Casts for JSONB fields
   
2. ClientResource (app/Http/Resources/CRM/ClientResource.php)
3. StoreClientRequest (app/Http/Requests/CRM/StoreClientRequest.php)
4. UpdateClientRequest (app/Http/Requests/CRM/UpdateClientRequest.php)
5. ClientController (app/Http/Controllers/CRM/ClientController.php) with full CRUD

Similar structure to Contact module.
```

### ✅ Prompt 2.3: Create Client Frontend Pages
**📎 Files to Tag:** @resources/js/Pages/CRM/Contacts/Index.vue @resources/js/Pages/CRM/Contacts/Show.vue @resources/js/Components/CRM/ContactForm.vue

```
Create all client pages matching the contact module structure:
1. resources/js/Pages/CRM/Clients/Index.vue - List clients with search
2. resources/js/Pages/CRM/Clients/Create.vue - Create client form
3. resources/js/Pages/CRM/Clients/Edit.vue - Edit client form
4. resources/js/Pages/CRM/Clients/Show.vue - Client detail with related contacts and deals

Use Vuetify and the same layout/table/form patterns as the Contacts module, adapted for company data.
```

### ✅ Prompt 2.4: Create Client Selector Component
**📎 Files to Tag:** @app/Models/Client.php @app/Http/Resources/CRM/ClientResource.php

```
Create resources/js/Components/CRM/ClientSelector.vue:
- Searchable dropdown for selecting a client
- Props: modelValue, label
- Emits: update:modelValue
- Fetches clients via API or receives as prop
- Use for linking contacts to clients

Make it reusable and styled with Vuetify to match the rest of the app.
```

### ✅ Prompt 2.5: Update Contact Forms with Client Selection
**📎 Files to Tag:** @resources/js/Components/CRM/ContactForm.vue @resources/js/Components/CRM/ClientSelector.vue

```
Update the ContactForm component to include a client selector:
- Add client_id field
- Use ClientSelector component
- Show selected client name
- Allow clearing selection

Test that contacts can be linked to clients.
```

### ✅ Prompt 2.6: Update Contacts Migration
**📎 Files to Tag:** @database/migrations/[contacts_migration].php @database/migrations/[clients_migration].php

```
Since we created contacts before clients, we need to ensure the foreign key is properly set. Verify the contacts migration has client_id with proper foreign key constraint to clients table. If not, create a new migration to add it.
```

---

## PHASE 3: Lead Management (3-4 Days)

### ✅ Prompt 3.1: Create Lead Sources Migration and Seeder
**📎 Files to Tag:** @CRM-MODULE-ROADMAP.md

```
Create:
1. Migration for lead_sources table (id, name, description, is_active)
2. Seeder to populate with default sources: Website Form, Referral, Trade Show, Cold Call, Social Media, Email Campaign, Partner, Event, Other

Run the seeder after migration.
```

### ✅ Prompt 3.2: Create Leads Migration
**📎 Files to Tag:** @database/migrations/[contacts_migration].php @database/migrations/[lead_sources_migration].php @CRM-MODULE-ROADMAP.md

```
Create migration for leads table with:
- Personal details (first_name, last_name, email, phone, mobile)
- Company info (company_name, job_title)
- Lead tracking (lead_source_id, status, score 0-100)
- Address fields
- Conversion tracking (converted_to_contact_id, converted_to_deal_id, converted_at)
- Additional (notes, tags JSONB, custom_fields JSONB)
- Relationships (owner_id, created_by, updated_by)
- Soft deletes
- Indexes

Status values: new, contacted, qualified, converted, lost
```

### ✅ Prompt 3.3: Create Lead Model and Resources
**📎 Files to Tag:** @app/Models/Contact.php @app/Models/Client.php @CRM-MODULE-ROADMAP.md

```
Create:
1. LeadSource model (simple model for reference table)
2. Lead model with:
   - Relationships to LeadSource, Contact (converted_to), Deal (converted_to), User (owner), Activities
   - Scopes: byStatus(), qualified(), notConverted()
   - Accessor for full_name
3. LeadResource
4. StoreLeadRequest
5. UpdateLeadRequest

Follow established patterns.
```

### ✅ Prompt 3.4: Create Lead Controller with Conversion
**📎 Files to Tag:** @app/Http/Controllers/CRM/ContactController.php @app/Models/Lead.php @app/Models/Contact.php @CRM-MODULE-ROADMAP.md

```
Create app/Http/Controllers/CRM/LeadController.php with:
- Standard CRUD methods (index, create, store, show, edit, update, destroy)
- Special method: convert() - Converts lead to contact and optionally creates a deal

The convert method should:
- Validate input (create_contact, create_deal, deal_title, deal_value, deal_stage_id, expected_close_date)
- Create a contact from lead data
- Optionally create a deal linked to the new contact
- Update lead status to 'converted' and set conversion references
- Return success with redirect

This is a key feature!
```

### ✅ Prompt 3.5: Create Lead Index Page
**📎 Files to Tag:** @resources/js/Pages/CRM/Contacts/Index.vue @able-pro-analysis.md

```
Create resources/js/Pages/CRM/Leads/Index.vue:
- List view with search and filters (status, source)
- Table columns: Name, Email, Company, Source, Status (with colored badges), Score, Owner, Actions
- Action buttons: View, Edit, Convert (if status is qualified), Delete
- Use Vuetify (v-table, v-chip/v-badge for status). Status colors: new=info, contacted=warning, qualified=primary, converted=success, lost=error
```

### ✅ Prompt 3.6: Create Lead Form Pages
**📎 Files to Tag:** @resources/js/Components/CRM/ContactForm.vue @resources/js/Pages/CRM/Contacts/Show.vue

```
Create:
1. resources/js/Pages/CRM/Leads/Create.vue - Form to create new lead
2. resources/js/Pages/CRM/Leads/Edit.vue - Form to edit lead
3. resources/js/Pages/CRM/Leads/Show.vue - Lead detail view

Forms should include:
- Personal info, company, lead source dropdown, status dropdown, score slider (0-100), notes
- Use Vuetify form components

Show page should display all info and show a "Convert Lead" button if status is qualified.
```

### ✅ Prompt 3.7: Create Lead Conversion Modal Component ⭐
**📎 Files to Tag:** @CRM-MODULE-ROADMAP.md @able-pro-analysis.md

```
Create resources/js/Components/CRM/LeadConversionModal.vue:
- Props: lead (object), dealStages (array), show (boolean)
- Emits: close, converted
- Show lead summary (name, email, company)
- Checkbox: "Create Contact" (always checked, disabled - required)
- Checkbox: "Create Deal" (optional)
- If "Create Deal" checked, show fields:
  * Deal Title (required)
  * Deal Value (required, number input with $ prefix)
  * Expected Close Date (date picker)
  * Pipeline Stage (dropdown of deal stages)
- Submit button: "Convert Lead"
- Use Vuetify v-dialog
- Handle form submission with Inertia

This is a critical component for lead conversion workflow!
```

### ✅ Prompt 3.8: Integrate Conversion Modal in Lead Pages
**📎 Files to Tag:** @resources/js/Pages/CRM/Leads/Show.vue @resources/js/Pages/CRM/Leads/Index.vue @resources/js/Components/CRM/LeadConversionModal.vue

```
Update resources/js/Pages/CRM/Leads/Show.vue and Index.vue:
- Add "Convert Lead" button (only show if lead status is 'qualified')
- Import LeadConversionModal component
- Show modal when button clicked
- Pass lead data and deal stages
- Handle conversion success (refresh page, show success message)

Test the complete lead-to-customer conversion workflow.
```

---

## PHASE 4: Deal & Pipeline Management (4-5 Days)

### ✅ Prompt 4.1: Create Deal Stages Migration and Seeder
```
Create:
1. Migration for deal_stages table (id, name, order_column, probability 0-100, color, is_active)
2. Seeder with default stages:
   - Qualification (order: 1, probability: 20, color: info)
   - Proposal (order: 2, probability: 40, color: primary)
   - Negotiation (order: 3, probability: 60, color: warning)
   - Closing (order: 4, probability: 80, color: success)
   - Won (order: 5, probability: 100, color: success)
   - Lost (order: 6, probability: 0, color: danger)

Run the seeder.
```

### ✅ Prompt 4.2: Create Deals Migration
```
Create migration for deals table with:
- Deal info (title, description, value, currency default 'USD')
- Relationships (deal_stage_id, contact_id, client_id, owner_id)
- Dates (expected_close_date, actual_close_date)
- Status (open/won/lost, default 'open')
- Probability (0-100)
- Lost reason (text, nullable)
- Additional (tags JSONB, custom_fields JSONB)
- Audit fields (created_by, updated_by)
- Soft deletes, timestamps
- Indexes on stage_id, contact_id, client_id, owner_id, status, expected_close_date
```

### ✅ Prompt 4.3: Create Deal Model and Resources
```
Create:
1. DealStage model (simple reference model)
2. Deal model with:
   - Relationships: belongsTo(DealStage, Contact, Client, User as owner), morphMany(Activity)
   - Scopes: open(), won(), lost(), byStage()
   - Accessors for formatted value
3. DealResource with conditional loading
4. StoreDealRequest with validation
5. UpdateDealRequest

Ensure value is validated as numeric with 2 decimal places.
```

### ✅ Prompt 4.4: Create Deal Controller
```
Create app/Http/Controllers/CRM/DealController.php with:
- index() - List view with filters
- pipeline() - Kanban view (returns deals grouped by stage)
- create() - Show form with contact/client selectors
- store() - Create deal
- show() - Deal detail with activities
- edit() - Edit form
- update() - Update deal (handle stage changes)
- destroy() - Soft delete
- updateStage() - Special method for drag-and-drop stage updates
- updateStatus() - Mark as won/lost

Return deal stages and contacts in pipeline() method for the kanban board.
```

### ✅ Prompt 4.5: Create Deal Card Component
```
Create resources/js/Components/CRM/DealCard.vue:
- Props: deal (object)
- Display: Deal title, contact name (with icon), deal value (formatted with $), expected close date
- Clickable to view deal details
- Use Vuetify v-card
- Add hover effect (slight shadow and transform)
- Cursor should be 'move' for dragging

This will be used in the kanban board. Make it compact but informative.
```

### ✅ Prompt 4.6: Create Deal Pipeline Kanban Component ⭐
```
Create resources/js/Components/CRM/DealPipeline.vue:
- Props: stages (array), deals (array)
- Use VueDraggable for drag-and-drop functionality
- Create columns for each stage
- Each column shows:
  * Stage name and deal count badge
  * Total value of deals in that stage
  * List of DealCard components
- Handle drag-and-drop between stages:
  * On drop, update deal's deal_stage_id via PUT request
  * Use Inertia router with preserveScroll
- Style columns to be equal width with scrollable content
- Use Vuetify color props for stage badges
- Show empty state if no deals in a stage

This is the centerpiece of the CRM! Make it smooth and intuitive.
```

### ✅ Prompt 4.7: Create Pipeline Page
```
Create resources/js/Pages/CRM/Deals/Pipeline.vue:
- Use DashboardLayout
- PageHeader with "Deal Pipeline" title and "Add Deal" button
- Import and use DealPipeline component
- Pass stages and deals from props
- Add filters at top: Owner, Expected Close Date Range
- Show total pipeline value as a metric card above the pipeline

Full-width layout for maximum space.
```

### ✅ Prompt 4.8: Create Deal List Page
```
Create resources/js/Pages/CRM/Deals/Index.vue:
- Alternative table view of deals (some users prefer lists)
- Columns: Title, Contact, Client, Stage, Value, Expected Close, Owner, Actions
- Search and filters (stage, status, owner)
- Sort by value, close date
- Action buttons: View, Edit, Delete
- Use Vuetify v-table
```

### ✅ Prompt 4.9: Create Deal Form Pages
```
Create:
1. resources/js/Pages/CRM/Deals/Create.vue - Create deal form
2. resources/js/Pages/CRM/Deals/Edit.vue - Edit deal form
3. resources/js/Pages/CRM/Deals/Show.vue - Deal detail page

Forms should include:
- Title, description
- Value (currency input), currency selector
- Contact selector (searchable dropdown)
- Client selector (optional)
- Deal stage dropdown
- Expected close date
- Probability (auto-set based on stage, but editable)
- Notes

Show page should display:
- All deal info in cards
- Contact and client details
- Activity timeline (placeholder for now)
- Win/Loss buttons if deal is open
- Edit/Delete actions
```

### ✅ Prompt 4.10: Update Lead Conversion to Include Deal Stages
```
Update the LeadController's convert method and LeadConversionModal component to properly fetch and use deal stages. Ensure deal stages are passed to the modal when converting a lead.

Test the complete flow: Create Lead → Qualify → Convert to Contact + Deal → See deal in pipeline.
```

### ✅ Prompt 4.11: Test Deal Pipeline
```
Test the complete deal management system:
1. Create multiple deals in different stages
2. Test drag-and-drop between stages in the pipeline
3. Verify deal stage updates persist
4. Test filtering deals
5. Test marking deals as won/lost
6. Test the list view
7. Ensure all links between contacts, clients, and deals work

This is a critical feature - make sure it's solid!
```

---

## PHASE 5: Activity Management (3-4 Days)

### ✅ Prompt 5.1: Create Activities Migration
```
Create migration for activities table with:
- Activity info (type, subject, description)
- Types: call, meeting, email, task, note
- Dates (activity_date, due_date, completed_at)
- Status (pending/completed/cancelled)
- Duration in minutes (nullable)
- Polymorphic relationship (activityable_type, activityable_id) - can link to Contact, Lead, Deal, or Client
- Additional (tags JSONB, custom_fields JSONB)
- Relationships (owner_id, created_by, updated_by)
- Soft deletes
- Indexes on type, status, owner_id, polymorphic fields, due_date
```

### ✅ Prompt 5.2: Create Activity Model and Resources
```
Create:
1. Activity model with:
   - morphTo relationship (activityable)
   - belongsTo(User as owner)
   - Scopes: pending(), completed(), byType(), overdue()
   - Method: markAsCompleted()
2. ActivityResource
3. StoreActivityRequest
4. UpdateActivityRequest

Validate that activityable_type is one of allowed models.
```

### ✅ Prompt 5.3: Create Activity Controller
```
Create app/Http/Controllers/CRM/ActivityController.php with:
- index() - List all activities with filters (type, status, date range)
- create() - Form to create activity (with related entity selection)
- store() - Save activity
- show() - Activity detail
- edit() - Edit form
- update() - Update activity
- destroy() - Delete activity
- complete() - Mark activity as completed
- calendar() - Return activities for calendar view (optional)

Include proper eager loading.
```

### ✅ Prompt 5.4: Create Activity Timeline Component ⭐
```
Create resources/js/Components/CRM/ActivityTimeline.vue:
- Props: activities (array), relatedTo (object with type and id)
- Display activities in chronological order (most recent first)
- Each activity shows:
  * Icon based on type (phone, users, mail, checkbox, note)
  * Color badge based on type
  * Subject and description
  * Owner name and date
  * Vertical timeline line connecting activities
- "Add Activity" button at top
- Quick-add form that slides in:
  * Type selector
  * Subject input
  * Description textarea
  * Submit directly via Inertia
- Empty state if no activities
- Use Vuetify components

This component will be embedded in Contact, Client, Lead, and Deal detail pages!
```

### ✅ Prompt 5.5: Create Activity Index Page
```
Create resources/js/Pages/CRM/Activities/Index.vue:
- List all activities with filters
- Group by status: Pending, Completed
- Tabs or sections for different activity types
- Table view with columns: Type (icon), Subject, Related To (Contact/Lead/Deal), Due Date, Status, Owner, Actions
- Mark as complete button
- Edit and delete actions
- Use Vuetify components
```

### ✅ Prompt 5.6: Create Activity Form Pages
```
Create:
1. resources/js/Pages/CRM/Activities/Create.vue - Create activity
2. resources/js/Pages/CRM/Activities/Edit.vue - Edit activity

Form should include:
- Activity type selector (with icons)
- Subject, description
- Related to selector (choose entity type then select specific record)
- Activity date/time
- Due date (for tasks)
- Duration (for calls/meetings)
- Status
- Use Vuetify form components
```

### ✅ Prompt 5.7: Integrate Activity Timeline in Entity Pages
```
Update these pages to include the ActivityTimeline component:
1. resources/js/Pages/CRM/Contacts/Show.vue
2. resources/js/Pages/CRM/Clients/Show.vue
3. resources/js/Pages/CRM/Leads/Show.vue
4. resources/js/Pages/CRM/Deals/Show.vue

Add a card section titled "Activity Timeline" and pass:
- activities: loaded from the controller (last 20)
- relatedTo: { type: 'Contact', id: contact.id } (adjust for each entity)

This creates a unified activity tracking system across all CRM entities!
```

### ✅ Prompt 5.8: Update Controllers to Load Activities
```
Update the show() methods in these controllers to eager load activities:
- ContactController
- ClientController
- LeadController
- DealController

Use: ->with(['activities' => fn($q) => $q->with('owner')->latest()->take(20)])

Ensure activities are included in the resources when loaded.
```

### ✅ Prompt 5.9: Create Activity Calendar View (Optional)
```
Create resources/js/Pages/CRM/Activities/Calendar.vue:
- Monthly calendar view showing activities by date
- Color-coded by activity type
- Click on activity to view/edit
- Click on date to create new activity for that date
- Use a simple calendar component or build a basic one

This is optional but useful for seeing meetings and calls.
```

### ✅ Prompt 5.10: Test Activity System
```
Test the complete activity management:
1. Create activities of different types (call, meeting, email, task, note)
2. Link activities to different entities (contact, lead, deal, client)
3. View activity timeline on entity detail pages
4. Test quick-add form in timeline
5. Mark tasks as complete
6. Test activity list page with filters
7. Test overdue task highlighting

Ensure polymorphic relationships work correctly!
```

---

## PHASE 6: Dashboard & Reports (3-4 Days)

### ✅ Prompt 6.1: Create CRM Dashboard Controller
```
Create app/Http/Controllers/CRM/DashboardController.php with index() method that returns:
- Metrics:
  * Total contacts count
  * Active leads count (not converted)
  * Open deals count and total value
  * Deals won this month (count and value)
  * Win rate percentage
- Charts data:
  * Deals by stage (for pipeline funnel chart)
  * Lead sources breakdown (for pie chart)
  * Monthly revenue trend (last 6 months)
  * Activity breakdown by type
- Recent activities (last 10)
- Top performers (users with most deals won)

Optimize queries with proper aggregations and caching where appropriate.
```

### ✅ Prompt 6.2: Create Metric Card Component
```
Create resources/js/Components/CRM/MetricCard.vue:
- Props: title, value, change (percentage), icon, variant (color)
- Display metric in a Vuetify card consistent with dashboard stat cards (e.g. views/widgets/statistics)
- Show icon with colored background
- Display value prominently
- Show trend indicator (up/down arrow with percentage) in appropriate color
- Click to view more details (optional link prop)

Reusable for all dashboard metrics.
```

### ✅ Prompt 6.3: Create Dashboard Charts Components
```
Create chart components using ApexCharts:

1. resources/js/Components/CRM/PipelineFunnelChart.vue
   - Bar chart showing number of deals in each stage
   - Use theme primary color (Vuetify/theme)
   - Clickable bars to filter deals

2. resources/js/Components/CRM/LeadSourceChart.vue
   - Pie/donut chart showing lead distribution by source
   - Multiple colors for different sources
   - Show percentages

3. resources/js/Components/CRM/RevenueChart.vue
   - Line chart showing monthly revenue trend
   - Show expected revenue (from open deals) vs actual (from won deals)
   - Two series on same chart

All charts should:
- Use theme/Vuetify color palette
- Be responsive
- Have proper legends and tooltips
- Match the dashboard aesthetic
```

### ✅ Prompt 6.4: Create CRM Dashboard Page ⭐
```
Create resources/js/Pages/CRM/Dashboard/Index.vue:
- Use DashboardLayout
- PageHeader with "CRM Dashboard" title
- First row: 4 metric cards (Contacts, Leads, Deals, Revenue)
- Second row: Pipeline funnel chart (col-lg-8) + Lead sources chart (col-lg-4)
- Third row: Revenue trend chart (col-lg-9) + Top performers list (col-lg-3)
- Fourth row: Recent activities feed (col-12)
- Quick action buttons: Add Contact, Add Lead, Add Deal
- Use Vuetify v-row/v-col and v-card

Make it visually appealing and informative at a glance!
```

### ✅ Prompt 6.5: Create Top Performers Component
```
Create resources/js/Components/CRM/TopPerformersCard.vue:
- Props: performers (array of users with metrics)
- Show user avatar, name
- Display: Number of deals won, Total revenue
- Rank indicator (1st, 2nd, 3rd with different colors/icons)
- Use Vuetify list/card styling
- Click on user to filter dashboard by that user (optional)

Motivational leaderboard style!
```

### ✅ Prompt 6.6: Create Recent Activities Feed Component
```
Create resources/js/Components/CRM/RecentActivitiesCard.vue:
- Props: activities (array)
- Compact timeline view
- Show: Icon, subject, related entity (with link), time ago
- "View All" link to activities page
- Use Vuetify v-card
- Max height with scroll if too many

Similar to ActivityTimeline but more compact for dashboard.
```

### ✅ Prompt 6.7: Add Dashboard Link to Navigation
```
Update routes/crm.php to ensure dashboard route is first:
- Route::get('/dashboard', [DashboardController::class, 'index'])->name('crm.dashboard');

Update sidebar so "CRM Dashboard" link goes to /crm/dashboard

Test that dashboard loads with all metrics and charts.
```

### ✅ Prompt 6.8: Create Reports Pages (Optional Enhancement)
```
Create additional report pages:

1. resources/js/Pages/CRM/Reports/Pipeline.vue
   - Detailed pipeline report
   - Filter by date range, owner, stage
   - Export to CSV option

2. resources/js/Pages/CRM/Reports/LeadConversion.vue
   - Lead conversion metrics
   - Conversion rate by source
   - Time to conversion analysis

3. resources/js/Pages/CRM/Reports/SalesPerformance.vue
   - Individual and team performance
   - Monthly/quarterly trends
   - Goal tracking

These are advanced features - implement if time permits.
```

### ✅ Prompt 6.9: Add Date Range Filter to Dashboard
```
Add a date range filter to the CRM dashboard:
- Dropdown with options: Today, This Week, This Month, Last Month, This Quarter, Custom Range
- Update all metrics and charts when filter changes
- Persist selection in URL query params
- Use Vuetify v-select or v-menu

Allows users to analyze different time periods.
```

### ✅ Prompt 6.10: Test Dashboard
```
Test the complete dashboard:
1. Verify all metrics display correctly
2. Test all charts render properly
3. Click through links to detail pages
4. Test date range filtering
5. Verify top performers list
6. Check recent activities feed
7. Test on different screen sizes (responsive)
8. Verify performance (should load in < 2 seconds)

Ensure dashboard provides valuable insights at a glance!
```

---

## PHASE 7: Polish & Enhancement (3-4 Days)

### ✅ Prompt 7.1: Add Search Functionality
```
Implement global CRM search:
1. Add search input in header (DashboardLayout or separate CRM header)
2. Create SearchController with search() method
3. Search across: Contacts, Clients, Leads, Deals
4. Return results grouped by type
5. Create search results page showing all matches
6. Use Laravel/DB full-text search where available (e.g. PostgreSQL) or simple ILIKE/LIKE for portability
7. Highlight matching terms
8. Recent searches dropdown (stored in localStorage)

Use Vuetify for search results layout.
```

### ✅ Prompt 7.2: Add Advanced Filters
```
Create a reusable filter component:
- resources/js/Components/CRM/AdvancedFilters.vue
- Multi-select filters for common fields (status, owner, date range, tags)
- Save filter presets (store in database)
- Clear all filters button
- Apply filters updates URL query params
- Use Vuetify form components

Apply to all list pages (contacts, clients, leads, deals, activities).
```

### ✅ Prompt 7.3: Add Export Functionality
```
Implement CSV export for all list views:
1. Add export button to each index page
2. Create export methods in controllers
3. Use Laravel Excel package or native CSV generation
4. Export visible columns only (respect current filters)
5. Include export of contacts, clients, leads, deals, activities
6. Option to export all or just current page
7. Background job for large exports with email notification

Users need to export data for external use!
```

### ✅ Prompt 7.4: Add Bulk Actions
```
Implement bulk operations on list pages:
- Checkboxes on each table row
- "Select All" checkbox in header
- Bulk actions dropdown: Delete, Change Owner, Update Status, Add Tag, Export
- Confirmation modal for destructive actions
- Progress indicator for bulk operations
- Success/error messages

Apply to contacts, clients, leads, deals lists.
```

### ✅ Prompt 7.5: Setup Notifications System
```
Create Laravel notifications for key events:
1. New lead assigned to user
2. Deal moved to closing stage
3. Task due reminder (1 day before, 1 hour before)
4. Deal won/lost
5. Contact/client assigned to user

Create notification templates:
- app/Notifications/CRM/LeadAssigned.php
- app/Notifications/CRM/TaskDueReminder.php
- app/Notifications/CRM/DealWon.php
- etc.

Send via mail and database channels. Show in header notification dropdown.
```

### ✅ Prompt 7.6: Create Email Templates
```
Create Blade email templates for notifications:
- resources/views/emails/crm/lead-assigned.blade.php
- resources/views/emails/crm/task-reminder.blade.php
- resources/views/emails/crm/deal-won.blade.php

Use professional, branded design. Include:
- Clear subject and message
- Action button (link to relevant page)
- Contact information
- Unsubscribe option

Test sending emails from local environment.
```

### ✅ Prompt 7.7: Add User Assignment Features
```
Enhance ownership and assignment:
1. Create "Assign to" dropdown on all entities
2. Allow reassigning contacts, leads, deals to different users
3. Log assignment changes in activities
4. Notification to new assignee
5. Filter by "My Items" vs "All Items" vs "Team Items"
6. Dashboard shows only assigned items by default (with option to see all)

Important for team collaboration!
```

### ✅ Prompt 7.8: Add Tags Management
```
Create comprehensive tagging system:
1. Create app/Http/Controllers/CRM/TagController.php
2. Create resources/js/Pages/CRM/Settings/Tags.vue (manage tags)
3. Create reusable TagInput component with:
   - Type-ahead search
   - Create new tags on the fly
   - Color coding
   - Remove tags
4. Add tag filters to all list pages
5. Show tags on all entity cards

Tags are crucial for organization!
```

### ✅ Prompt 7.9: Implement Permissions & Authorization
```
Create authorization policies:
1. app/Policies/CRM/ContactPolicy.php
2. app/Policies/CRM/ClientPolicy.php
3. app/Policies/CRM/LeadPolicy.php
4. app/Policies/CRM/DealPolicy.php
5. app/Policies/CRM/ActivityPolicy.php

Rules:
- Users can always view their assigned items
- Managers can view all items
- Admins can do everything
- Users can only edit/delete their own items (unless manager/admin)

Register policies in AppServiceProvider.
Apply authorization checks in controllers and blade/vue templates.
```

### ✅ Prompt 7.10: Add Custom Fields Support
```
Implement custom fields feature:
1. Create custom_fields JSONB column in all entity tables (already exists)
2. Create resources/js/Pages/CRM/Settings/CustomFields.vue
3. Allow admins to define custom fields per entity type:
   - Field name, type (text, number, date, dropdown), required, options
4. Create CustomFieldInput component to render custom fields in forms
5. Store custom field values in JSONB column
6. Display custom fields in detail pages
7. Allow filtering and searching by custom fields

This adds flexibility to the CRM!
```

### ✅ Prompt 7.11: Create Onboarding Tour
```
Add guided tour for new users:
1. Install intro.js or driver.js
2. Create welcome screen on first login
3. Interactive tour highlighting:
   - How to add contacts
   - How to create leads
   - How to use the pipeline
   - How to log activities
   - How to use the dashboard
4. "Skip" and "Next" buttons
5. Store completion status in user settings

Improves user adoption!
```

### ✅ Prompt 7.12: Performance Optimization
```
Optimize CRM module for performance:
1. Add database indexes (verify all foreign keys have indexes)
2. Implement eager loading everywhere to prevent N+1 queries
3. Add query result caching for dashboard metrics (cache for 5 minutes)
4. Paginate all lists (20 items per page)
5. Use lazy loading for large images
6. Add loading skeletons for better perceived performance
7. Optimize Vite build (code splitting)
8. Add database query logging in development (Laravel Telescope)

Run performance tests and optimize bottlenecks.
```

### ✅ Prompt 7.13: Error Handling & Validation
```
Improve error handling:
1. Add try-catch blocks in controllers for database operations
2. Return user-friendly error messages
3. Add validation errors display in all forms
4. Create custom error pages (404, 500) for CRM section
5. Log errors to file and monitoring service
6. Add flash messages for success/error operations
7. Handle network errors in frontend (Inertia error handling)

Better UX when things go wrong!
```

### ✅ Prompt 7.14: Create User Guide Documentation
```
Create user documentation:
1. Create resources/views/crm-help.blade.php or markdown file
2. Document each module:
   - Contacts: How to add, edit, search
   - Leads: How to capture and convert
   - Deals: How to use pipeline, drag-drop
   - Activities: How to log interactions
   - Dashboard: How to read metrics
3. Include screenshots/gifs
4. Add "Help" link in CRM navigation
5. Create video tutorials (optional)

Users need guidance!
```

### ✅ Prompt 7.15: Final Testing & Bug Fixes
```
Comprehensive testing checklist:
1. Test all CRUD operations for each entity
2. Test all relationships (contacts-clients, leads-deals, etc.)
3. Test lead conversion workflow end-to-end
4. Test drag-drop in pipeline (different browsers)
5. Test activity logging on all entities
6. Test search and filters
7. Test permissions (different user roles)
8. Test exports
9. Test notifications
10. Test responsive design (mobile, tablet)
11. Test with large datasets (import 1000+ records)
12. Test error scenarios (network failure, invalid data)
13. Browser compatibility (Chrome, Firefox, Safari, Edge)
14. Load testing (multiple concurrent users)

Fix all bugs found. Get QA approval before production!
```

---

## PHASE 8: Production Deployment (Bonus)

### ✅ Prompt 8.1: Prepare for Production
```
Production deployment checklist:
1. Review all .env variables
2. Set APP_ENV=production and APP_DEBUG=false
3. Configure production database credentials
4. Setup Redis for caching and queues
5. Configure mail settings (SMTP or service like SendGrid)
6. Setup proper logging (Papertrail, Sentry)
7. Configure backup strategy
8. Enable HTTPS (SSL certificate)
9. Set proper file permissions
10. Configure queue workers (supervisor)

Run: php artisan config:cache, route:cache, view:cache
```

### ✅ Prompt 8.2: Database Migration Strategy
```
Plan production database migration:
1. Backup existing production database
2. Test migrations on staging environment first
3. Run migrations during low-traffic window
4. Have rollback plan ready
5. Monitor for errors after migration
6. Verify data integrity

Create migration rollback scripts if needed.
```

### ✅ Prompt 8.3: Setup Monitoring
```
Implement production monitoring:
1. Setup Laravel Telescope in production (with authentication)
2. Configure error tracking (Sentry, Bugsnag, or Flare)
3. Setup uptime monitoring (Pingdom, UptimeRobot)
4. Configure application performance monitoring (New Relic, DataDog)
5. Setup database query monitoring
6. Create alerts for critical errors
7. Monitor queue failures
8. Track key metrics (response times, error rates)

Be proactive about issues!
```

### ✅ Prompt 8.4: User Training
```
Prepare for user rollout:
1. Schedule training sessions for end users
2. Create training materials (guides, videos)
3. Do pilot testing with small user group
4. Gather feedback and make improvements
5. Create feedback channel (in-app or dedicated email)
6. Plan phased rollout (department by department)
7. Have support team ready for questions

User adoption is key to success!
```

### ✅ Prompt 8.5: Post-Launch Support Plan
```
Setup post-launch support:
1. Monitor application closely for first week
2. Have dedicated support person available
3. Track user feedback and issues
4. Create issue tracking system (Jira, GitHub Issues)
5. Prioritize bug fixes vs feature requests
6. Plan regular updates and maintenance windows
7. Schedule performance reviews
8. Gather user satisfaction metrics

Continuous improvement!
```

---

## 📊 Progress Tracking

Track your progress by checking off completed prompts:

**Phase 0 - Setup:** [ ] 0/5 prompts completed  
**Phase 1 - Contacts:** [ ] 0/11 prompts completed  
**Phase 2 - Clients:** [ ] 0/6 prompts completed  
**Phase 3 - Leads:** [ ] 0/8 prompts completed  
**Phase 4 - Deals:** [ ] 0/11 prompts completed  
**Phase 5 - Activities:** [ ] 0/10 prompts completed  
**Phase 6 - Dashboard:** [ ] 0/10 prompts completed  
**Phase 7 - Polish:** [ ] 0/15 prompts completed  
**Phase 8 - Deployment:** [ ] 0/5 prompts completed

**Total Progress:** [ ] 0/81 prompts completed

---

## 🎯 Quick Tips

1. **One Prompt at a Time:** Don't rush. Complete each prompt fully before moving on.
2. **Test Frequently:** Test each feature immediately after implementation.
3. **Review Code:** Review generated code before accepting it.
4. **Ask Questions:** If anything is unclear, ask for clarification.
5. **Customize:** Feel free to adjust prompts to match your specific needs.
6. **Document:** Keep notes of any changes or customizations you make.
7. **Backup:** Commit code to Git after completing each phase.

---

## 🔄 Iteration Strategy

If you encounter issues:
1. **Debug First:** Try to understand the error
2. **Ask for Fix:** "Fix the error in [filename]: [error message]"
3. **Simplify:** If something is too complex, break it into smaller prompts
4. **Alternative Approach:** Ask "Is there a simpler way to implement [feature]?"
5. **Code Review:** Ask "Review [filename] for best practices and potential issues"

---

## 📝 Customization Prompts

After completing core implementation, use these to customize:

### Custom Styling
```
Update the CRM module color scheme to match our brand:
- Primary color: [your color]
- Success color: [your color]
- Warning color: [your color]
Update all components to use these colors (e.g. via Vuetify theme or CSS variables).
```

### Additional Fields
```
Add these custom fields to the Contact model:
- [field_name]: [field_type] ([validation rules])
Update migration, model, form, and views accordingly.
```

### Integration
```
Integrate the CRM with [external service]:
- Setup API credentials
- Create service class in app/Services/
- Add sync functionality for [entities]
```

### Reporting
```
Create a custom report showing:
- [Metric 1]
- [Metric 2]
- [Metric 3]
With filters for [date range, user, etc.]
Export to PDF and Excel.
```

---

## 🎉 Completion

When you've completed all prompts:
1. ✅ Run full test suite
2. ✅ Update documentation
3. ✅ Create user guide
4. ✅ Deploy to staging
5. ✅ Get user acceptance testing (UAT)
6. ✅ Fix any final issues
7. ✅ Deploy to production
8. ✅ Celebrate! 🎊

---

**You've got this! Follow the prompts, test thoroughly, and build an amazing CRM system!** 💪

*Last Updated: February 9, 2026*
