<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  tickets: Object,
  filters: Object,
});

const search = ref(props.filters?.search || '');
const statusFilter = ref(props.filters?.status || '');
const priorityFilter = ref(props.filters?.priority || '');
const loading = ref(false);

const statuses = [
  { title: 'All Statuses', value: '' },
  { title: 'Open', value: 'open' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Pending', value: 'pending' },
  { title: 'Resolved', value: 'resolved' },
  { title: 'Closed', value: 'closed' },
];

const priorities = [
  { title: 'All Priorities', value: '' },
  { title: 'Low', value: 'low' },
  { title: 'Medium', value: 'medium' },
  { title: 'High', value: 'high' },
  { title: 'Urgent', value: 'urgent' },
];

const applyFilters = () => {
  loading.value = true;
  router.get(route('crm.tickets.index'), { 
    search: search.value, 
    status: statusFilter.value,
    priority: priorityFilter.value
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
};

const clearFilters = () => {
  search.value = '';
  statusFilter.value = '';
  priorityFilter.value = '';
  applyFilters();
};

const deleteTicket = (id) => {
  if (confirm('Are you sure you want to delete this ticket?')) {
    router.delete(route('crm.tickets.destroy', id));
  }
};

const convertToLead = (id) => {
  if (confirm('Convert this ticket to a Lead?')) {
    router.post(route('crm.tickets.convert-to-lead', id));
  }
};

const convertToDeal = (id) => {
  if (confirm('Convert this ticket to a Deal?')) {
    router.post(route('crm.tickets.convert-to-deal', id));
  }
};

const getStatusColor = (status) => {
  switch (status) {
    case 'open': return 'primary';
    case 'in_progress': return 'info';
    case 'pending': return 'warning';
    case 'resolved': return 'success';
    case 'closed': return 'grey';
    default: return 'grey';
  }
};

const getPriorityColor = (priority) => {
  switch (priority) {
    case 'low': return 'success';
    case 'medium': return 'warning';
    case 'high': return 'error';
    case 'urgent': return 'deep-orange';
    default: return 'grey';
  }
};

watch([search, statusFilter, priorityFilter], debounce(() => {
  applyFilters();
}, 500));
</script>

<template>
  <Head title="Support Tickets" />
  <DashboardLayout>
    <v-container fluid>
      <v-row>
        <v-col cols="12">
          <v-card elevation="0" class="mb-6">
            <v-card-text class="d-flex align-center justify-space-between py-4">
              <div>
                <h1 class="text-h4 mb-1">Support Tickets</h1>
                <p class="text-subtitle-1 text-medium-emphasis">Manage customer support requests and issues.</p>
              </div>
              <v-btn
                color="primary"
                prepend-icon="mdi-plus"
                :to="route('crm.tickets.create')"
              >
                New Ticket
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
                    label="Search tickets..."
                    variant="outlined"
                    density="comfortable"
                    hide-details
                    clearable
                  ></v-text-field>
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
                <v-col cols="12" sm="3">
                  <v-select
                    v-model="priorityFilter"
                    :items="priorities"
                    label="Priority"
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
                  <th class="text-left">Ticket #</th>
                  <th class="text-left">Subject</th>
                  <th class="text-left">Client</th>
                  <th class="text-left">Status</th>
                  <th class="text-left">Priority</th>
                  <th class="text-left">Category</th>
                  <th class="text-left">Created At</th>
                  <th class="text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading">
                  <td colspan="8" class="text-center py-10">
                    <v-progress-circular indeterminate color="primary"></v-progress-circular>
                  </td>
                </tr>
                <tr v-else-if="tickets.data.length === 0">
                  <td colspan="8" class="text-center py-10">
                    <v-icon size="64" color="medium-emphasis" class="mb-4">mdi-ticket-outline</v-icon>
                    <p class="text-h6 text-medium-emphasis">No tickets found.</p>
                  </td>
                </tr>
                <tr v-for="ticket in tickets.data" :key="ticket.id">
                  <td>
                    <span class="text-subtitle-2 font-weight-bold">{{ ticket.ticket_number }}</span>
                  </td>
                  <td class="py-4">
                    <div class="d-flex flex-column">
                      <Link :href="route('crm.tickets.show', ticket.id)" class="text-subtitle-1 font-weight-bold text-decoration-none text-primary">
                        {{ ticket.subject }}
                      </Link>
                      <span class="text-caption text-medium-emphasis text-truncate" style="max-width: 250px;">
                        {{ ticket.description }}
                      </span>
                    </div>
                  </td>
                  <td>
                    <Link v-if="ticket.client" :href="route('crm.clients.show', ticket.client.id)" class="text-body-2 text-decoration-none text-secondary">
                      {{ ticket.client.name }}
                    </Link>
                    <span v-else class="text-medium-emphasis">N/A</span>
                  </td>
                  <td>
                    <v-chip
                      :color="getStatusColor(ticket.status)"
                      size="x-small"
                      label
                      class="text-uppercase font-weight-bold"
                    >
                      {{ ticket.status.replace('_', ' ') }}
                    </v-chip>
                  </td>
                  <td>
                    <v-chip
                      :color="getPriorityColor(ticket.priority)"
                      size="x-small"
                      variant="outlined"
                      class="text-uppercase font-weight-bold"
                    >
                      {{ ticket.priority }}
                    </v-chip>
                  </td>
                  <td>
                    <v-chip size="x-small" variant="tonal" class="text-uppercase">
                      {{ ticket.category }}
                    </v-chip>
                  </td>
                  <td>
                    <span class="text-body-2">
                      {{ new Date(ticket.created_at).toLocaleDateString() }}
                    </span>
                  </td>
                  <td class="text-right">
                    <v-menu>
                      <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-pencil" size="small" v-bind="props"></v-btn>
                      </template>
                      <v-list>
                        <v-list-item :to="route('crm.tickets.show', ticket.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-eye</v-icon>
                          </template>
                          <v-list-item-title>View Details</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="route('crm.tickets.edit', ticket.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-pencil</v-icon>
                          </template>
                          <v-list-item-title>Edit Ticket</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="convertToLead(ticket.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-account-convert-outline</v-icon>
                          </template>
                          <v-list-item-title>Convert to Lead</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="convertToDeal(ticket.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-currency-usd</v-icon>
                          </template>
                          <v-list-item-title>Convert to Deal</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="deleteTicket(ticket.id)">
                          <template v-slot:prepend>
                            <v-icon>mdi-delete</v-icon>
                          </template>
                          <v-list-item-title>Delete Ticket</v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </tbody>
            </v-table>

            <v-divider></v-divider>

            <div class="pa-4 d-flex justify-center">
              <v-pagination
                v-model="tickets.current_page"
                :length="tickets.last_page"
            @update:modelValue="router.get(route('crm.tickets.index'), { page: $event, search, status: statusFilter, priority: priorityFilter }, { preserveState: true, preserveScroll: true })"
            size="small"
              ></v-pagination>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>