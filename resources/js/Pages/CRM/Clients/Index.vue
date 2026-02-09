<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';

const props = defineProps({
  clients: Object,
  filters: Object,
});

const search = ref(props.filters.search);

watch(search, debounce((value) => {
  router.get(route('crm.clients.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 300));

const deleteClient = (id) => {
  if (confirm('Are you sure you want to delete this client?')) {
    router.delete(route('crm.clients.destroy', id));
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
                  <v-menu>
                    <template v-slot:activator="{ props }">
                      <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                    </template>
                    <v-list size="small">
                      <v-list-item @click="router.get(route('crm.clients.show', client.id))" prepend-icon="mdi-eye">
                        <v-list-item-title>View</v-list-item-title>
                      </v-list-item>
                      <v-list-item @click="router.get(route('crm.clients.edit', client.id))" prepend-icon="mdi-pencil">
                        <v-list-item-title>Edit</v-list-item-title>
                      </v-list-item>
                      <v-divider></v-divider>
                      <v-list-item @click="deleteClient(client.id)" prepend-icon="mdi-delete" color="error">
                        <v-list-item-title>Delete</v-list-item-title>
                      </v-list-item>
                    </v-list>
                  </v-menu>
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
  </DashboardLayout>
</template>
