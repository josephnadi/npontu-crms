# 🎯 CRM Module Implementation Roadmap
**Project:** CRM System with Able Pro Template (Able Admin Archive)  
**Stack:** Laravel 11+ | Vue 3 | Inertia.js | **Vuetify 3** | Able Pro theme | SQLite/PostgreSQL/MySQL  
**Timeline:** 6-8 Weeks (Flexible based on priorities)  
**Last Updated:** February 9, 2026

**This project:** The Able Pro theme is implemented with **Vuetify 3** (v-app, v-card, v-table, etc.). Use `@/layouts/dashboard/DashboardLayout.vue` and the same patterns as `views/dashboards/default/`. Sidebar menu: `resources/js/layouts/dashboard/vertical-sidebar/sidebarItem.ts`.

---

## 📋 Table of Contents
1. [Executive Summary](#executive-summary)
2. [System Architecture](#system-architecture)
3. [Core Features & Modules](#core-features--modules)
4. [Database Design](#database-design)
5. [Phase-by-Phase Implementation](#phase-by-phase-implementation)
6. [Able Pro Integration Guide](#able-pro-integration-guide)
7. [Component Library](#component-library)
8. [API Endpoints](#api-endpoints)
9. [Testing & Quality Assurance](#testing--quality-assurance)
10. [Deployment & Maintenance](#deployment--maintenance)

---

## 📊 Executive Summary

### What We're Building
A comprehensive Customer Relationship Management (CRM) system that helps businesses:
- Manage customer contacts and relationships
- Track sales leads from initial contact to conversion
- Monitor deals through a visual sales pipeline
- Log all customer interactions (calls, meetings, emails)
- Generate reports and insights from sales data

### Key Design Principles
✅ **Consistent with Able Pro** - All UI components match the existing dashboard design  
✅ **User-Friendly** - Simple, intuitive interfaces for daily use  
✅ **Scalable** - Built to handle growing data and users  
✅ **Mobile-Responsive** - Works on all device sizes  
✅ **Fast & Efficient** - Optimized queries and caching

### Success Metrics
- Create/manage contacts in under 30 seconds
- Convert leads to deals in 3 clicks
- View pipeline status at a glance
- Generate reports in real-time
- 100% match with Able Pro design system

---

## 🏗️ System Architecture

### Technology Stack

#### **Backend**
```
Laravel 11+               → Core framework
PostgreSQL 15+            → Database
Inertia.js Server Adapter → Backend-frontend bridge
Laravel Eloquent ORM      → Database interactions
Laravel Validation        → Form validation
Laravel Queues            → Background jobs
Laravel Notifications     → Email/alerts
```

#### **Frontend**
```
Vue 3 (Composition API)   → Frontend framework
Inertia.js Client         → SPA without API
Vite                      → Build tool
Vuetify 3                 → UI framework (Able Pro theme)
ApexCharts / vue3-apexcharts → Data visualization
VueDraggable              → Kanban boards
Day.js                    → Date handling
vue-tabler-icons          → Icons
```

#### **Database**
```
SQLite / MySQL / PostgreSQL → Use .env DB_CONNECTION
- json() column type      → Flexible custom fields (Laravel $table->json())
- Indexes                 → Query optimization
- Foreign keys            → Data integrity
```

### Folder Structure
```
my-project/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── CRM/
│   │   │       ├── ContactController.php
│   │   │       ├── ClientController.php
│   │   │       ├── LeadController.php
│   │   │       ├── DealController.php
│   │   │       ├── ActivityController.php
│   │   ├── Requests/CRM/
│   │   │   ├── StoreContactRequest.php
│   │   │   ├── UpdateContactRequest.php
│   │   │   └── ...
│   │   └── Resources/CRM/
│   │       ├── ContactResource.php
│   │       ├── ClientResource.php
│   │       └── ...
│   ├── Models/
│   │   ├── Contact.php
│   │   ├── Client.php
│   │   ├── Lead.php
│   │   ├── Deal.php
│   │   ├── Activity.php
│   │   ├── LeadSource.php
│   │   ├── DealStage.php
│   │   └── Tag.php
│   └── Policies/
│       └── CRM/
│           ├── ContactPolicy.php
│           └── ...
│
├── database/
│   ├── migrations/
│   │   ├── create_contacts_table.php
│   │   ├── create_clients_table.php
│   │   ├── create_leads_table.php
│   │   ├── create_deals_table.php
│   │   ├── create_activities_table.php
│   │   ├── create_lead_sources_table.php
│   │   ├── create_deal_stages_table.php
│   │   └── create_tags_table.php
│   └── seeders/
│       └── CRM/
│           ├── LeadSourceSeeder.php
│           ├── DealStageSeeder.php
│           └── DemoDataSeeder.php
│
├── resources/
│   └── js/
│       ├── layouts/
│       │   └── dashboard/
│       │       ├── DashboardLayout.vue   ← Use this layout for CRM pages
│       │       └── vertical-sidebar/
│       │           └── sidebarItem.ts    ← Add CRM menu here
│       ├── Pages/
│       │   └── CRM/
│       │       ├── Contacts/
│       │       │   ├── Index.vue              # Contact list
│       │       │   ├── Create.vue             # Add contact
│       │       │   ├── Edit.vue               # Edit contact
│       │       │   └── Show.vue               # Contact detail
│       │       ├── Clients/
│       │       │   ├── Index.vue
│       │       │   ├── Create.vue
│       │       │   ├── Edit.vue
│       │       │   └── Show.vue
│       │       ├── Leads/
│       │       │   ├── Index.vue
│       │       │   ├── Create.vue
│       │       │   ├── Edit.vue
│       │       │   └── Show.vue
│       │       ├── Deals/
│       │       │   ├── Pipeline.vue           # Kanban board ⭐
│       │       │   ├── Index.vue              # List view
│       │       │   ├── Create.vue
│       │       │   ├── Edit.vue
│       │       │   └── Show.vue
│       │       ├── Activities/
│       │       │   ├── Index.vue
│       │       │   ├── Calendar.vue
│       │       │   └── Create.vue
│       │       └── Engagement/
│       │           ├── Index.vue
│       │           ├── Create.vue
│       │           └── Show.vue
│       │
│       └── components/
│           └── CRM/
│               ├── ContactCard.vue
│               ├── ContactSelector.vue
│               ├── ClientCard.vue
│               ├── LeadCard.vue
│               ├── LeadConversionModal.vue    # ⭐ Convert lead
│               ├── DealCard.vue
│               ├── DealPipeline.vue           # ⭐ Drag & drop
│               ├── ActivityTimeline.vue       # ⭐ Activity feed
│               ├── ActivityForm.vue
│               ├── EngagementTimeline.vue     # Engagement events on entity pages
│               ├── MetricCard.vue
│               ├── StageSelector.vue
│               ├── StatusBadge.vue
│               └── EmptyState.vue
│
└── routes/
    ├── web.php
    └── crm.php                                # CRM routes
```

---

## 🎯 Core Features & Modules

### 1. Contact Management 👤
**Purpose:** Central repository for people/individuals

**Features:**
- ✅ Add/Edit/Delete contacts
- ✅ Store: Name, Email, Phone, Job Title, Company
- ✅ Link contacts to clients (companies)
- ✅ Search and filter contacts
- ✅ View contact activity history
- ✅ Assign owner (team member responsible)
- ✅ Custom fields support (JSONB)
- ✅ Tags for organization

**UI Components:**
- Contact list (table with search, filters, pagination)
- Contact detail page (profile, activities, deals)
- Contact form (create/edit modal or page)
- Contact card (quick view)

---

### 2. Client/Account Management 🏢
**Purpose:** Track companies/organizations (B2B)

**Features:**
- ✅ Add/Edit/Delete clients
- ✅ Store: Company name, Industry, Website, Address
- ✅ Link multiple contacts to one client
- ✅ View all deals associated with client
- ✅ Track client metrics (total deal value, # contacts)
- ✅ Client activity timeline

**UI Components:**
- Client list (cards or table)
- Client detail page (info, contacts, deals, metrics)
- Client form
- Client selector dropdown (for linking contacts)

---

### 3. Lead Management 🎯
**Purpose:** Track potential customers (not yet converted)

**Features:**
- ✅ Capture leads with source tracking (Website, Referral, Event, etc.)
- ✅ Lead statuses:
  - **New** - Just captured
  - **Contacted** - Reached out
  - **Qualified** - Interest confirmed
  - **Converted** - Became customer
  - **Lost** - Not interested
- ✅ Lead scoring (0-100 based on interest level)
- ✅ **Convert lead to Contact + Deal** (with one click)
- ✅ Lead activity tracking
- ✅ Bulk actions (assign, status change, delete)

**UI Components:**
- Lead list (table or kanban by status)
- Lead form
- Lead detail page
- **Lead conversion modal** ⭐ (key feature)
  - Create contact from lead
  - Optionally create deal
  - Set deal value and stage

**Lead Conversion Flow:**
```
Lead (Qualified) → Click "Convert" → Modal Opens
  ↓
Choose options:
  [x] Create Contact (auto-fill from lead data)
  [x] Create Deal
      - Deal Title: _______
      - Value: $_______
      - Stage: [Dropdown]
  ↓
Click "Convert" → Lead marked as "Converted"
                → Contact created
                → Deal created (if selected)
```

---

### 4. Deal/Pipeline Management 💼
**Purpose:** Track sales opportunities through stages

**Features:**
- ✅ Create deals with:
  - Title, Description, Value, Currency
  - Expected close date
  - Linked contact & client
  - Current stage
  - Probability (%)
- ✅ **Visual Kanban Pipeline** ⭐ (drag & drop between stages)
- ✅ Deal stages (configurable):
  1. Qualification (20% probability)
  2. Proposal (40%)
  3. Negotiation (60%)
  4. Closing (80%)
  5. Won (100%)
  6. Lost (0%)
- ✅ Mark deals as Won/Lost with reason
- ✅ Track actual close date
- ✅ Deal metrics (total value, win rate, average deal size)

**UI Components:**
- **Deal Pipeline (Kanban)** ⭐ - Main view
  - Columns for each stage
  - Drag cards between stages
  - Color-coded by probability/value
  - Quick stats per stage
- Deal list (table view alternative)
- Deal detail page (full info, contact, activities)
- Deal form

**Pipeline Visualization:**
```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│ Qualification│  Proposal   │ Negotiation │   Closing   │
├─────────────┼─────────────┼─────────────┼─────────────┤
│ Deal Card 1 │ Deal Card 4 │ Deal Card 7 │ Deal Card 9 │
│   $5,000    │   $15,000   │   $25,000   │   $50,000   │
│ Contact: JD │ Contact: AB │ Contact: XY │ Contact: MN │
├─────────────┼─────────────┼─────────────┼─────────────┤
│ Deal Card 2 │ Deal Card 5 │ Deal Card 8 │             │
│   $3,000    │   $8,000    │   $12,000   │             │
├─────────────┼─────────────┼─────────────┼─────────────┤
│ Deal Card 3 │ Deal Card 6 │             │             │
│   $7,500    │   $20,000   │             │             │
└─────────────┴─────────────┴─────────────┴─────────────┘
  $15.5k (3)     $43k (3)      $37k (2)      $50k (1)
```

---

### 5. Activity Management 📅
**Purpose:** Log all customer interactions

**Features:**
- ✅ Activity types:
  - **Call** - Phone conversations
  - **Meeting** - In-person or video meetings
  - **Email** - Email correspondence
  - **Task** - To-do items
  - **Note** - General notes
- ✅ Link to Contact, Lead, Deal, or Client (polymorphic)
- ✅ Set due dates and reminders
- ✅ Mark activities as complete
- ✅ Activity timeline view (chronological)
- ✅ Calendar view for meetings/calls
- ✅ Filter by type, status, date range

**UI Components:**
- **Activity Timeline** ⭐ (shows on contact/deal pages)
  - Chronological list with icons
  - Quick add activity form
  - Color-coded by type
- Activity list (table view)
- Activity form (modal or page)
- Calendar view (monthly/weekly grid)

**Timeline Example:**
```
📞 Call - Discussed pricing options
   John Doe • Feb 8, 2026 3:30 PM

🤝 Meeting - Product demo scheduled
   Jane Smith • Feb 7, 2026 10:00 AM

📧 Email - Sent proposal document
   John Doe • Feb 6, 2026 2:15 PM

📝 Note - Interested in enterprise plan
   Sales Team • Feb 5, 2026 9:00 AM
```

---

### 6. Engagement Management 📈
**Purpose:** Track engagement touchpoints and events (email opens, link clicks, meetings attended, webinars, etc.) to measure how contacts/leads interact with your business.

**Features:**
- ✅ Engagement types (configurable):
  - **Email opened** - Email engagement
  - **Link clicked** - Click tracking
  - **Meeting attended** - Event attendance
  - **Webinar** - Webinar registration/attendance
  - **Form submitted** - Form or survey
  - **Content viewed** - Page/content view
  - **Other** - Custom engagement
- ✅ Link to Contact, Lead, Deal, or Client (polymorphic)
- ✅ Engagement date and optional score (0–100)
- ✅ Subject/description and optional metadata (JSON)
- ✅ List and filter by type, entity, date range
- ✅ Engagement timeline on Contact/Lead/Deal/Client detail pages
- ✅ Optional engagement score rollup per contact/lead

**UI Components:**
- Engagement list (table with type, subject, related entity, date, score)
- Engagement form (log new engagement)
- Engagement timeline snippet on entity Show pages
- Filters: type, date range, related entity

**Engagement Flow:**
```
Contact/Lead/Deal/Client → "Log engagement" → Select type, subject, date, score
  ↓
Saved and shown in Engagement list + on entity’s timeline
```

---

### 7. CRM Dashboard & Reports 📊
**Purpose:** Overview of CRM metrics and performance

**Features:**
- ✅ Key metrics (stat cards):
  - Total Contacts
  - Active Leads
  - Open Deals
  - Total Deal Value
  - Win Rate
  - Average Deal Size
- ✅ Charts:
  - **Pipeline Funnel** (deals by stage)
  - **Revenue Forecast** (expected close dates)
  - **Lead Source Performance** (conversion rates)
  - **Activity Breakdown** (by type)
  - **Monthly Trends** (deals won over time)
- ✅ Recent activities feed
- ✅ Top performers (users with most deals/revenue)
- ✅ Quick actions (add contact, add lead, add deal)

**Dashboard Layout (Using Able Pro components):**
```
┌─────────────────────────────────────────────────────────┐
│  CRM Dashboard                         [+ Add Contact]   │
├──────────────┬──────────────┬──────────────┬────────────┤
│ 📇 Contacts  │ 🎯 Leads     │ 💼 Deals     │ 💰 Value   │
│    1,234     │    456       │    89        │   $2.5M    │
│  +12% ↑     │  +8% ↑      │  +15% ↑     │  +22% ↑   │
└──────────────┴──────────────┴──────────────┴────────────┘

┌─────────────────────────────────┬──────────────────────┐
│  Pipeline Funnel (ApexCharts)   │  Lead Sources        │
│  [Bar Chart showing stages]     │  [Pie Chart]         │
│                                 │                      │
│  Qualification: 25 deals        │  Website: 45%        │
│  Proposal: 18 deals             │  Referral: 30%       │
│  Negotiation: 12 deals          │  Event: 15%          │
│  Closing: 8 deals               │  Other: 10%          │
└─────────────────────────────────┴──────────────────────┘

┌─────────────────────────────────────────────────────────┐
│  Recent Activities                                       │
│  📞 Call with John Doe - 2 hours ago                    │
│  🤝 Meeting with ABC Corp - 5 hours ago                 │
│  📧 Email to Jane Smith - Yesterday                     │
└─────────────────────────────────────────────────────────┘
```

---

## 🗄️ Database Design

### PostgreSQL Schema

#### Core Tables Structure
```
users (existing)
  ↓
contacts → clients
  ↓
deals → deal_stages
  ↓
activities (polymorphic)
engagements (polymorphic)

leads → lead_sources
  ↓ (convert)
contacts + deals
```

### Table Definitions

#### 1. **contacts**
```sql
CREATE TABLE contacts (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE,
    phone VARCHAR(50),
    mobile VARCHAR(50),
    job_title VARCHAR(100),
    company_name VARCHAR(255),
    client_id BIGINT REFERENCES clients(id) ON DELETE SET NULL,
    status VARCHAR(20) DEFAULT 'active', -- active/inactive
    avatar_url VARCHAR(500),
    
    -- Address fields
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    
    -- Additional
    notes TEXT,
    tags JSONB,                          -- ["vip", "enterprise"]
    custom_fields JSONB,                 -- {"industry": "tech"}
    
    -- Ownership & tracking
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    updated_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

-- Indexes for performance
CREATE INDEX idx_contacts_email ON contacts(email);
CREATE INDEX idx_contacts_client_id ON contacts(client_id);
CREATE INDEX idx_contacts_owner_id ON contacts(owner_id);
CREATE INDEX idx_contacts_status ON contacts(status);
CREATE INDEX idx_contacts_deleted_at ON contacts(deleted_at);
CREATE INDEX idx_contacts_full_name ON contacts(first_name, last_name);
```

#### 2. **clients**
```sql
CREATE TABLE clients (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    industry VARCHAR(100),
    website VARCHAR(500),
    email VARCHAR(255),
    phone VARCHAR(50),
    
    -- Address
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    
    -- Business info
    annual_revenue DECIMAL(15, 2),
    employee_count INTEGER,
    status VARCHAR(20) DEFAULT 'prospect', -- prospect/active/inactive
    logo_url VARCHAR(500),
    description TEXT,
    
    tags JSONB,
    custom_fields JSONB,
    
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    updated_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_clients_name ON clients(name);
CREATE INDEX idx_clients_owner_id ON clients(owner_id);
CREATE INDEX idx_clients_status ON clients(status);
CREATE INDEX idx_clients_deleted_at ON clients(deleted_at);
```

#### 3. **lead_sources** (Reference table)
```sql
CREATE TABLE lead_sources (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed with default sources
INSERT INTO lead_sources (name) VALUES 
    ('Website Form'), 
    ('Referral'), 
    ('Trade Show'), 
    ('Cold Call'),
    ('Social Media'), 
    ('Email Campaign'), 
    ('Partner'),
    ('Event'),
    ('Other');
```

#### 4. **leads**
```sql
CREATE TABLE leads (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    mobile VARCHAR(50),
    company_name VARCHAR(255),
    job_title VARCHAR(100),
    
    lead_source_id BIGINT REFERENCES lead_sources(id),
    status VARCHAR(20) DEFAULT 'new', -- new/contacted/qualified/converted/lost
    score INTEGER DEFAULT 0 CHECK (score >= 0 AND score <= 100),
    
    -- Address
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    
    notes TEXT,
    tags JSONB,
    custom_fields JSONB,
    
    -- Conversion tracking
    converted_to_contact_id BIGINT REFERENCES contacts(id),
    converted_to_deal_id BIGINT REFERENCES deals(id),
    converted_at TIMESTAMP NULL,
    
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    updated_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_leads_email ON leads(email);
CREATE INDEX idx_leads_status ON leads(status);
CREATE INDEX idx_leads_owner_id ON leads(owner_id);
CREATE INDEX idx_leads_source_id ON leads(lead_source_id);
CREATE INDEX idx_leads_deleted_at ON leads(deleted_at);
```

#### 5. **deal_stages** (Reference table)
```sql
CREATE TABLE deal_stages (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    order_column INTEGER NOT NULL,
    probability INTEGER DEFAULT 0 CHECK (probability >= 0 AND probability <= 100),
    color VARCHAR(20) DEFAULT 'primary', -- Bootstrap color classes
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Seed with default stages
INSERT INTO deal_stages (name, order_column, probability, color) VALUES 
    ('Qualification', 1, 20, 'info'),
    ('Proposal', 2, 40, 'primary'),
    ('Negotiation', 3, 60, 'warning'),
    ('Closing', 4, 80, 'success'),
    ('Won', 5, 100, 'success'),
    ('Lost', 6, 0, 'danger');
```

#### 6. **deals**
```sql
CREATE TABLE deals (
    id BIGSERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    value DECIMAL(15, 2) NOT NULL DEFAULT 0,
    currency VARCHAR(3) DEFAULT 'USD',
    
    deal_stage_id BIGINT REFERENCES deal_stages(id),
    contact_id BIGINT REFERENCES contacts(id) ON DELETE SET NULL,
    client_id BIGINT REFERENCES clients(id) ON DELETE SET NULL,
    
    expected_close_date DATE,
    actual_close_date DATE NULL,
    probability INTEGER DEFAULT 0 CHECK (probability >= 0 AND probability <= 100),
    
    status VARCHAR(20) DEFAULT 'open', -- open/won/lost
    lost_reason TEXT NULL,
    
    tags JSONB,
    custom_fields JSONB,
    
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    updated_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_deals_stage_id ON deals(deal_stage_id);
CREATE INDEX idx_deals_contact_id ON deals(contact_id);
CREATE INDEX idx_deals_client_id ON deals(client_id);
CREATE INDEX idx_deals_owner_id ON deals(owner_id);
CREATE INDEX idx_deals_status ON deals(status);
CREATE INDEX idx_deals_expected_close_date ON deals(expected_close_date);
CREATE INDEX idx_deals_deleted_at ON deals(deleted_at);
```

#### 7. **activities** (Polymorphic - can link to any entity)
```sql
CREATE TABLE activities (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- call/meeting/email/task/note
    subject VARCHAR(255) NOT NULL,
    description TEXT,
    
    activity_date TIMESTAMP,
    due_date TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    status VARCHAR(20) DEFAULT 'pending', -- pending/completed/cancelled
    duration_minutes INTEGER,
    
    -- Polymorphic relationship (can be Contact, Lead, Deal, or Client)
    activityable_type VARCHAR(100), -- 'App\Models\Contact'
    activityable_id BIGINT,         -- 123
    
    tags JSONB,
    custom_fields JSONB,
    
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    updated_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_activities_type ON activities(type);
CREATE INDEX idx_activities_status ON activities(status);
CREATE INDEX idx_activities_owner_id ON activities(owner_id);
CREATE INDEX idx_activities_polymorphic ON activities(activityable_type, activityable_id);
CREATE INDEX idx_activities_due_date ON activities(due_date);
CREATE INDEX idx_activities_deleted_at ON activities(deleted_at);
```

#### 8. **engagements** (Polymorphic - engagement events per entity)
```sql
CREATE TABLE engagements (
    id BIGSERIAL PRIMARY KEY,
    type VARCHAR(50) NOT NULL, -- email_opened, link_clicked, meeting_attended, webinar, form_submitted, content_viewed, other
    subject VARCHAR(255),
    description TEXT,
    engagement_date TIMESTAMP NOT NULL,
    score INTEGER CHECK (score >= 0 AND score <= 100), -- optional 0-100
    
    -- Polymorphic (Contact, Lead, Deal, or Client)
    engagementable_type VARCHAR(100),
    engagementable_id BIGINT,
    
    metadata JSONB, -- optional extra data
    tags JSONB,
    
    owner_id BIGINT REFERENCES users(id),
    created_by BIGINT REFERENCES users(id),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
);

CREATE INDEX idx_engagements_type ON engagements(type);
CREATE INDEX idx_engagements_engagement_date ON engagements(engagement_date);
CREATE INDEX idx_engagements_polymorphic ON engagements(engagementable_type, engagementable_id);
CREATE INDEX idx_engagements_owner_id ON engagements(owner_id);
CREATE INDEX idx_engagements_deleted_at ON engagements(deleted_at);
```

#### 9. **tags** (Optional - for better tag management)
```sql
CREATE TABLE tags (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    type VARCHAR(50), -- contact/lead/deal/client/activity
    color VARCHAR(20) DEFAULT 'primary',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE taggables (
    tag_id BIGINT REFERENCES tags(id) ON DELETE CASCADE,
    taggable_type VARCHAR(100),
    taggable_id BIGINT,
    PRIMARY KEY (tag_id, taggable_type, taggable_id)
);

CREATE INDEX idx_taggables_polymorphic ON taggables(taggable_type, taggable_id);
```

### Entity Relationship Diagram
```
┌─────────┐
│  users  │
└────┬────┘
     │ owner_id
     ├──────────────────────────┐
     │                          │
┌────▼──────┐            ┌─────▼──────┐
│  clients  │◄───────────┤  contacts  │
└───────────┘ client_id  └──────┬─────┘
                                │
                          ┌─────▼──────┐
                          │   deals    │
                          └──────┬─────┘
                                │
         ┌──────────────────────┼──────────────────────┬──────────────────────┐
         │                      │                      │                      │
    ┌────▼────┐           ┌────▼────┐           ┌────▼────┐           ┌────▼────┐
    │  leads  │           │activities│           │engagements│          │deal_stages│
    └─────────┘           └──────────┘           └──────────┘          └──────────┘
         │
    ┌────▼────────┐
    │lead_sources │
    └─────────────┘
```

---

## 🚀 Phase-by-Phase Implementation

### Timeline Overview
| Phase | Focus | Duration | Priority |
|-------|-------|----------|----------|
| **Phase 0** | Setup & Infrastructure | 2 days | P0 |
| **Phase 1** | Contact Management | 3-4 days | P0 |
| **Phase 2** | Client Management | 2-3 days | P1 |
| **Phase 3** | Lead Management | 3-4 days | P0 |
| **Phase 4** | Deal & Pipeline | 4-5 days | P0 |
| **Phase 5** | Activities | 3-4 days | P1 |
| **Phase 6** | Engagement | 2-3 days | P1 |
| **Phase 7** | Dashboard & Reports | 3-4 days | P0 |
| **Phase 8** | Polish & Testing | 3-4 days | P1 |
| **Total** | | **25-33 days** | **5-7 weeks** |

---

### **PHASE 0: Project Setup** (2 Days)

#### Day 1: Database & Infrastructure

**Tasks:**
1. **Create PostgreSQL database**
```bash
# Create database
createdb my_crm_db

# Or using psql
psql -U postgres
CREATE DATABASE my_crm_db;
\q
```

2. **Update Laravel configuration**
```bash
# Edit .env file
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=my_crm_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Test connection
php artisan migrate
```

3. **Install additional packages**
```bash
# Backend packages
composer require intervention/image  # For avatar uploads (optional)

# Frontend packages
npm install vuedraggable@next        # For kanban boards
npm install apexcharts vue3-apexcharts  # For charts
npm install dayjs                    # Date manipulation
```

#### Day 2: Folder Structure & Routing

**Create directory structure:**
```bash
# Backend directories
mkdir -p app/Http/Controllers/CRM
mkdir -p app/Http/Requests/CRM
mkdir -p app/Http/Resources/CRM
mkdir -p app/Policies/CRM
mkdir -p database/seeders/CRM

# Frontend directories
mkdir -p resources/js/Pages/CRM/{Dashboard,Contacts,Clients,Leads,Deals,Activities,Engagement}
mkdir -p resources/js/Components/CRM
```

**Create CRM routes file:**
```bash
touch routes/crm.php
```

**Update `bootstrap/app.php` to include CRM routes:** Add a `then` callback to the existing `withRouting()` so CRM routes are loaded with auth:
```php
// bootstrap/app.php - inside withRouting(..., then: function () { ... })
then: function () {
    Route::middleware(['web', 'auth'])
        ->prefix('crm')
        ->group(base_path('routes/crm.php'));
}
```
Keep the existing `web`, `commands`, and `health` keys.

**Create base CRM routes structure (`routes/crm.php`):**
```php
<?php

use App\Http\Controllers\CRM\ContactController;
use App\Http\Controllers\CRM\ClientController;
use App\Http\Controllers\CRM\LeadController;
use App\Http\Controllers\CRM\DealController;
use App\Http\Controllers\CRM\ActivityController;
use App\Http\Controllers\CRM\EngagementController;
use App\Http\Controllers\CRM\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // CRM Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('crm.dashboard');
    
    // Contacts
    Route::resource('contacts', ContactController::class)->names('crm.contacts');
    
    // Clients
    Route::resource('clients', ClientController::class)->names('crm.clients');
    
    // Leads
    Route::resource('leads', LeadController::class)->names('crm.leads');
    Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('crm.leads.convert');
    
    // Deals
    Route::resource('deals', DealController::class)->names('crm.deals');
    Route::get('deals-pipeline', [DealController::class, 'pipeline'])->name('crm.deals.pipeline');
    Route::put('deals/{deal}/stage', [DealController::class, 'updateStage'])->name('crm.deals.updateStage');
    
    // Activities
    Route::resource('activities', ActivityController::class)->names('crm.activities');
    Route::get('activities-calendar', [ActivityController::class, 'calendar'])->name('crm.activities.calendar');
    Route::put('activities/{activity}/complete', [ActivityController::class, 'complete'])->name('crm.activities.complete');
    
    // Engagement
    Route::resource('engagement', EngagementController::class)->names('crm.engagement');
});
```

**Update sidebar navigation (`resources/js/layouts/dashboard/vertical-sidebar/sidebarItem.ts`):**  
Add a CRM section to the `sidebarItem` array using the existing `menu` interface (header, title, icon, to, children):
```ts
{ header: 'CRM' },
{ title: 'Dashboard', icon: 'custom-chart', to: '/crm/dashboard' },
{ title: 'Contacts', icon: 'custom-user', to: '/crm/contacts' },
{ title: 'Clients', icon: 'custom-building', to: '/crm/clients' },
{ title: 'Leads', icon: 'custom-speaker', to: '/crm/leads' },
{ title: 'Deals', icon: 'custom-briefcase', to: '/crm/deals-pipeline' },
{ title: 'Activities', icon: 'custom-calendar', to: '/crm/activities' },
{ title: 'Engagement', icon: 'custom-chart-line', to: '/crm/engagement' },
```
Use the same icon naming as the rest of the sidebar (e.g. `custom-*` or the project’s vue-tabler-icons).

---

### **PHASE 1: Contact Management** (3-4 Days)

#### Day 1: Backend - Database & Models

**1. Create migration:**
```bash
php artisan make:migration create_contacts_table
```

**Edit migration file:**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->unique()->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->string('company_name')->nullable();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('active');
            $table->string('avatar_url', 500)->nullable();
            
            // Address
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            
            // Additional
            $table->text('notes')->nullable();
            $table->jsonb('tags')->nullable();
            $table->jsonb('custom_fields')->nullable();
            
            // Ownership
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('email');
            $table->index('client_id');
            $table->index('owner_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
```

**2. Create Contact model:**
```bash
php artisan make:model Contact
```

**Edit `app/Models/Contact.php`:**
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'mobile',
        'job_title', 'company_name', 'client_id', 'status',
        'avatar_url', 'address', 'city', 'state', 'country',
        'postal_code', 'notes', 'tags', 'custom_fields',
        'owner_id', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
    ];

    protected $appends = ['full_name'];

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Relationships
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'activityable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('first_name', 'ilike', "%{$search}%")
              ->orWhere('last_name', 'ilike', "%{$search}%")
              ->orWhere('email', 'ilike', "%{$search}%")
              ->orWhere('company_name', 'ilike', "%{$search}%");
        });
    }
}
```

**3. Run migration:**
```bash
php artisan migrate
```

#### Day 2: Backend - Controllers & Requests

**1. Create controller, requests, and resources:**
```bash
php artisan make:controller CRM/ContactController --resource
php artisan make:request CRM/StoreContactRequest
php artisan make:request CRM/UpdateContactRequest
php artisan make:resource CRM/ContactResource
```

**2. Edit `app/Http/Controllers/CRM/ContactController.php`:**
```php
<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Http\Requests\CRM\StoreContactRequest;
use App\Http\Requests\CRM\UpdateContactRequest;
use App\Http\Resources\CRM\ContactResource;
use Inertia\Inertia;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $contacts = Contact::with(['client', 'owner'])
            ->when($request->search, fn($q) => $q->search($request->search))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->client_id, fn($q) => $q->where('client_id', $request->client_id))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('CRM/Contacts/Index', [
            'contacts' => ContactResource::collection($contacts),
            'filters' => $request->only(['search', 'status', 'client_id']),
        ]);
    }

    public function create()
    {
        return Inertia::render('CRM/Contacts/Create');
    }

    public function store(StoreContactRequest $request)
    {
        $contact = Contact::create([
            ...$request->validated(),
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('crm.contacts.show', $contact)
            ->with('success', 'Contact created successfully.');
    }

    public function show(Contact $contact)
    {
        $contact->load([
            'client',
            'owner',
            'deals' => fn($q) => $q->with('stage')->latest(),
            'activities' => fn($q) => $q->with('owner')->latest()->take(20)
        ]);

        return Inertia::render('CRM/Contacts/Show', [
            'contact' => new ContactResource($contact),
        ]);
    }

    public function edit(Contact $contact)
    {
        return Inertia::render('CRM/Contacts/Edit', [
            'contact' => new ContactResource($contact),
        ]);
    }

    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $contact->update([
            ...$request->validated(),
            'updated_by' => auth()->id(),
        ]);

        return redirect()
            ->route('crm.contacts.show', $contact)
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()
            ->route('crm.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }
}
```

**3. Edit `app/Http/Requests/CRM/StoreContactRequest.php`:**
```php
<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:contacts,email',
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|in:active,inactive',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ];
    }
}
```

**4. Edit `app/Http/Requests/CRM/UpdateContactRequest.php`:**
```php
<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|unique:contacts,email,' . $this->contact->id,
            'phone' => 'nullable|string|max:50',
            'mobile' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'client_id' => 'nullable|exists:clients,id',
            'status' => 'nullable|in:active,inactive',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ];
    }
}
```

**5. Edit `app/Http/Resources/CRM/ContactResource.php`:**
```php
<?php

namespace App\Http\Resources\CRM;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'mobile' => $this->mobile,
            'job_title' => $this->job_title,
            'company_name' => $this->company_name,
            'status' => $this->status,
            'avatar_url' => $this->avatar_url ?? '/assets/images/user/avatar-1.jpg',
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
            'notes' => $this->notes,
            'client' => $this->whenLoaded('client'),
            'owner' => $this->whenLoaded('owner', fn() => [
                'id' => $this->owner->id,
                'name' => $this->owner->name,
            ]),
            'deals' => $this->whenLoaded('deals'),
            'activities' => $this->whenLoaded('activities'),
            'created_at' => $this->created_at?->format('M d, Y'),
            'updated_at' => $this->updated_at?->format('M d, Y'),
        ];
    }
}
```

#### Days 3-4: Frontend - Vue Components

**1. Create Contact Index page (`resources/js/Pages/CRM/Contacts/Index.vue`):**

Due to length constraints, I'll provide a link to the full implementation in the roadmap. The key structure includes:
- Search and filter controls
- Data table with pagination
- Action buttons (View, Edit, Delete)
- Able Pro card and table styling

**2. Create Contact Form component** (for Create/Edit)
**3. Create Contact Show page** (detail view)
**4. Update sidebar to include CRM menu**

*(Full Vue code available in the detailed implementation section)*

---

### **PHASE 2: Client Management** (2-3 Days)

Similar structure to Contacts:
- Day 1: Backend (Migration, Model, Controller, Requests, Resources)
- Day 2: Frontend (Index, Create/Edit, Show pages)
- Day 3: Client-Contact relationships and metrics

---

### **PHASE 3: Lead Management** (3-4 Days)

- Day 1-2: Backend (Migrations for leads & lead_sources, Models, Controllers)
- Day 3: Frontend (Lead list, forms)
- Day 4: **Lead Conversion Feature** ⭐ (Modal to convert lead → contact + deal)

---

### **PHASE 4: Deal & Pipeline Management** (4-5 Days)

- Day 1-2: Backend (Migrations for deals & deal_stages, Models, Controllers)
- Day 3-4: **Kanban Pipeline UI** ⭐ (Drag & drop with VueDraggable)
- Day 5: Deal detail page and metrics

---

### **PHASE 5: Activity Management** (3-4 Days)

- Day 1-2: Backend (Polymorphic activities migration, Model, Controller)
- Day 3: **Activity Timeline Component** ⭐
- Day 4: Calendar view (optional)

---

### **PHASE 6: Engagement** (2-3 Days)

- Day 1: Backend (engagements migration with polymorphic relation, Model, Controller, Requests, Resource)
- Day 2: Frontend (Engagement Index list, Create form, filters by type and date)
- Day 3: **EngagementTimeline** component on Contact/Lead/Deal/Client Show pages; optional engagement score

---

### **PHASE 7: Dashboard & Reports** (3-4 Days)

- Day 1-2: Backend (Dashboard controller, metrics calculations)
- Day 3-4: Frontend (Metric cards, ApexCharts integration, charts)

---

### **PHASE 8: Polish & Testing** (3-4 Days)

- Day 1: Notifications setup
- Day 2: Search & filters enhancement
- Day 3: Export functionality
- Day 4: Testing & bug fixes

---

## 🎨 Able Pro / Vuetify Integration Guide

### Design System Consistency

**In this project** the Able Pro theme is implemented with **Vuetify 3**. Use Vuetify components so CRM pages match the existing dashboard (`views/dashboards/default/`, `views/widgets/`).

#### 1. **Use Vuetify Components**

**Stat Cards:** Use `v-card` with layout similar to `views/widgets/statistics/components/UserCard.vue` or chart cards.

**Data Tables:** Use `v-table` (see Vuetify docs). Example:
```vue
<v-card>
  <v-table>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
        <th class="text-end">Actions</th>
      </tr>
    </thead>
    <tbody>...</tbody>
  </v-table>
</v-card>
```

**Buttons:** `v-btn` (e.g. `color="primary"`, `variant="outlined"`). **Badges/Status:** `v-chip` or `v-badge` with `color="success"`, `"error"`, etc.

#### 2. **Icons**

This project uses **vue-tabler-icons** and custom icons (e.g. in sidebar `custom-home-trend`). Use the same pattern for CRM (vue-tabler-icons or add custom icons as needed).

#### 3. **Color Scheme**

Use Able Pro's color variables:
```css
Primary:   #4680FF (blue)
Success:   #2ca87f (green)
Warning:   #E58A00 (orange)
Danger:    #DC2626 (red)
Info:      #3b82f6 (light blue)
Secondary: #6c757d (gray)
```

In components:
```vue
<!-- Background variants -->
<div class="bg-light-primary">...</div>
<div class="bg-light-success">...</div>

<!-- Text variants -->
<span class="text-primary">...</span>
<span class="text-success">...</span>

<!-- Badge variants -->
<span class="badge bg-success">Won</span>
<span class="badge bg-danger">Lost</span>
```

#### 4. **Page Layout Structure**

Every CRM page should use the same layout as the dashboard:

```vue
<script setup>
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue'
import { Link } from '@inertiajs/vue3'
// Use BaseBreadcrumb or similar + a title/actions bar if the project has them
</script>

<template>
  <DashboardLayout>
    <v-row>
      <v-col cols="12">
        <!-- Breadcrumbs + title + "Add Contact" button -->
        <v-card>
          <v-card-text>
            <!-- Your content here -->
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
```

#### 5. **Forms**

Use Vuetify form components:

```vue
<v-form @submit.prevent="submit">
  <v-row>
    <v-col cols="12" md="6">
      <v-text-field
        v-model="form.first_name"
        label="First Name"
        :error-messages="form.errors.first_name"
        required
      />
    </v-col>
    <v-col cols="12" md="6">
      <v-select
        v-model="form.status"
        :items="['active', 'inactive']"
        label="Status"
      />
    </v-col>
    <v-col cols="12">
      <v-btn type="submit" color="primary">Save Contact</v-btn>
      <Link href="/crm/contacts"><v-btn variant="outlined" class="ms-2">Cancel</v-btn></Link>
    </v-col>
  </v-row>
</v-form>
```

#### 6. **Modals**

Use Vuetify `v-dialog`:

```vue
<v-dialog v-model="showModal" max-width="600" persistent>
  <v-card>
    <v-card-title>Convert Lead</v-card-title>
    <v-card-text><!-- Form content --></v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn variant="text" @click="showModal = false">Cancel</v-btn>
      <v-btn color="primary" @click="convert">Convert Lead</v-btn>
    </v-card-actions>
  </v-card>
</v-dialog>
```

#### 7. **Charts (ApexCharts)**

This project already uses **vue3-apexcharts**. Reuse the same pattern as in `views/widgets/chart/components/` (e.g. RepeatCustomer, TotalIncome). Wrap charts in `v-card` for consistency.

---

## 🧩 Component Library

### Reusable CRM Components

Create these components in `resources/js/components/CRM/`. Use **Vuetify** (v-card, v-btn, v-list, etc.) so they match the rest of the app.

#### 1. **ContactCard.vue**  
Use `v-card`, `v-avatar`, and typography. Show contact full_name, job_title, company_name, email, phone. Match the style of existing cards (e.g. UserCard, ProjectCard).

#### 2. **DealCard.vue** (for Kanban)  
Use `v-card` with title, contact name, formatted value, expected close date. Add `cursor: move` and hover styles for drag-and-drop. Use Inertia `Link` to deal show page.

#### 3. **ActivityTimeline.vue**  
Props: `activities`, `relatedTo`. Use `v-card`, `v-list` or a vertical timeline with icons per type (call, meeting, email, task, note). Include a quick-add form (v-form, v-select for type, v-text-field, v-textarea) that posts to `/crm/activities` via Inertia useForm. Use Vuetify list/timeline styling; empty state when no activities.

#### 4. **LeadConversionModal.vue** ⭐  
Use **v-dialog** with v-card. Props: `lead`, `dealStages`, `show`. Emit `close`, `converted`. Show lead summary; checkbox "Create Contact" (checked, disabled); optional "Create Deal" with deal title, value, expected close date, stage (v-select). Submit via Inertia `form.post(\`/crm/leads/${lead.id}/convert\`)`. Use Vuetify form components.

#### 5. **DealPipeline.vue** ⭐ (Kanban Board)  
Use **vuedraggable** and **v-row**/ **v-col** with one column per stage. Each column: **v-card** with stage name, deal count, total value, and a draggable list of **DealCard**. On drop, call `router.put(\`/crm/deals/${deal.id}\`, { deal_stage_id })` with Inertia (preserveScroll). Use Vuetify for layout and empty state.

---

## 🔌 API Endpoints Summary

```
GET    /crm/dashboard                    - CRM overview dashboard
GET    /crm/contacts                     - List contacts (with search, filters, pagination)
POST   /crm/contacts                     - Create contact
GET    /crm/contacts/{id}                - View contact detail
PUT    /crm/contacts/{id}                - Update contact
DELETE /crm/contacts/{id}                - Delete contact

GET    /crm/clients                      - List clients
POST   /crm/clients                      - Create client
GET    /crm/clients/{id}                 - View client
PUT    /crm/clients/{id}                 - Update client
DELETE /crm/clients/{id}                 - Delete client

GET    /crm/leads                        - List leads
POST   /crm/leads                        - Create lead
GET    /crm/leads/{id}                   - View lead
PUT    /crm/leads/{id}                   - Update lead
DELETE /crm/leads/{id}                   - Delete lead
POST   /crm/leads/{id}/convert           - Convert lead to contact/deal ⭐

GET    /crm/deals                        - List deals (table view)
GET    /crm/deals-pipeline               - Kanban pipeline view ⭐
POST   /crm/deals                        - Create deal
GET    /crm/deals/{id}                   - View deal
PUT    /crm/deals/{id}                   - Update deal
DELETE /crm/deals/{id}                   - Delete deal
PUT    /crm/deals/{id}/stage             - Move deal to stage (drag & drop)

GET    /crm/activities                   - List activities
POST   /crm/activities                   - Create activity
GET    /crm/activities/{id}              - View activity
PUT    /crm/activities/{id}              - Update activity
DELETE /crm/activities/{id}              - Delete activity
PUT    /crm/activities/{id}/complete     - Mark complete

GET    /crm/engagement                   - List engagements (filters: type, date range, entity)
POST   /crm/engagement                   - Create engagement
GET    /crm/engagement/{id}              - View engagement
PUT    /crm/engagement/{id}              - Update engagement
DELETE /crm/engagement/{id}               - Delete engagement
```

---

## 🧪 Testing & Quality Assurance

### Backend Testing (PHPUnit)

```bash
php artisan make:test CRM/ContactTest
```

```php
<?php

namespace Tests\Feature\CRM;

use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_contacts_list()
    {
        $user = User::factory()->create();
        Contact::factory()->count(5)->create();

        $response = $this->actingAs($user)
            ->get('/crm/contacts');

        $response->assertStatus(200);
    }

    public function test_user_can_create_contact()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/crm/contacts', [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john@example.com',
                'status' => 'active',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('contacts', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_contact_requires_first_and_last_name()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/crm/contacts', [
                'email' => 'test@example.com',
            ]);

        $response->assertSessionHasErrors(['first_name', 'last_name']);
    }
}
```

### Frontend Testing (Vitest)

```javascript
// tests/Components/CRM/ContactCard.test.js
import { mount } from '@vue/test-utils'
import ContactCard from '@/Components/CRM/ContactCard.vue'

describe('ContactCard', () => {
  const contact = {
    full_name: 'John Doe',
    email: 'john@example.com',
    phone: '555-1234',
    job_title: 'CEO',
    company_name: 'Acme Inc',
    avatar_url: '/images/avatar.jpg'
  }

  it('renders contact information correctly', () => {
    const wrapper = mount(ContactCard, {
      props: { contact }
    })

    expect(wrapper.text()).toContain('John Doe')
    expect(wrapper.text()).toContain('john@example.com')
    expect(wrapper.text()).toContain('CEO')
  })

  it('displays avatar image', () => {
    const wrapper = mount(ContactCard, {
      props: { contact }
    })

    const img = wrapper.find('img')
    expect(img.attributes('src')).toBe('/images/avatar.jpg')
  })
})
```

---

## 📦 Deployment & Maintenance

### Pre-Deployment Checklist

**Database:**
- [ ] All migrations tested on staging
- [ ] Seed data created for demo/testing
- [ ] Database indexes verified
- [ ] Backup strategy in place

**Code Quality:**
- [ ] All features tested manually
- [ ] PHPUnit tests passing
- [ ] Frontend tests passing
- [ ] No linter errors
- [ ] Code reviewed

**Performance:**
- [ ] N+1 queries eliminated (use eager loading)
- [ ] Pagination implemented on all lists
- [ ] Database queries optimized
- [ ] Frontend assets minified (Vite build)

**Security:**
- [ ] CSRF protection enabled
- [ ] Input validation on all forms
- [ ] Authorization policies implemented
- [ ] SQL injection protection (Eloquent)
- [ ] XSS protection

**Configuration:**
- [ ] Environment variables configured
- [ ] Queue workers setup (for notifications)
- [ ] Error logging configured
- [ ] Cache configured (Redis recommended)

### Production Deployment

```bash
# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci

# 3. Build frontend assets
npm run build

# 4. Run migrations
php artisan migrate --force

# 5. Clear and cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Restart queues
php artisan queue:restart
```

### Monitoring

Set up monitoring for:
- Database query performance
- API response times
- Error rates
- User activity metrics
- Storage usage

---

## 🎉 Next Steps

### Getting Started

1. **Review this roadmap** with your team
2. **Set up development environment:**
   - PostgreSQL installed and running
   - Laravel project configured
   - Able Pro assets integrated
3. **Start with Phase 0** (Setup)
4. **Build incrementally** - Complete one phase before moving to next
5. **Test continuously** - Don't wait until the end

### Prioritization

If you need to launch quickly, focus on **MVP features first**:

**Priority 0 (Must Have):**
- Contact Management (Phase 1)
- Lead Management (Phase 3)
- Deal Pipeline (Phase 4)
- Basic Dashboard (Phase 7 - simplified)

**Priority 1 (Should Have):**
- Client Management (Phase 2)
- Activity Management (Phase 5)
- Full Dashboard with charts

**Priority 2 (Nice to Have):**
- Advanced filters
- Exports
- Notifications
- Calendar view

### Timeline Adjustment

**Fast Track (3-4 weeks):**
- Skip Client Management initially
- Simplified Dashboard (metrics only, no charts)
- Basic activity logging (no calendar)

**Standard (6 weeks):**
- All core features
- Full dashboard with charts
- Complete activity management

**Complete (8 weeks):**
- All features
- Polish and enhancements
- Comprehensive testing
- Documentation

---

## 📚 Additional Resources

### Documentation
- Laravel 11: https://laravel.com/docs/11.x
- Inertia.js: https://inertiajs.com/
- Vue 3: https://vuejs.org/guide/introduction.html
- PostgreSQL: https://www.postgresql.org/docs/
- ApexCharts: https://apexcharts.com/docs/vue-charts/
- VueDraggable: https://github.com/SortableJS/vue.draggable.next

### Tools
- TablePlus: Database GUI for PostgreSQL
- Laravel Telescope: Debugging and monitoring
- Postman: API testing
- Browser DevTools: Frontend debugging

---

**Ready to build? Start with Phase 0 and let's create an amazing CRM system!** 🚀

---

*Document Version: 1.0*  
*Last Updated: February 9, 2026*  
*Author: CRM Development Team*
