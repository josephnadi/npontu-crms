<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  invoices: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const statusFilter = ref(props.filters.status || '');
const loading = ref(false);

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'Draft', value: 'draft' },
  { title: 'Sent', value: 'sent' },
  { title: 'Paid', value: 'paid' },
  { title: 'Overdue', value: 'overdue' },
  { title: 'Cancelled', value: 'cancelled' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.invoices.index'), { 
    search: search.value, 
    status: statusFilter.value 
  }, {
    preserveState: true,
    replace: true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  statusFilter.value = '';
  applyFilters();
};

const deleteInvoice = (id) => {
  if (confirm('Are you sure you want to delete this invoice?')) {
    router.delete(route('crm.invoices.destroy', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'draft': return 'grey';
    case 'sent': return 'info';
    case 'paid': return 'success';
    case 'overdue': return 'error';
    case 'cancelled': return 'warning';
    default: return 'grey';
  }
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(amount);
};

watch([search, statusFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Invoices" />
  <DashboardLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div>
                <h1 class="text-h4 mb-1">Invoices</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Manage your billing and invoices.</p>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                :to="route('crm.invoices.create')"
              >
                New Invoice
              </v-btn>
            </v-card-text>
          </v-card>

          <v-card elevation="0">
            <v-card-text class="pa-4">
              <v-row>
                <v-col cols="12" sm="6" md="4">
                  <v-text-field
                    v-model="search"
                    prepend-inner-icon="mdi-magnify"
                    label="Search Invoice #..."
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    clearable
                  ></v-text-field>
                </v-col>
                <v-col cols="12" sm="6" md="3">
                  <v-select
                    v-model="statusFilter"
                    :items="statuses"
                    label="Status"
                    variant="outlined"
                    density="comfortable"
                    hide-details
                  ></v-select>
                </v-col>
                <v-col cols="12" sm="12" md="2" class="d-flex align-center">
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
                  <th class="text-left">Invoice #</th>
                  <th class="text-left">Client</th>
                  <th class="text-left">Issue Date</th>
                  <th class="text-left">Due Date</th>
                  <th class="text-left">Amount</th>
                  <th class="text-left">Status</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="7" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                  </td>
                </tr>
                <tr v-else-if="invoices.data.length === 0">
                  <td colspan="7" class="text-center py-10">
                    <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-file-document-outline</v-icon>
                    <p class="text-h6 text-medium-emphasis">No invoices found.</p>
                  </td>
                </tr>
                <tr v-for="invoice in invoices.data" :key="invoice.id">
                  <td class="py-4">
                    <Link :href="route('crm.invoices.show', invoice.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary">
                      {{ invoice.invoice_number }}
                    </Link>
                  </td>
                  <td>
                    <div class="d-flex flex-column">
                      <span class="text-body-2 font-weight-bold">{{ invoice.client.name }}</span>
                      <span v-if="invoice.project" class="text-caption text-medium-emphasis">{{ invoice.project.name }}</span>
                    </div>
                  </td>
                  <td>{{ new Date(invoice.issue_date).toLocaleDateString() }}</td>
                  <td>
                    <span :class="{'text-error': invoice.status !== 'paid' && new Date(invoice.due_date) < new Date()}">
                      {{ new Date(invoice.due_date).toLocaleDateString() }}
                    </span>
                  </td>
                  <td class="font-weight-bold">{{ formatCurrency(invoice.total_amount) }}</td>
                  <td>
                    <v-chip
                      :color="getStatusColor(invoice.status)"
                      size="x-small"
                      label
                      class="text-uppercase font-weight-bold"
                    >
                      {{ invoice.status }}
                    </v-chip>
                  </td>
                  <td class="text-right">
                    <v-menu>
                      <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                      </template>
                      <v-list density="compact">
                        <v-list-item :to="route('crm.invoices.show', invoice.id)" prepend-icon="mdi-eye">
                          <v-list-item-title>View</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="route('crm.invoices.edit', invoice.id)" prepend-icon="mdi-pencil">
                          <v-list-item-title>Edit</v-list-item-title>
                        </v-list-item>
                        <v-divider></v-divider>
                        <v-list-item @click="deleteInvoice(invoice.id)" prepend-icon="mdi-delete" color="error">
                          <v-list-item-title>Delete</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-divider></v-divider>

            <v-card-text v-if="invoices.links.length > 3" class="pa-4 d-flex justify-center">
              <v-pagination
                v-model="invoices.current_page"
                :length="invoices.last_page"
                @update:modelValue="router.get(route('crm.invoices.index', { page: $event }))"
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
