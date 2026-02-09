# 🚀 CRM Implementation - Quick Start Card

**This project (Able Admin Archive):** Vue 3 + Inertia + **Vuetify 3** (Able Pro theme). Use `@/layouts/dashboard/DashboardLayout.vue`, Vuetify components, and `sidebarItem.ts` for the CRM menu. Default DB is SQLite.

## 📋 Your 3 Implementation Files

| File | Purpose | When to Use |
|------|---------|-------------|
| **CRM-MODULE-ROADMAP.md** | Technical specs & architecture | Understanding features |
| **CRM-IMPLEMENTATION-PROMPTS.md** | 81 copy-paste prompts | Daily implementation |
| **CRM-CONTEXT-GUIDE.md** | File tagging reference | Before each prompt (if present) |

---

## ⚡ Quick Start (5 Minutes)

### 1️⃣ First Prompt (Right Now!)

**Copy this:**
```
@.env.example @config/database.php

Ensure the database is set up for the CRM. This project defaults to 
SQLite; to use PostgreSQL instead, create a DB (e.g. "crm_db"), set 
DB_CONNECTION=pgsql and credentials in .env, and run php artisan migrate.
```

**Paste it to me** (or your AI assistant) → Get started immediately!

---

## 📖 How to Use Each Prompt

### Standard Format:
```
[Step 1] Check "Files to Tag" section
[Step 2] Tag files: @file1 @file2 @file3
[Step 3] Copy the prompt text
[Step 4] Paste to AI with tagged files
[Step 5] Review implementation
[Step 6] Test the feature
[Step 7] Mark prompt as done ✅
```

### Example:
```
📎 Files to Tag: @app/Models/Contact.php @app/Http/Controllers/DashboardController.php

@app/Models/Contact.php @app/Http/Controllers/DashboardController.php

Create app/Http/Controllers/CRM/ContactController.php with full CRUD...
```

---

## 🎯 Implementation Phases

| Phase | Duration | Key Feature | Prompts |
|-------|----------|-------------|---------|
| **0 - Setup** | 1 day | Database, routes, sidebar | 5 |
| **1 - Contacts** | 3-4 days | Contact CRUD | 11 |
| **2 - Clients** | 2-3 days | Company management | 6 |
| **3 - Leads** | 3-4 days | Lead conversion ⭐ | 8 |
| **4 - Deals** | 4-5 days | Kanban pipeline ⭐ | 11 |
| **5 - Activities** | 3-4 days | Activity timeline ⭐ | 10 |
| **6 - Dashboard** | 3-4 days | Metrics & charts | 10 |
| **7 - Polish** | 3-4 days | Search, export, etc. | 15 |
| **8 - Deploy** | 2-3 days | Production setup | 5 |

**Total:** 81 prompts, 4-6 weeks

---

## 🏆 MVP Fast Track

Need to launch quickly? Focus on these:

### Essential Prompts (Minimum Viable Product):
- ✅ Phase 0: All (5 prompts) - Setup
- ✅ Phase 1: All (11 prompts) - Contacts
- ✅ Phase 3: 3.1-3.8 (8 prompts) - Leads + Conversion
- ✅ Phase 4: 4.1-4.7, 4.11 (8 prompts) - Pipeline
- ✅ Phase 6: 6.1-6.4 (4 prompts) - Basic Dashboard

**MVP Total:** ~36 prompts = **3-4 weeks**

Skip Phase 2 (Clients), Phase 5 (Activities), Phase 7 (Polish) initially.

---

## 💡 Context Management Cheat Sheet

### Common File Combinations:

**Creating Models:**
```
@app/Models/User.php @CRM-MODULE-ROADMAP.md
```

**Creating Controllers:**
```
@app/Models/[Entity].php @app/Http/Controllers/Controller.php
```

**Creating Vue Pages:**
```
@resources/js/Pages/Dashboard/Default.vue @resources/js/layouts/dashboard/DashboardLayout.vue
```

**Creating Forms:**
```
@resources/js/components/CRM/ContactForm.vue @able-pro-analysis.md
```

### File Tagging Rules:
- ✅ 3-5 files = Perfect
- ✅ 1-2 files = Good for simple prompts
- ⚠️ 6-8 files = Maximum (complex prompts only)
- ❌ 10+ files = Too much context

---

## 🔥 Today's Goal

**Complete Phase 0 (5 prompts in ~2 hours):**

1. ✅ Database setup
2. ✅ Install packages
3. ✅ Create folders
4. ✅ Setup routes
5. ✅ Update sidebar

**Result:** CRM infrastructure ready for development!

---

## 📞 Key Features to Build

### The Big 3 (Must-Have):
1. **Lead Conversion** 🎯 - Turn prospects into customers (Phase 3)
2. **Kanban Pipeline** 💼 - Visual deal management (Phase 4)
3. **Activity Timeline** 📅 - Track all interactions (Phase 5)

### Nice to Have:
- Client management
- Dashboard with charts
- Advanced search
- Export functionality
- Notifications

---

## 🎨 Design System (Vuetify + Able Pro theme)

### Quick Reference:

This project uses **Vuetify 3**. Use Vuetify components so CRM matches the dashboard:

- **Cards:** `v-card`, `v-card-title`, `v-card-text`
- **Buttons:** `v-btn` (color="primary", variant="outlined")
- **Tables:** `v-table`
- **Forms:** `v-text-field`, `v-select`, `v-textarea`, `v-form`
- **Feedback:** `v-chip`, `v-alert`
- **Layout:** `v-row`, `v-col`

**Icons:** vue-tabler-icons or the project’s custom icons (e.g. in `sidebarItem.ts`).  
**Layout:** `DashboardLayout` from `@/layouts/dashboard/DashboardLayout.vue`.

---

## ✅ Testing Checklist

After each phase:
- [ ] Feature works in UI
- [ ] Database saves correctly
- [ ] Search/filters work
- [ ] Relationships load properly
- [ ] No console errors
- [ ] Mobile responsive
- [ ] Commit to Git

---

## 🚨 Troubleshooting

**Issue:** Migration fails
- Check database connection
- Verify PostgreSQL is running
- Check migration order

**Issue:** Vue component not loading
- Run `npm run dev`
- Check browser console
- Verify import paths

**Issue:** Routes not working
- Run `php artisan route:list`
- Check bootstrap/app.php
- Clear route cache: `php artisan route:clear`

**Issue:** Styling looks wrong
- Use Vuetify components (v-card, v-btn, etc.)
- Verify Vite is running
- Compare with views/dashboards/default or existing Pages

---

## 📈 Progress Tracking

Update daily:

```
Week 1: Phase 0-1 (Setup + Contacts)
Week 2: Phase 2-3 (Clients + Leads)  
Week 3: Phase 4 (Deals + Pipeline)
Week 4: Phase 5 (Activities)
Week 5: Phase 6 (Dashboard)
Week 6: Phase 7 (Polish)
```

---

## 🎯 Success Metrics

You'll know you're succeeding when:
- ✅ Each prompt takes 15-30 minutes
- ✅ Tests pass immediately
- ✅ UI looks professional (Able Pro styling)
- ✅ No major bugs
- ✅ You're enjoying the process! 😊

---

## 🌟 Pro Tips

1. **Don't skip file tagging** - It makes AI 10x better
2. **Test immediately** - Don't accumulate bugs
3. **Commit often** - After each phase minimum
4. **Read the roadmap** - Understand before building
5. **Take breaks** - Quality > speed
6. **Ask questions** - If stuck, ask AI for help
7. **Customize freely** - These are guidelines, not rules

---

## 🎉 You've Got This!

You have:
- ✅ Complete roadmap (2,489 lines)
- ✅ 81 ready prompts (1,305 lines)
- ✅ Context management guide
- ✅ This quick reference

**Everything you need to build a professional CRM!**

---

## 📞 Need Help?

**Stuck on a prompt?** Ask:
```
"I'm stuck on Prompt [X.Y]. Here's the error: [error]. 
Can you help me fix it?"
```

**Want to customize?** Ask:
```
"I want to modify [feature] to do [X]. 
How should I adjust Prompt [X.Y]?"
```

**Need clarification?** Ask:
```
"Can you explain what Prompt [X.Y] is doing 
and why we need it?"
```

---

**Ready? Start with Prompt 0.1! 🚀**

*Print this card or keep it open while implementing!*
