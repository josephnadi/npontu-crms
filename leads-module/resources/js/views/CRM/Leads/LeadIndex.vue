<script setup lang="ts">
import { ref, computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import LeadConversionModal from '@/Components/CRM/LeadConversionModal.vue';

const page = usePage();

// Props from controller
const props = defineProps({
  leads: {
    type: Object,
    default: () => ({ data: [] })
  },
  leadSources: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  dealStages: {
    type: Array,
    default: () => []
  }
});

console.log('LeadIndex Props:', props);
console.log('Leads:', props.leads);
console.log('LeadSources:', props.leadSources);

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
  router.get('/leads', {
    search: search.value,
    status: statusFilter.value,
    lead_source_id: sourceFilter.value
  }, {
    preserveState: true,
    preserveScroll: true
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
  router.visit('/leads/create');
};

// View modal state
const showViewModal = ref(false);
const selectedViewLead = ref(null);

// Open view modal
const openViewModal = (lead) => {
  selectedViewLead.value = lead;
  showViewModal.value = true;
};

// Navigate to edit page
const editLead = (id) => {
  router.visit(`/leads/${id}/edit`);
};

// Delete lead
const deleteLead = (id, name) => {
  if (confirm(`Are you sure you want to delete lead "${name}"?`)) {
    router.delete(`/leads/${id}`, {
      preserveScroll: true
    });
  }
};

// Conversion modal state
const showConversionModal = ref(false);
const selectedLead = ref(null);

// Open conversion modal
const openConversionModal = (lead) => {
  selectedLead.value = lead;
  showConversionModal.value = true;
};

// Handle successful conversion
const handleConverted = () => {
  showConversionModal.value = false;
  selectedLead.value = null;
  // The controller will redirect and show success message
};

// Source filter options
const sourceOptions = computed(() => {
  if (!props.leadSources || !Array.isArray(props.leadSources)) {
    return [{ value: '', text: 'All Sources' }];
  }
  return [
    { value: '', text: 'All Sources' },
    ...props.leadSources.map((source) => ({
      value: source.id,
      text: source.name
    }))
  ];
});

// Safe access to leads data with better defaults
const leadsData = computed(() => {
  try {
    return Array.isArray(props.leads?.data) ? props.leads.data : [];
  } catch (e) {
    console.error('Error accessing leads data:', e);
    return [];
  }
});

const leadsFrom = computed(() => props.leads?.from || 0);
const leadsTo = computed(() => props.leads?.to || 0);
const leadsTotal = computed(() => props.leads?.total || 0);
const leadsLastPage = computed(() => props.leads?.last_page || 1);
const leadsCurrentPage = computed(() => props.leads?.current_page || 1);
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <v-row>
    <v-col cols="12">
      <UiParentCard title="Leads Management">
        <!-- Filters and Actions -->
        <template v-slot:action>
          <v-btn color="primary" @click="createLead">
            <v-icon left>mdi-plus</v-icon>
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
            />
          </v-col>
          
          <v-col cols="12" md="2" class="d-flex flex-column gap-2">
            <v-btn color="primary" @click="applyFilters">
              Filter
            </v-btn>
            <v-btn color="secondary" variant="outlined" @click="clearFilters">
              Clear
            </v-btn>
          </v-col>
        </v-row>

        <!-- Leads Table -->
        <div class="table-responsive">
        <v-table>
          <thead>
            <tr>
              <th class="text-left" style="min-width: 150px;">Name</th>
              <th class="text-left" style="min-width: 180px;">Email</th>
              <th class="text-left" style="min-width: 150px;">Company</th>
              <th class="text-left" style="min-width: 120px;">Source</th>
              <th class="text-center" style="width: 80px;">Score</th>
              <th class="text-left" style="min-width: 120px;">Owner</th>
              <th class="text-center" style="width: 180px; min-width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="leadsData.length === 0">
              <td colspan="7" class="text-center py-8">
                <div class="text-h6 text-medium-emphasis">No leads found</div>
                <p class="text-body-2 text-medium-emphasis mt-2">
                  Try adjusting your filters or create a new lead
                </p>
              </td>
            </tr>
            <tr v-for="lead in leadsData" :key="lead.id">
              <td>
                <div class="font-weight-medium">{{ lead.full_name }}</div>
              </td>
              <td>{{ lead.email || '-' }}</td>
              <td>{{ lead.company_name || '-' }}</td>
              <td>
                <span v-if="lead.lead_source">
                  {{ lead.lead_source.name }}
                </span>
                <span v-else class="text-medium-emphasis">-</span>
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
                <span v-if="lead.owner">
                  {{ lead.owner.name }}
                </span>
                <span v-else class="text-medium-emphasis">-</span>
              </td>
              <td>
                <div class="action-buttons">
                  <v-btn
                    size="small"
                    variant="text"
                    icon
                    @click="openViewModal(lead)"
                    title="View Lead"
                    class="action-icon"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="pc-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9M12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17M12,4.5C7,4.5 2.73,7.61 1,12C2.73,16.39 7,19.5 12,19.5C17,19.5 21.27,16.39 23,12C21.27,7.61 17,4.5 12,4.5Z" />
                    </svg>
                  </v-btn>
                  <v-btn
                    size="small"
                    variant="text"
                    icon
                    @click="editLead(lead.id)"
                    title="Edit Lead"
                    class="action-icon"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="pc-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z" />
                    </svg>
                  </v-btn>
                  <v-btn
                    size="small"
                    variant="text"
                    icon
                    @click="deleteLead(lead.id, lead.full_name)"
                    title="Delete Lead"
                    class="action-icon action-icon-delete"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="pc-icon" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z" />
                    </svg>
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
              Showing {{ leadsFrom }} to {{ leadsTo }} of {{ leadsTotal }} leads
            </div>
            <v-pagination
              :length="leadsLastPage"
              :model-value="leadsCurrentPage"
              @update:model-value="(page) => router.get('/leads', { ...filters, page })"
              :total-visible="7"
              density="comfortable"
            />
          </v-col>
        </v-row>
      </UiParentCard>
    </v-col>
  </v-row>

  <!-- Lead Conversion Modal -->
  <LeadConversionModal
    v-if="selectedLead"
    :show="showConversionModal"
    :lead="selectedLead"
    :deal-stages="dealStages"
    @close="showConversionModal = false; selectedLead = null"
    @converted="handleConverted"
  />

  <!-- Lead View Modal -->
  <v-dialog v-model="showViewModal" max-width="900px">
    <v-card v-if="selectedViewLead">
      <v-card-title class="d-flex align-center justify-space-between pa-4 bg-primary">
        <span class="text-h5 text-white">Lead Details</span>
        <v-btn icon="mdi-close" variant="text" color="white" @click="showViewModal = false"></v-btn>
      </v-card-title>

      <v-divider></v-divider>

      <v-card-text class="pa-6">
        <v-row>
          <!-- Left Column -->
          <v-col cols="12" md="6">
            <div class="mb-6">
              <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Personal Information</h3>
              
              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Full Name</div>
                <div class="text-body-1 font-weight-medium">{{ selectedViewLead.full_name }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Email</div>
                <div class="text-body-1">{{ selectedViewLead.email || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Phone</div>
                <div class="text-body-1">{{ selectedViewLead.phone || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Mobile</div>
                <div class="text-body-1">{{ selectedViewLead.mobile || '-' }}</div>
              </div>
            </div>

            <v-divider class="my-4"></v-divider>

            <div>
              <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Company Information</h3>
              
              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Company</div>
                <div class="text-body-1">{{ selectedViewLead.company_name || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Job Title</div>
                <div class="text-body-1">{{ selectedViewLead.job_title || '-' }}</div>
              </div>
            </div>
          </v-col>

          <!-- Right Column -->
          <v-col cols="12" md="6">
            <div class="mb-6">
              <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Lead Information</h3>
              
              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Status</div>
                <v-chip
                  :color="getStatusColor(selectedViewLead.status)"
                  size="small"
                  label
                >
                  {{ selectedViewLead.status.charAt(0).toUpperCase() + selectedViewLead.status.slice(1) }}
                </v-chip>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Lead Score</div>
                <v-chip
                  :color="selectedViewLead.score >= 70 ? 'success' : selectedViewLead.score >= 40 ? 'warning' : 'default'"
                  size="default"
                >
                  {{ selectedViewLead.score }}/100
                </v-chip>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Source</div>
                <div class="text-body-1">{{ selectedViewLead.lead_source?.name || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Owner</div>
                <div class="text-body-1">{{ selectedViewLead.owner?.name || '-' }}</div>
              </div>
            </div>

            <v-divider class="my-4"></v-divider>

            <div>
              <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Address</h3>
              
              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">City</div>
                <div class="text-body-1">{{ selectedViewLead.city || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">State</div>
                <div class="text-body-1">{{ selectedViewLead.state || '-' }}</div>
              </div>

              <div class="mb-4">
                <div class="text-caption text-grey-darken-1 mb-1">Country</div>
                <div class="text-body-1">{{ selectedViewLead.country || '-' }}</div>
              </div>
            </div>
          </v-col>

          <!-- Notes Section (Full Width) -->
          <v-col cols="12" v-if="selectedViewLead.notes">
            <v-divider class="mb-4"></v-divider>
            <h3 class="text-subtitle-1 font-weight-bold mb-4 text-primary">Notes</h3>
            <div class="text-body-2 pa-3 bg-grey-lighten-4 rounded">
              {{ selectedViewLead.notes }}
            </div>
          </v-col>
        </v-row>
      </v-card-text>

      <v-divider></v-divider>

      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          variant="elevated"
          @click="showViewModal = false"
        >
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.gap-1 {
  gap: 4px;
}

.gap-2 {
  gap: 8px;
}

.table-responsive {
  overflow-x: auto;
  width: 100%;
}

td {
  vertical-align: middle !important;
}

/* Force actions column width */
th:last-child {
  min-width: 180px !important;
  width: 180px !important;
}

.bg-primary {
  background-color: rgb(var(--v-theme-primary));
}

.bg-grey-lighten-4 {
  background-color: #f5f5f5;
}

/* Action buttons styling - sidebar icon style */
.action-buttons {
  display: flex;
  gap: 4px;
  justify-content: center;
  align-items: center;
}

.action-icon {
  border-radius: 8px;
  transition: all 0.2s ease;
  color: rgb(var(--v-theme-lightText));
  
  .pc-icon {
    width: 20px;
    height: 20px;
  }
  
  &:hover {
    background: rgb(var(--v-theme-primary), 0.05);
    color: rgb(var(--v-theme-primary));
  }
  
  &.action-icon-delete:hover {
    background: rgb(var(--v-theme-error), 0.05);
    color: rgb(var(--v-theme-error));
  }
}
</style>
