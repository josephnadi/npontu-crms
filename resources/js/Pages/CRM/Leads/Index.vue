<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  leads: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const loading = ref(false);

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'New', value: 'new' },
  { title: 'Contacted', value: 'contacted' },
  { title: 'Qualified', value: 'qualified' },
  { title: 'Converted', value: 'converted' },
  { title: 'Lost', value: 'lost' },
];

watch([search, statusFilter], debounce(() => {
  loading.value = true;
  router.get(route('crm.leads.index'), { 
    search: search.value, 
    status: statusFilter.value 
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
}, 300));

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
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h3 font-weight-bold">Leads</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Track potential customers and opportunities</p>
        </div>
        <Link :href="route('crm.leads.create')">
          <v-btn color="primary" prepend-icon="mdi-plus">Add Lead</v-btn>
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
                  label="Search leads..."
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="statusFilter"
                  :items="statuses"
                  label="Status Filter"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-select>
              </v-col>
            </v-row>
          </v-card-text>

          <v-progress-linear
            v-if="loading"
            indeterminate
            color="primary"
            height="2"
          ></v-progress-linear>

          <v-table>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Lead Name</th>
                <th class="text-left font-weight-bold">Company</th>
                <th class="text-left font-weight-bold">Status</th>
                <th class="text-left font-weight-bold">Source</th>
                <th class="text-left font-weight-bold">Score</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="lead in leads.data" :key="lead.id">
                <td>
                  <Link :href="route('crm.leads.show', lead.id)" class="text-decoration-none font-weight-bold text-primary">
                    {{ lead.first_name }} {{ lead.last_name }}
                  </Link>
                  <div class="text-caption text-medium-emphasis">{{ lead.email }}</div>
                </td>
                <td>{{ lead.company_name || 'N/A' }}</td>
                <td>
                  <v-chip :color="getStatusColor(lead.status)" size="x-small" variant="flat" class="text-capitalize">
                    {{ lead.status }}
                  </v-chip>
                </td>
                <td>{{ lead.source || 'N/A' }}</td>
                <td>
                  <v-progress-linear
                    :model-value="lead.score"
                    :color="lead.score > 70 ? 'success' : (lead.score > 40 ? 'warning' : 'error')"
                    height="6"
                    rounded
                  ></v-progress-linear>
                  <div class="text-caption text-right mt-1">{{ lead.score }}%</div>
                </td>
                <td class="text-right">
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                    </template>
                    <v-list size="small">
                      <v-list-item @click="router.get(route('crm.leads.show', lead.id))" prepend-icon="mdi-eye">
                        <v-list-item-title>View Details</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="router.get(route('crm.leads.edit', lead.id))" prepend-icon="mdi-pencil">
                        <v-list-item-title>Edit</v-list-item-title>
                      </v-list-item>
                      <v-divider></v-divider>
                      <v-list-item @click="deleteLead(lead.id)" prepend-icon="mdi-delete" color="error">
                        <v-list-item-title>Delete</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
                </td>
              </tr>
              <tr v-if="leads.data.length === 0">
                <td colspan="6" class="text-center py-8 text-medium-emphasis">
                  No leads found. Click "Add Lead" to get started.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>

          <div class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="leads.current_page"
              :length="leads.last_page"
              @update:modelValue="router.get(route('crm.leads.index'), { page: $event, search: search, status: statusFilter }, { preserveState: true })"
              size="small"
            ></v-pagination>
          </div>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
