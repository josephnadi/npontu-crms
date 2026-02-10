<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  leads: Object,
  filters: Object,
  dealStages: Array, // Added for conversion
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const sourceFilter = ref(props.filters.source || '');
const loading = ref(false);

const showConvertModal = ref(false);
const selectedLead = ref(null);

const convertForm = useForm({
  create_client: true,
  create_deal: true,
  deal_title: '',
  deal_value: 0,
  deal_stage_id: props.dealStages?.[0]?.id || null,
});

const openConvertModal = (lead) => {
  selectedLead.value = lead;
  convertForm.deal_title = `${lead.company_name || lead.first_name} Deal`;
  showConvertModal.value = true;
};

const submitConvert = () => {
  convertForm.post(route('crm.leads.convert', selectedLead.value.id), {
    onSuccess: () => {
      showConvertModal.value = false;
    }
  });
};

const updateStatus = (lead, newStatus) => {
  router.patch(route('crm.leads.update', lead.id), {
    ...lead,
    status: newStatus,
    owner_id: lead.owner_id
  }, {
    preserveScroll: true
  });
};

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'New', value: 'new' },
  { title: 'Contacted', value: 'contacted' },
  { title: 'Qualified', value: 'qualified' },
  { title: 'Converted', value: 'converted' },
  { title: 'Lost', value: 'lost' },
];

const sources = [
  { title: 'All Sources', value: '' },
  { title: 'Website Form', value: 'Website Form' },
  { title: 'Social Media', value: 'Social Media' },
  { title: 'Referral', value: 'Referral' },
  { title: 'Direct', value: 'Direct' },
  { title: 'Email', value: 'Email' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.leads.index'), { 
    search: search.value, 
    status: statusFilter.value,
    source: sourceFilter.value
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  statusFilter.value = '';
  sourceFilter.value = '';
  applyFilters();
};

const getStatusColor = (status) => {
  switch (status) {
    case 'new': return 'info';
    case 'contacted': return 'warning';
    case 'qualified': return 'success';
    case 'converted': return 'primary';
    case 'lost': return 'error';
    default: return 'grey';
  }
};

const deleteLead = (id) => {
  if (confirm('Are you sure you want to delete this lead?')) {
    router.delete(route('crm.leads.destroy', id));
  }
};
</script>

<template>
  <Head title="Leads" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="mb-0">
        <div class="d-flex align-center text-caption text-medium-emphasis mb-2">
          <span>Home</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span>CRM</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span class="text-high-emphasis">Leads</span>
        </div>
        <h2 class="text-h3 font-weight-bold mb-6">Leads</h2>
      </v-col>

      <v-col cols="12">
        <v-card elevation="0" border>
          <v-card-title class="pa-4 d-flex justify-space-between align-center border-bottom">
            <span class="text-h6 font-weight-bold">Leads Management</span>
            <Link :href="route('crm.leads.create')">
              <v-btn color="primary" flat>New Lead</v-btn>
            </Link>
          </v-card-title>

          <v-card-text class="pa-4">
            <v-row align="center">
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="search"
                  label="Search"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  @keyup.enter="applyFilters"
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="2">
                <v-select
                  v-model="statusFilter"
                  :items="statuses"
                  label="Status"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" md="2">
                <v-select
                  v-model="sourceFilter"
                  :items="sources"
                  label="Source"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" md="2" class="d-flex flex-column gap-2">
                <v-btn color="primary" block @click="applyFilters" flat>Filter</v-btn>
                <v-btn variant="outlined" block @click="clearFilters">Clear</v-btn>
              </v-col>
            </v-row>
          </v-card-text>

          <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
            height="2"
          ></v-progress-linear>

          <v-table class="leads-table">
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Name</th>
                <th class="text-left font-weight-bold">Email</th>
                <th class="text-left font-weight-bold">Company</th>
                <th class="text-left font-weight-bold">Source</th>
                <th class="text-left font-weight-bold">Status</th>
                <th class="text-left font-weight-bold">Score</th>
                <th class="text-left font-weight-bold">Owner</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads.data" :key="lead.id">
                <td class="py-4">
                  <Link :href="route('crm.leads.show', lead.id)" class="text-decoration-none text-high-emphasis">
                    {{ lead.first_name }} {{ lead.last_name }}
                  </Link>
                </td>
                <td>{{ lead.email }}</td>
                <td>{{ lead.company_name || '-' }}</td>
                <td>{{ lead.source || '-' }}</td>
                <td>
                  <v-chip
                    :color="getStatusColor(lead.status)"
                    size="small"
                    variant="tonal"
                    class="text-capitalize font-weight-medium"
                    style="border-radius: 4px;"
                  >
                    {{ lead.status }}
                  </v-chip>
                </td>
                <td>
                  <v-avatar
                    size="28"
                    :color="lead.score >= 70 ? 'success' : (lead.score >= 40 ? 'warning' : 'error')"
                    class="text-white text-caption font-weight-bold"
                  >
                    {{ lead.score }}
                  </v-avatar>
                </td>
                <td>{{ lead.owner?.first_name || 'N/A' }}</td>
                <td class="text-right">
                  <v-menu location="bottom end" transition="scale-transition">
                    <template v-slot:activator="{ props }">
                      <v-btn
                        v-bind="props"
                        icon="mdi-dots-vertical"
                        variant="text"
                        size="small"
                        color="medium-emphasis"
                      ></v-btn>
                    </template>
                    <v-list density="compact" width="200">
                      <v-list-item @click="router.get(route('crm.leads.show', lead.id))" prepend-icon="mdi-eye">
                        <v-list-item-title>View Details</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="router.get(route('crm.leads.edit', lead.id))" prepend-icon="mdi-pencil">
                        <v-list-item-title>Edit Lead</v-list-item-title>
                      </v-list-item>
                      
                      <v-divider class="my-1"></v-divider>
                      
                      <v-list-subheader class="text-uppercase font-weight-bold text-caption">Move To / Status</v-list-subheader>
                      
                      <v-menu location="left start" open-on-hover transition="scale-transition">
                        <template v-slot:activator="{ props }">
                          <v-list-item v-bind="props" prepend-icon="mdi-swap-horizontal" append-icon="mdi-chevron-right">
                            <v-list-item-title>Change Status</v-list-item-title>
                          </v-list-item>
                        </template>
                        <v-list density="compact" width="150">
                          <v-list-item 
                            v-for="status in statuses.filter(s => s.value !== '')" 
                            :key="status.value"
                            @click="updateStatus(lead, status.value)"
                            :active="lead.status === status.value"
                          >
                            <v-list-item-title>{{ status.title }}</v-list-item-title>
                          </v-list-item>
                        </v-list>
                      </v-menu>

                      <v-list-item 
                        v-if="lead.status !== 'converted'"
                        @click="openConvertModal(lead)" 
                        prepend-icon="mdi-account-convert" 
                        class="text-success"
                      >
                        <v-list-item-title>Convert to Deal</v-list-item-title>
                      </v-list-item>

                      <v-divider class="my-1"></v-divider>
                      
                      <v-list-item @click="deleteLead(lead.id)" prepend-icon="mdi-delete" class="text-error">
                        <v-list-item-title>Delete Lead</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="leads.data.length === 0">
                <td colspan="8" class="text-center py-8 text-medium-emphasis">
                  No leads found. Click "New Lead" to get started.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>

          <div class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="leads.current_page"
              :length="leads.last_page"
              @update:modelValue="router.get(route('crm.leads.index'), { page: $event, search: search, status: statusFilter, source: sourceFilter }, { preserveState: true })"
              size="small"
              active-color="primary"
            ></v-pagination>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>

  <!-- Convert Lead Modal -->
  <v-dialog v-model="showConvertModal" max-width="500px">
    <v-card>
      <v-card-title class="pa-4 border-bottom d-flex justify-space-between align-center">
        <span class="text-h5 font-weight-bold">Convert Lead</span>
        <v-btn icon="mdi-close" variant="text" @click="showConvertModal = false"></v-btn>
      </v-card-title>
      <v-card-text class="pa-4">
        <p class="mb-4 text-body-2 text-medium-emphasis">
          Convert <strong>{{ selectedLead?.first_name }} {{ selectedLead?.last_name }}</strong> into a contact and optionally create a deal.
        </p>
        
        <v-checkbox
          v-model="convertForm.create_client"
          label="Create Client/Company"
          hide-details
          class="mb-2"
        ></v-checkbox>
        
        <v-checkbox
          v-model="convertForm.create_deal"
          label="Create Deal"
          hide-details
          class="mb-4"
        ></v-checkbox>

        <v-expand-transition>
          <div v-if="convertForm.create_deal">
            <v-text-field
              v-model="convertForm.deal_title"
              label="Deal Title"
              variant="outlined"
              density="comfortable"
              class="mb-3"
              :error-messages="convertForm.errors.deal_title"
            ></v-text-field>
            
            <v-text-field
              v-model="convertForm.deal_value"
              label="Deal Value (GHS)"
              type="number"
              variant="outlined"
              density="comfortable"
              class="mb-3"
              :error-messages="convertForm.errors.deal_value"
            ></v-text-field>

            <v-select
              v-model="convertForm.deal_stage_id"
              :items="dealStages"
              item-title="name"
              item-value="id"
              label="Deal Stage"
              variant="outlined"
              density="comfortable"
              :error-messages="convertForm.errors.deal_stage_id"
            ></v-select>
          </div>
        </v-expand-transition>
      </v-card-text>
      <v-divider></v-divider>
      <v-card-actions class="pa-4">
        <v-spacer></v-spacer>
        <v-btn variant="text" @click="showConvertModal = false">Cancel</v-btn>
        <v-btn color="success" @click="submitConvert" :loading="convertForm.processing">Convert Lead</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.leads-table :deep(th) {
  font-size: 0.875rem !important;
  color: rgba(0, 0, 0, 0.87) !important;
  background-color: transparent !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.leads-table :deep(td) {
  font-size: 0.875rem !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
}

.gap-1 {
  gap: 4px;
}
.gap-2 {
  gap: 8px;
}

.border-bottom {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
</style>
