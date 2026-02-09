<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  client: Object,
});
</script>

<template>
  <Head :title="'Client Details - ' + client.name" />

  <DashboardLayout>
    <v-row>
      <!-- Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div class="d-flex align-center">
          <Link :href="route('crm.clients.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <div>
            <h2 class="text-h3 font-weight-bold">{{ client.name }}</h2>
            <div class="d-flex align-center text-subtitle-1 text-medium-emphasis">
              <v-icon size="small" class="mr-1">mdi-factory</v-icon>
              {{ client.industry || 'No Industry' }}
              <v-divider vertical class="mx-3"></v-divider>
              <v-icon size="small" class="mr-1">mdi-map-marker</v-icon>
              {{ client.city || 'No Location' }}
            </div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <Link :href="route('crm.clients.edit', client.id)">
            <v-btn variant="outlined" color="primary" prepend-icon="mdi-pencil">Edit Client</v-btn>
          </Link>
        </div>
      </v-col>

      <!-- Sidebar Info -->
      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Client Information"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Website</div>
              <a v-if="client.website" :href="client.website" target="_blank" class="text-body-2 text-primary text-decoration-none d-flex align-center">
                {{ client.website }}
                <v-icon size="x-small" class="ml-1">mdi-open-in-new</v-icon>
              </a>
              <span v-else class="text-body-2">N/A</span>
            </div>
            
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Phone</div>
              <div class="text-body-2">{{ client.phone || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Full Address</div>
              <div class="text-body-2">
                {{ client.address }}<br v-if="client.address">
                {{ client.city }}<span v-if="client.state">, {{ client.state }}</span> {{ client.postal_code }}<br v-if="client.city">
                {{ client.country }}
                <span v-if="!client.address && !client.city">N/A</span>
              </div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Owner</div>
              <div class="d-flex align-center">
                <v-avatar size="24" color="primary" class="mr-2">
                  <span class="text-caption text-white">{{ client.owner?.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ client.owner?.name }}</span>
              </div>
            </div>
          </v-card-text>
        </v-card>

        <v-card elevation="0" border>
          <v-card-item title="Notes"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <p class="text-body-2" style="white-space: pre-wrap;">{{ client.notes || 'No notes available for this client.' }}</p>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Main Content: Contacts -->
      <v-col cols="12" md="8">
        <v-card elevation="0" border>
          <v-card-item title="Associated Contacts">
            <template v-slot:append>
              <v-btn color="primary" size="small" variant="text" prepend-icon="mdi-plus">Add Contact</v-btn>
            </template>
          </v-card-item>
          <v-divider></v-divider>
          <v-table>
            <thead>
              <tr>
                <th class="text-left font-weight-bold">Name</th>
                <th class="text-left font-weight-bold">Email</th>
                <th class="text-left font-weight-bold">Job Title</th>
                <th class="text-right font-weight-bold">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="contact in client.contacts" :key="contact.id">
                <td>
                  <div class="d-flex align-center py-2">
                    <v-avatar color="grey-lighten-3" size="32" class="mr-3">
                      <span class="text-caption font-weight-bold">{{ contact.first_name.charAt(0) }}{{ contact.last_name.charAt(0) }}</span>
                    </v-avatar>
                    <span class="font-weight-medium">{{ contact.first_name }} {{ contact.last_name }}</span>
                  </div>
                </td>
                <td>{{ contact.email || 'N/A' }}</td>
                <td>{{ contact.job_title || 'N/A' }}</td>
                <td class="text-right">
                  <v-btn icon="mdi-pencil" variant="text" size="small"></v-btn>
                  <v-btn icon="mdi-email-outline" variant="text" size="small"></v-btn>
                </td>
              </tr>
              <tr v-if="client.contacts.length === 0">
                <td colspan="4" class="text-center py-8 text-medium-emphasis">
                  No contacts found for this client.
                </td>
              </tr>
            </tbody>
          </v-table>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
