<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';



const props = defineProps({
  clients: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const loading = ref(false);
const showConversionDialog = ref(false);
const converting = ref(false);
const selectedClient = ref(null);

watch(search, debounce((value) => {
  router.get(route('crm.clients.index'), { search: value }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
}, 500));

const deleteClient = (id) => {
  if (confirm('Are you sure you want to delete this client?')) {
    router.delete(route('crm.clients.destroy', id));
  }
};

const confirmConvertToLead = (client) => {
  selectedClient.value = client;
  showConversionDialog.value = true;
};

const convertToLead = () => {
  if (!selectedClient.value) return;
  converting.value = true;
  router.post(route('crm.clients.convert', selectedClient.value.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConversionDialog.value = false;
      selectedClient.value = null;
    }
  });
};

const convertToPartner = (id) => {
  if (confirm('Convert this client to a Partner?')) {
    router.post(route('crm.clients.convert-to-partner', id));
  }
};

const convertToTicket = (id) => {
  if (confirm('Create a support ticket for this client?')) {
    router.post(route('crm.clients.convert-to-ticket', id));
  }
};
</script>

<template>
  <Head title="Clients" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h3 font-weight-bold">Clients</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Manage your customer companies and organizations</p>
        </div>
        <Link :href="route('crm.clients.create')">
          <v-btn color="primary" prepend-icon="mdi-plus">Add Client</v-btn>
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
                  label="Search clients..."
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-text-field>
              </v-col>
            </v-row>
          </v-card-text>

          <v-divider></v-divider>

          <v-table>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Name</th>
                <th class="text-left font-weight-bold">Industry</th>
                <th class="text-left font-weight-bold">Location</th>
                <th class="text-left font-weight-bold">Contacts</th>
                <th class="text-left font-weight-bold">Phone</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in clients.data" :key="client.id">
                <td>
                  <Link :href="route('crm.clients.show', client.id)" class="text-decoration-none font-weight-bold text-primary">
                    {{ client.name }}
                  </Link>
                </td>
                <td>{{ client.industry || 'N/A' }}</td>
                <td>
                  <span v-if="client.city">{{ client.city }}<span v-if="client.country">, {{ client.country }}</span></span>
                  <span v-else>N/A</span>
                </td>
                <td>
                  <v-chip size="small" color="info" variant="flat">
                    {{ client.contacts_count }} Contacts
                  </v-chip>
                </td>
                <td>{{ client.phone || 'N/A' }}</td>
                <td class="text-right">
                  <div class="d-flex justify-end gap-1">
                    <Link :href="route('crm.clients.show', client.id)" class="text-decoration-none">
                      <v-tooltip text="View Details">
                        <template v-slot:activator="{ props }">
                          <v-btn size="x-small" icon color="info" v-bind="props">
                            <v-icon size="small">mdi-eye</v-icon>
                          </v-btn>
                        </template>
                      </v-tooltip>
                    </Link>
                    <Link :href="route('crm.clients.edit', client.id)" class="text-decoration-none">
                      <v-tooltip text="Edit">
                        <template v-slot:activator="{ props }">
                          <v-btn size="x-small" icon color="primary" v-bind="props">
                            <v-icon size="small">mdi-pencil</v-icon>
                          </v-btn>
                        </template>
                      </v-tooltip>
                    </Link>
                    <v-tooltip text="Convert to Lead">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="warning" @click="confirmConvertToLead(client)" v-bind="props">
                          <v-icon size="small">mdi-account-convert-outline</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                    <v-tooltip text="Convert to Partner">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="primary" @click="convertToPartner(client.id)" v-bind="props">
                          <v-icon size="small">mdi-handshake-outline</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                    <v-tooltip text="Convert to Ticket">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="info" @click="convertToTicket(client.id)" v-bind="props">
                          <v-icon size="small">mdi-ticket-outline</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                    <v-tooltip text="Delete">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="error" @click="deleteClient(client.id)" v-bind="props">
                          <v-icon size="small">mdi-delete</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                  </div>
                </td>
              </tr>
              <tr v-if="clients.data.length === 0">
                <td colspan="6" class="text-center py-8 text-medium-emphasis">
                  No clients found. Click "Add Client" to get started.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>

          <div class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="clients.current_page"
              :length="clients.last_page"
              @update:modelValue="router.get(route('crm.clients.index'), { page: $event, search: search }, { preserveState: true })"
              size="small"
            ></v-pagination>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Conversion Confirmation Dialog -->
    <v-dialog v-model="showConversionDialog" max-width="500px">
      <v-card v-if="selectedClient">
        <v-card-title class="pa-4 bg-warning text-white">
          Convert {{ selectedClient.name }} to Lead?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          This will archive the client and its contacts, and create a new lead from the primary contact information. Activities and engagements will be migrated to the new lead.
        </v-card-text>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConversionDialog = false">Cancel</v-btn>
          <v-btn color="warning" variant="elevated" :loading="converting" @click="convertToLead">
            Confirm Conversion
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </DashboardLayout>
</template>
