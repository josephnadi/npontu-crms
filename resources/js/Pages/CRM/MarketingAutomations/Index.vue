<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  automations: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const typeFilter = ref(props.filters.type || '');
const statusFilter = ref(props.filters.status || '');
const loading = ref(false);

const types = [
  { title: 'All Types', value: '' },
  { title: 'Email Campaign', value: 'email_campaign' },
  { title: 'Drip Sequence', value: 'drip_sequence' },
  { title: 'Newsletter', value: 'newsletter' },
  { title: 'SMS Alert', value: 'sms_alert' },
];

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'Draft', value: 'draft' },
  { title: 'Active', value: 'active' },
  { title: 'Paused', value: 'paused' },
  { title: 'Completed', value: 'completed' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.marketing-automations.index'), { 
    search: search.value, 
    type: typeFilter.value,
    status: statusFilter.value 
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  typeFilter.value = '';
  statusFilter.value = '';
  applyFilters();
};

const deleteAutomation = (id) => {
  if (confirm('Are you sure you want to delete this automation?')) {
    router.delete(route('crm.marketing-automations.destroy', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'draft': return 'grey';
    case 'active': return 'success';
    case 'paused': return 'warning';
    case 'completed': return 'info';
    default: return 'grey';
  }
};

const getTypeIcon = (type) => {
  switch (type) {
    case 'email_campaign': return 'mdi-email-outline';
    case 'drip_sequence': return 'mdi-email-sync-outline';
    case 'newsletter': return 'mdi-newspaper-variant-outline';
    case 'sms_alert': return 'mdi-cellphone-message';
    default: return 'mdi-robot-outline';
  }
};

watch([search, typeFilter, statusFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Marketing Automations" />
  <DashboardLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div>
                <h1 class="text-h4 mb-1">Marketing Automations</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Automate your marketing workflows and campaigns.</p>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                :to="route('crm.marketing-automations.create')"
              >
                New Automation
              </v-btn>
            </v-card-text>
          </v-card>

          <v-card elevation="0">
            <v-card-text class="pa-4">
              <v-row>
                <v-col cols="12" sm="4">
                  <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    label="Search automations..."
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="3">
                  <v-select
                    v-model="typeFilter"
                    :items="types"
                    label="Type"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="3">
                  <v-select
                    v-model="statusFilter"
                    :items="statuses"
                    label="Status"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="2" class="d-flex align-center">
                  <v-btn
                    variant="text"
                    color="primary"
                    @click="clearFilters"
                    block
                  >
                    Clear
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>

            <v-divider></v-divider>

            <v-table hover>
              <thead>
                <tr>
                  <th class="text-left">Automation Name</th>
                  <th class="text-left">Type</th>
                  <th class="text-left">Status</th>
                  <th class="text-center">Sent / Open / Click</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="5" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                  </td>
                </tr>
                <tr v-else-if="automations.data.length === 0">
                  <td colspan="5" class="text-center py-10">
                    <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-robot-outline</v-icon>
                    <p class="text-h6 text-medium-emphasis">No automations found.</p>
                  </td>
                </tr>
                <tr v-for="automation in automations.data" :key="automation.id">
                  <td class="py-4">
                    <div class="d-flex flex-column">
                      <Link :href="route('crm.marketing-automations.show', automation.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary">
                        {{ automation.name }}
                      </Link>
                      <span class="text-caption text-medium-emphasis text-truncate" style="max-width: 300px;">
                        {{ automation.description || 'No description' }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-center">
                      <v-icon size="small" class="mr-2">{{ getTypeIcon(automation.type) }}</v-icon>
                      <span class="text-body-2">{{ automation.type.replace('_', ' ') }}</span>
                    </div>
                  </td>
                  <td>
                    <v-chip
                      :color="getStatusColor(automation.status)"
                      size="x-small"
                      label
                      class="text-uppercase font-weight-bold"
                    >
                      {{ automation.status }}
                    </v-chip>
                  </td>
                  <td class="text-center">
                    <div class="d-flex justify-center gap-4">
                      <div class="d-flex flex-column">
                        <span class="text-body-2 font-weight-bold">{{ automation.sent_count }}</span>
                        <span class="text-caption text-medium-emphasis">Sent</span>
                      </div>
                      <div class="d-flex flex-column">
                        <span class="text-body-2 font-weight-bold text-success">{{ automation.open_count }}</span>
                        <span class="text-caption text-medium-emphasis">Opens</span>
                      </div>
                      <div class="d-flex flex-column">
                        <span class="text-body-2 font-weight-bold text-info">{{ automation.click_count }}</span>
                        <span class="text-caption text-medium-emphasis">Clicks</span>
                      </div>
                    </div>
                  </td>
                  <td class="text-right">
                    <v-menu>
                      <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-pencil" size="small" v-bind="props"></v-btn>
                      </template>
                      <v-list>
                        <v-list-item :to="route('crm.marketing-automations.show', automation.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-eye</v-icon>
                          </template>
                          <v-list-item-title>View Details</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="route('crm.marketing-automations.edit', automation.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-pencil</v-icon>
                          </template>
                          <v-list-item-title>Edit Automation</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deleteAutomation(automation.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-delete</v-icon>
                          </template>
                          <v-list-item-title>Delete Automation</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-divider></v-divider>

            <v-card-text v-if="automations.links.length > 3" class="pa-4 d-flex justify-center">
              <v-pagination
                v-model="automations.current_page"
                :length="automations.last_page"
                @update:modelValue="router.get(route('crm.marketing-automations.index', { page: $event }))"
                total-visible="7"
                rounded="circle"
              ></v-pagination>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>

<style scoped>
.gap-4 {
  gap: 16px;
}
</style>
