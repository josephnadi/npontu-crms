<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  contacts: Object,
  filters: Object,
  clients: Array,
});

const search = ref(props.filters.search || '');
const clientIdFilter = ref(props.filters.client_id || '');
const loading = ref(false);
const showConversionDialog = ref(false);
const converting = ref(false);
const selectedContact = ref(null);

watch([search, clientIdFilter], debounce(() => {
  loading.value = true;
  router.get(route('crm.contacts.index'), { 
    search: search.value, 
    client_id: clientIdFilter.value 
  }, {
    preserveState: true,
    replace: true,
    preserveScroll: true,
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false
  });
}, 500));

const deleteContact = (id) => {
  if (confirm('Are you sure you want to delete this contact?')) {
    router.delete(route('crm.contacts.destroy', id));
  }
};

const confirmConvertToLead = (contact) => {
  selectedContact.value = contact;
  showConversionDialog.value = true;
};

const convertToLead = () => {
  if (!selectedContact.value) return;
  converting.value = true;
  router.post(route('crm.contacts.convert', selectedContact.value.id), {}, {
    onFinish: () => {
      converting.value = false;
      showConversionDialog.value = false;
      selectedContact.value = null;
    }
  });
};

const convertToTicket = (id) => {
  if (confirm('Create a support ticket for this contact?')) {
    router.post(route('crm.contacts.convert-to-ticket', id));
  }
};
</script>

<template>
  <Head title="Contacts" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h3 font-weight-bold">Contacts</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Manage your customer relationships</p>
        </div>
        <Link :href="route('crm.contacts.create')">
          <v-btn color="primary" prepend-icon="mdi-plus">Add Contact</v-btn>
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
                  label="Search contacts..."
                  variant="outlined"
                  density="comfortable"
                  hide-details
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="3">
                <v-select
                  v-model="clientIdFilter"
                  :items="clients"
                  item-title="name"
                  item-value="id"
                  label="Filter by Client"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  clearable
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
                <th class="text-left font-weight-bold">Contact Name</th>
                <th class="text-left font-weight-bold">Client / Company</th>
                <th class="text-left font-weight-bold">Email</th>
                <th class="text-left font-weight-bold">Phone</th>
                <th class="text-left font-weight-bold">Status</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="contact in contacts.data" :key="contact.id">
                <td>
                  <Link :href="route('crm.contacts.show', contact.id)" class="text-decoration-none font-weight-bold text-primary">
                    {{ contact.first_name }} {{ contact.last_name }}
                  </Link>
                  <div class="text-caption text-medium-emphasis">{{ contact.job_title || 'N/A' }}</div>
                </td>
                <td>
                  <Link v-if="contact.client" :href="route('crm.clients.show', contact.client.id)" class="text-decoration-none">
                    {{ contact.client.name }}
                  </Link>
                  <span v-else class="text-medium-emphasis">N/A</span>
                </td>
                <td>{{ contact.email || 'N/A' }}</td>
                <td>{{ contact.phone || 'N/A' }}</td>
                <td>
                  <v-chip :color="contact.status === 'active' ? 'success' : 'grey'" size="x-small" variant="flat" class="text-capitalize">
                    {{ contact.status }}
                  </v-chip>
                </td>
                <td class="text-right">
                  <div class="d-flex justify-end gap-1">
                    <Link :href="route('crm.contacts.show', contact.id)" class="text-decoration-none">
                      <v-tooltip text="View Details">
                        <template v-slot:activator="{ props }">
                          <v-btn size="x-small" icon color="info" v-bind="props">
                            <v-icon size="small">mdi-eye</v-icon>
                          </v-btn>
                        </template>
                      </v-tooltip>
                    </Link>
                    <Link :href="route('crm.contacts.edit', contact.id)" class="text-decoration-none">
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
                        <v-btn size="x-small" icon color="warning" @click="confirmConvertToLead(contact)" v-bind="props">
                          <v-icon size="small">mdi-account-convert-outline</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                    <v-tooltip text="Convert to Ticket">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="info" @click="convertToTicket(contact.id)" v-bind="props">
                          <v-icon size="small">mdi-ticket-outline</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                    <v-tooltip text="Delete">
                      <template v-slot:activator="{ props }">
                        <v-btn size="x-small" icon color="error" @click="deleteContact(contact.id)" v-bind="props">
                          <v-icon size="small">mdi-delete</v-icon>
                        </v-btn>
                      </template>
                    </v-tooltip>
                  </div>
                </td>
              </tr>
              <tr v-if="contacts.data.length === 0">
                <td colspan="6" class="text-center py-8 text-medium-emphasis">
                  No contacts found. Click "Add Contact" to get started.
                </td>
              </tr>
            </tbody>
          </v-table>

          <v-divider></v-divider>

          <div class="pa-4 d-flex justify-center">
            <v-pagination
              v-model="contacts.current_page"
              :length="contacts.last_page"
              @update:modelValue="router.get(route('crm.contacts.index'), { page: $event, search: search, client_id: clientIdFilter }, { preserveState: true })"
              size="small"
            ></v-pagination>
          </div>
        </v-card>
      </v-col>
    </v-row>

    <!-- Conversion Confirmation Dialog -->
    <v-dialog v-model="showConversionDialog" max-width="500px">
      <v-card v-if="selectedContact">
        <v-card-title class="pa-4 bg-warning text-white">
          Convert {{ selectedContact.first_name }} {{ selectedContact.last_name }} to Lead?
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          This will archive the contact and create a new lead from its information. Activities and engagements will be migrated to the new lead.
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
