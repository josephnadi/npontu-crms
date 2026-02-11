<script setup lang="ts">
import { ref, computed, watch, PropType } from 'vue';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import LeadConversionModal from '@/components/CRM/LeadConversionModal.vue';

interface Lead {
  id: number;
  full_name: string;
  email: string;
  phone?: string;
  company_name?: string;
  lead_source?: {
    id: number;
    name: string;
  };
  score: number;
  status: 'new' | 'contacted' | 'qualified' | 'converted' | 'lost';
  notes?: string;
}

interface LeadSource {
  id: number;
  name: string;
}

interface LeadFilters {
  search?: string;
  status?: string;
  lead_source_id?: number;
}

interface LeadStats {
  total?: number;
  new?: number;
  qualified?: number;
  converted?: number;
}

interface DealStage {
  id: number;
  name: string;
}

interface PaginatedLeads {
  data: Lead[];
  from: number;
  to: number;
  total: number;
  current_page: number;
  last_page: number;
  // Add other pagination properties if needed
}

// Props from controller
const props = withDefaults(defineProps<{
  leads: PaginatedLeads;
  leadSources: LeadSource[];
  filters: LeadFilters;
  stats: LeadStats;
  dealStages: DealStage[];
}>(), {
  leads: () => ({ data: [], from: 0, to: 0, total: 0, current_page: 1, last_page: 1 }),
  leadSources: () => [],
  filters: () => ({}),
  stats: () => ({}),
  dealStages: () => []
});

// Page title and breadcrumbs
const pageTitle = ref('Leads');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: true, href: '#' }
]);

// Filters
const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const sourceFilter = ref(props.filters.lead_source_id || '');
const loading = ref(false);

// Status options
const statusOptions = [
  { value: '', text: 'All Statuses' },
  { value: 'new', text: 'New' },
  { value: 'contacted', text: 'Contacted' },
  { value: 'qualified', text: 'Qualified' },
  { value: 'converted', text: 'Converted' },
  { value: 'lost', text: 'Lost' }
];

// Status colors mapping
const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    new: 'info',
    contacted: 'warning',
    qualified: 'purple',
    converted: 'success',
    lost: 'error'
  };
  return colors[status] || 'default';
};

// Apply filters
const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.leads.index'), {
    search: search.value,
    status: statusFilter.value,
    lead_source_id: sourceFilter.value
  }, {
    preserveState: true,
    preserveScroll: true,
    onFinish: () => loading.value = false
  });
};

// Clear filters
const clearFilters = () => {
  search.value = '';
  statusFilter.value = '';
  sourceFilter.value = '';
  applyFilters();
};

// Navigate to create page
const createLead = () => {
  router.visit(route('crm.leads.create'));
};

// View modal state
const showViewModal = ref(false);
const selectedViewLead = ref<Lead | null>(null);

// Open view modal
const openViewModal = (lead: Lead) => {
  selectedViewLead.value = lead;
  showViewModal.value = true;
};

// Navigate to edit page
const editLead = (id: number) => {
  router.visit(route('crm.leads.edit', { id: id }));
};

// Delete lead
const deleteLead = (id: number, name: string) => {
  if (confirm(`Are you sure you want to delete lead "${name}"?`)) {
    router.delete(route('crm.leads.destroy', id), {
      preserveScroll: true
    });
  }
};

// Conversion modal state
const showConversionModal = ref(false);
const selectedLead = ref(null);

// Open conversion modal
const openConversionModal = (lead: Lead) => {
  selectedLead.value = lead;
  showConversionModal.value = true;
};

// Handle successful conversion
const handleConverted = () => {
  showConversionModal.value = false;
  selectedLead.value = null;
};

// Convert to Partner
const convertToPartner = (id: number) => {
  if (confirm('Convert this lead to a Partner?')) {
    router.post(route('crm.leads.convert-to-partner', { id: id }));
  }
};

// Convert to Ticket
const convertToTicket = (id: number) => {
  if (confirm('Create a support ticket for this lead?')) {
    router.post(route('crm.leads.convert-to-ticket', { id: id }));
  }
};

// Source filter options
const sourceOptions = computed(() => {
  return [
    { value: '', text: 'All Sources' },
    ...props.leadSources.map((source: any) => ({
      value: source.id,
      text: source.name
    }))
  ];
});

// Safe access to leads data
const leadsData = computed(() => props.leads?.data || []);

watch([search, statusFilter, sourceFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <v-row class="mb-4">
    <v-col cols="12" sm="6" md="3">
      <v-card elevation="0" class="bg-lightprimary pa-4 rounded-lg">
        <div class="d-flex align-center">
          <v-avatar color="primary" size="40" class="mr-3">
            <v-icon color="white">mdi-account-group</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-medium-emphasis">Total Leads</div>
            <div class="text-h5 font-weight-bold">{{ stats.total || 0 }}</div>
          </div>
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" sm="6" md="3">
      <v-card elevation="0" class="bg-lightinfo pa-4 rounded-lg">
        <div class="d-flex align-center">
          <v-avatar color="info" size="40" class="mr-3">
            <v-icon color="white">mdi-account-plus</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-medium-emphasis">New Leads</div>
            <div class="text-h5 font-weight-bold">{{ stats.new || 0 }}</div>
          </div>
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" sm="6" md="3">
      <v-card elevation="0" class="bg-lightwarning pa-4 rounded-lg">
        <div class="d-flex align-center">
          <v-avatar color="warning" size="40" class="mr-3">
            <v-icon color="white">mdi-account-check</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-medium-emphasis">Qualified</div>
            <div class="text-h5 font-weight-bold">{{ stats.qualified || 0 }}</div>
          </div>
        </div>
      </v-card>
    </v-col>
    <v-col cols="12" sm="6" md="3">
      <v-card elevation="0" class="bg-lightsuccess pa-4 rounded-lg">
        <div class="d-flex align-center">
          <v-avatar color="success" size="40" class="mr-3">
            <v-icon color="white">mdi-account-convert</v-icon>
          </v-avatar>
          <div>
            <div class="text-subtitle-2 text-medium-emphasis">Converted</div>
            <div class="text-h5 font-weight-bold">{{ stats.converted || 0 }}</div>
          </div>
        </div>
      </v-card>
    </v-col>
  </v-row>

  <v-row>
    <v-col cols="12">
      <UiParentCard title="Leads Management">
        <template v-slot:action>
          <v-btn color="primary" @click="createLead">
            <v-icon left class="mr-1">mdi-plus</v-icon>
            New Lead
          </v-btn>
        </template>

        <!-- Filter Section -->
        <v-row class="mb-4">
          <v-col cols="12" md="4">
            <v-text-field
              v-model="search"
              label="Search"
              placeholder="Search by name, email, company..."
              variant="outlined"
              density="comfortable"
              prepend-inner-icon="mdi-magnify"
              clearable
              hide-details
              @keyup.enter="applyFilters"
            />
          </v-col>
          
          <v-col cols="12" md="3">
            <v-select
              v-model="statusFilter"
              :items="statusOptions"
              item-title="text"
              item-value="value"
              label="Status"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>
          
          <v-col cols="12" md="3">
            <v-select
              v-model="sourceFilter"
              :items="sourceOptions"
              item-title="text"
              item-value="value"
              label="Source"
              variant="outlined"
              density="comfortable"
              hide-details
            />
          </v-col>
          
          <v-col cols="12" md="2" class="d-flex gap-2">
            <v-btn color="primary" @click="applyFilters" block height="48">
              Filter
            </v-btn>
          </v-col>
        </v-row>

        <!-- Leads Table -->
        <div class="table-responsive">
        <v-table>
          <thead>
            <tr>
              <th class="text-left">Name</th>
              <th class="text-left">Company</th>
              <th class="text-left">Source</th>
              <th class="text-center">Score</th>
              <th class="text-left">Status</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="leadsData.length === 0">
              <td colspan="6" class="text-center py-8">
                <div class="text-h6 text-medium-emphasis">No leads found</div>
              </td>
            </tr>
            <tr v-for="lead in leadsData" :key="lead.id">
              <td>
                <div class="font-weight-medium">{{ lead.full_name }}</div>
                <div class="text-caption text-medium-emphasis">{{ lead.email }}</div>
              </td>
              <td>{{ lead.company_name || '-' }}</td>
              <td>
                {{ lead.lead_source?.name || '-' }}
              </td>
              <td class="text-center">
                <v-chip
                  :color="lead.score >= 70 ? 'success' : lead.score >= 40 ? 'warning' : 'default'"
                  size="small"
                  variant="flat"
                >
                  {{ lead.score }}
                </v-chip>
              </td>
              <td>
                <v-chip
                  :color="getStatusColor(lead.status)"
                  size="small"
                  label
                >
                  {{ lead.status.charAt(0).toUpperCase() + lead.status.slice(1) }}
                </v-chip>
              </td>
              <td class="text-right">
                <div class="d-flex justify-end gap-1">
                  <!-- View Action -->
                  <v-btn size="small" icon variant="text" @click="openViewModal(lead)" title="Quick View">
                    <v-icon color="info">mdi-eye</v-icon>
                  </v-btn>

                  <!-- Edit Action with Menu -->
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn size="small" icon variant="text" v-bind="props" title="Edit & Actions">
                        <v-icon color="primary">mdi-pencil</v-icon>
                      </v-btn>
                    </template>
                    <v-list density="compact">
                      <v-list-item @click="editLead(lead.id)" value="edit">
                        <template v-slot:prepend>
                          <v-icon size="small" class="mr-2">mdi-pencil</v-icon>
                        </template>
                        <v-list-item-title>Edit Lead</v-list-item-title>
                      </v-list-item>

                      <Link :href="route('crm.leads.show', lead.id)" class="text-decoration-none text-high-emphasis">
                        <v-list-item value="details">
                          <template v-slot:prepend>
                            <v-icon size="small" class="mr-2">mdi-file-document-outline</v-icon>
                          </template>
                          <v-list-item-title>Full Details</v-list-item-title>
                        </v-list-item>
                      </Link>

                      <v-list-item 
                        v-if="lead.status === 'qualified'" 
                        @click="openConversionModal(lead)"
                        value="convert-client"
                      >
                        <template v-slot:prepend>
                          <v-icon size="small" class="mr-2">mdi-account-convert</v-icon>
                        </template>
                        <v-list-item-title>Convert to Client</v-list-item-title>
                      </v-list-item>

                      <v-list-item 
                        v-if="lead.status !== 'converted'"
                        @click="convertToPartner(lead.id)"
                        value="convert-partner"
                      >
                        <template v-slot:prepend>
                          <v-icon size="small" class="mr-2">mdi-handshake-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Partner</v-list-item-title>
                      </v-list-item>

                      <v-list-item @click="convertToTicket(lead.id)" value="convert-ticket">
                        <template v-slot:prepend>
                          <v-icon size="small" class="mr-2">mdi-ticket-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Ticket</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>

                  <!-- Delete Action -->
                  <v-btn size="small" icon variant="text" @click="deleteLead(lead.id, lead.full_name)" title="Delete">
                    <v-icon color="error">mdi-delete</v-icon>
                  </v-btn>
                </div>
              </td>
            </tr>
          </tbody>
        </v-table>
        </div>

        <!-- Pagination -->
        <v-row class="mt-4" v-if="leadsData.length > 0">
          <v-col cols="12" class="d-flex justify-space-between align-center">
            <div class="text-body-2 text-medium-emphasis">
              Showing {{ leads.from }} to {{ leads.to }} of {{ leads.total }} leads
            </div>
            <v-pagination
              v-model="leads.current_page"
              :length="leads.last_page"
              @update:model-value="(page) => router.get(route('crm.leads.index'), { ...filters, page })"
              :total-visible="5"
              density="comfortable"
            />
          </v-col>
        </v-row>
      </UiParentCard>
    </v-col>
  </v-row>

  <!-- Lead View Modal -->
  <v-dialog v-model="showViewModal" max-width="800px">
    <v-card v-if="selectedViewLead">
      <v-card-title class="d-flex align-center justify-space-between pa-4 bg-primary">
        <span class="text-h5 text-white">Lead Details</span>
        <v-btn icon="mdi-close" variant="text" color="white" @click="showViewModal = false"></v-btn>
      </v-card-title>

      <v-card-text class="pa-6">
        <v-row>
          <v-col cols="12" md="6">
            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Personal Information</h3>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Full Name</div>
              <div class="text-body-1 font-weight-medium">{{ selectedViewLead.full_name }}</div>
            </div>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Email</div>
              <div class="text-body-1">{{ selectedViewLead.email || '-' }}</div>
            </div>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Phone</div>
              <div class="text-body-1">{{ selectedViewLead.phone || '-' }}</div>
            </div>
          </v-col>
          <v-col cols="12" md="6">
            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Company & Status</h3>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Company</div>
              <div class="text-body-1">{{ selectedViewLead.company_name || '-' }}</div>
            </div>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Status</div>
              <v-chip :color="getStatusColor(selectedViewLead.status)" size="small" label>
                {{ selectedViewLead.status.charAt(0).toUpperCase() + selectedViewLead.status.slice(1) }}
              </v-chip>
            </div>
            <div class="mb-4">
              <div class="text-caption text-grey-darken-1">Score</div>
              <v-chip :color="selectedViewLead.score >= 70 ? 'success' : 'warning'" size="small">
                {{ selectedViewLead.score }}/100
              </v-chip>
            </div>
          </v-col>
        </v-row>
        <v-divider class="my-4"></v-divider>
        <div v-if="selectedViewLead.notes">
          <h3 class="text-subtitle-1 font-weight-bold mb-2 text-primary">Notes</h3>
          <div class="text-body-2 pa-3 bg-grey-lighten-4 rounded">
            {{ selectedViewLead.notes }}
          </div>
        </div>
      </v-card-text>
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn color="primary" variant="elevated" @click="showViewModal = false">Close</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Lead Conversion Modal -->
  <LeadConversionModal
    v-if="selectedLead"
    :show="showConversionModal"
    :lead="selectedLead"
    :deal-stages="dealStages"
    @close="showConversionModal = false; selectedLead = null"
    @converted="handleConverted"
  />
</template>

<style scoped>
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.bg-lightprimary { background-color: #eef2f6; }
.bg-lightinfo { background-color: #e3f2fd; }
.bg-lightwarning { background-color: #fff8e1; }
.bg-lightsuccess { background-color: #e8f5e9; }
.table-responsive { overflow-x: auto; }
</style>
