<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  partners: Object,
  filters: Object,
});

const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');
const statusFilter = ref(props.filters?.status || '');
const loading = ref(false);

const types = [
  { title: 'All Types', value: '' },
  { title: 'Reseller', value: 'reseller' },
  { title: 'Affiliate', value: 'affiliate' },
  { title: 'Referral', value: 'referral' },
  { title: 'Technology', value: 'technology' },
];

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' },
  { title: 'Pending', value: 'pending' },
  { title: 'Terminated', value: 'terminated' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.partners.index'), { 
    search: search.value, 
    type: typeFilter.value,
    status: statusFilter.value 
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onFinish: () => loading.value = false
  });
};

watch([search, typeFilter, statusFilter], debounce(() => {
  applyFilters();
}, 500));

const deletePartner = (id) => {
  if (confirm('Are you sure you want to delete this partner?')) {
    router.delete(route('crm.partners.destroy', id));
  }
};

const convertToLead = (id) => {
  if (confirm('Are you sure you want to convert this partner back to a lead?')) {
    router.post(route('crm.partners.convert-to-lead', id));
  }
};

const convertToTicket = (id) => {
  if (confirm('Are you sure you want to create a support ticket from this partner?')) {
    router.post(route('crm.partners.convert-to-ticket', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'active': return 'success';
    case 'inactive': return 'grey';
    case 'pending': return 'warning';
    case 'terminated': return 'error';
    default: return 'grey';
  }
};
</script>

<template>
  <Head title="Partners" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h3 font-weight-bold">Partners</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Manage your business partnerships and referral network</p>
        </div>
        <Link :href="route('crm.partners.create')">
          <v-btn color="primary" prepend-icon="mdi-plus">Add Partner</v-btn>
        </Link>
      </v-col>

      <v-col cols="12">
        <v-card elevation="0" border>
          <v-card-text class="pa-4">
            <v-row>
              <v-col cols="12" md="4">
                <v-text-field
                  v-model="search"
                  prepend-inner-icon="mdi-magnify"
                  label="Search partners..."
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  clearable
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="typeFilter"
                  :items="types"
                  label="Type"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="statusFilter"
                  :items="statuses"
                  label="Status"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>

          <v-table hover>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Name</th>
                <th class="text-left font-weight-bold">Type</th>
                <th class="text-left font-weight-bold">Status</th>
                <th class="text-left font-weight-bold">Email</th>
                <th class="text-left font-weight-bold">Comm. Rate</th>
                <th class="text-left font-weight-bold">Account Manager</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="partner in partners.data" :key="partner.id">
                <td>
                  <Link :href="route('crm.partners.show', partner.id)" class="text-decoration-none font-weight-bold text-primary">
                    {{ partner.name }}
                  </Link>
                </td>
                <td>
                  <v-chip size="x-small" variant="tonal" class="text-uppercase">
                    {{ partner.type }}
                  </v-chip>
                </td>
                <td>
                  <v-chip
                    :color="getStatusColor(partner.status)"
                    size="x-small"
                    label
                    class="text-uppercase font-weight-bold"
                  >
                    {{ partner.status }}
                  </v-chip>
                </td>
                <td>{{ partner.email || 'N/A' }}</td>
                <td>
                  <span v-if="partner.commission_rate">{{ partner.commission_rate }}%</span>
                  <span v-else>N/A</span>
                </td>
                <td>
                  <div v-if="partner.account_manager" class="d-flex align-center">
                    <v-avatar size="24" class="mr-2" color="primary">
                      <span class="text-caption text-white">{{ partner.account_manager.name.charAt(0) }}</span>
                    </v-avatar>
                    <span class="text-body-2">{{ partner.account_manager.name }}</span>
                  </div>
                  <span v-else class="text-medium-emphasis text-body-2">Unassigned</span>
                </td>
                <td class="text-right">
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn
                        color="primary"
                        icon
                        flat
                        size="small"
                        v-bind="props"
                      >
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                    </template>
                    <v-list>
                      <Link :href="route('crm.partners.show', partner.id)" class="text-decoration-none">
                        <v-list-item>
                          <template v-slot:prepend>
                            <v-icon size="small">mdi-eye</v-icon>
                          </template>
                          <v-list-item-title>View Details</v-list-item-title>
                        </v-list-item>
                      </Link>
                      <Link :href="route('crm.partners.edit', partner.id)" class="text-decoration-none">
                        <v-list-item>
                          <template v-slot:prepend>
                            <v-icon size="small">mdi-pencil</v-icon>
                          </template>
                          <v-list-item-title>Edit Partner</v-list-item-title>
                        </v-list-item>
                      </Link>
                      <v-list-item @click="convertToLead(partner.id)">
                        <template v-slot:prepend>
                          <v-icon size="small">mdi-account-convert-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Lead</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="convertToTicket(partner.id)">
                        <template v-slot:prepend>
                          <v-icon size="small">mdi-ticket-outline</v-icon>
                        </template>
                        <v-list-item-title>Convert to Ticket</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="deletePartner(partner.id)">
                        <template v-slot:prepend>
                          <v-icon size="small">mdi-delete</v-icon>
                        </template>
                        <v-list-item-title>Delete Partner</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="partners.data.length === 0">
                <td colspan="7" class="text-center py-8 text-medium-emphasis">
                  No partners found. Click "Add Partner" to get started.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>

          <div class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="partners.current_page"
              :length="partners.last_page"
              @update:modelValue="router.get(route('crm.partners.index'), { page: $event, search, type: typeFilter, status: statusFilter }, { preserveState: true })"
              size="small"
            ></v-pagination>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>