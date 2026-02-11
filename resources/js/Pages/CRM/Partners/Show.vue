<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  partner: Object,
});

const tab = ref('details');
const convertingToLead = ref(false);
const convertingToTicket = ref(false);

const convertToLead = () => {
  if (confirm(`Convert ${props.partner.name} to a Lead?`)) {
    convertingToLead.value = true;
    router.post(route('crm.partners.convert-to-lead', props.partner.id), {}, {
      onFinish: () => convertingToLead.value = false
    });
  }
};

const convertToTicket = () => {
  if (confirm(`Create a support ticket for ${props.partner.name}?`)) {
    convertingToTicket.value = true;
    router.post(route('crm.partners.convert-to-ticket', props.partner.id), {}, {
      onFinish: () => convertingToTicket.value = false
    });
  }
};

const deletePartner = () => {
  if (confirm('Are you sure you want to delete this partner?')) {
    router.delete(route('crm.partners.destroy', props.partner.id));
  }
};
</script>

<template>
  <Head :title="'Partner Details - ' + partner.name" />

  <DashboardLayout>
    <v-row>
      <!-- Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div class="d-flex align-center">
          <Link :href="route('crm.partners.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <div>
            <h2 class="text-h3 font-weight-bold">{{ partner.name }}</h2>
            <div class="d-flex align-center text-subtitle-1 text-medium-emphasis">
              <v-icon size="small" class="mr-1">mdi-handshake-outline</v-icon>
              {{ partner.type }}
              <v-divider vertical class="mx-3"></v-divider>
              <v-icon size="small" class="mr-1" :color="partner.status === 'Active' ? 'success' : 'warning'">mdi-circle-medium</v-icon>
              {{ partner.status }}
            </div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <v-btn 
            variant="outlined" 
            color="warning" 
            @click="convertToLead" 
            :loading="convertingToLead"
            title="Convert to Lead"
          >
            <v-icon>mdi-account-convert-outline</v-icon>
          </v-btn>
          <v-btn 
            variant="outlined" 
            color="info" 
            @click="convertToTicket" 
            :loading="convertingToTicket"
            title="Convert to Ticket"
          >
            <v-icon>mdi-ticket-outline</v-icon>
          </v-btn>
          <Link :href="route('crm.partners.edit', partner.id)">
            <v-btn variant="outlined" color="primary" title="Edit Partner">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </Link>
          <v-btn variant="outlined" color="error" @click="deletePartner" title="Delete Partner">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </div>
      </v-col>

      <!-- Sidebar Info -->
      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Partner Information"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Email</div>
              <div class="text-body-2">{{ partner.email || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Phone</div>
              <div class="text-body-2">{{ partner.phone || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Website</div>
              <a v-if="partner.website" :href="partner.website" target="_blank" class="text-body-2 text-primary text-decoration-none d-flex align-center">
                {{ partner.website }}
                <v-icon size="x-small" class="ml-1">mdi-open-in-new</v-icon>
              </a>
              <span v-else class="text-body-2">N/A</span>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Commission Rate</div>
              <div class="text-body-2">{{ partner.commission_rate }}%</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Account Manager</div>
              <div class="d-flex align-center">
                <v-avatar size="24" color="primary" class="mr-2">
                  <span class="text-caption text-white">{{ partner.account_manager?.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ partner.account_manager?.name }}</span>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Main Content -->
      <v-col cols="12" md="8">
        <v-tabs v-model="tab" color="primary">
          <v-tab value="details">Details</v-tab>
          <v-tab value="deals">Deals ({{ partner.deals?.length || 0 }})</v-tab>
        </v-tabs>

        <v-window v-model="tab" class="mt-4">
          <!-- Details Tab -->
          <v-window-item value="details">
            <v-card elevation="0" border>
              <v-card-item title="Description"></v-card-item>
              <v-divider></v-divider>
              <v-card-text class="pa-4">
                <p class="text-body-2" style="white-space: pre-wrap;">{{ partner.description || 'No description available for this partner.' }}</p>
              </v-card-text>
            </v-card>
          </v-window-item>

          <!-- Deals Tab -->
          <v-window-item value="deals">
            <v-card elevation="0" border>
              <v-table v-if="partner.deals?.length > 0">
                <thead>
                  <tr>
                    <th class="text-left font-weight-bold">Title</th>
                    <th class="text-left font-weight-bold">Value</th>
                    <th class="text-left font-weight-bold">Stage</th>
                    <th class="text-right font-weight-bold">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="deal in partner.deals" :key="deal.id">
                    <td>{{ deal.title }}</td>
                    <td>GH₵ {{ deal.value?.toLocaleString() }}</td>
                    <td>
                      <v-chip size="x-small" color="primary" variant="tonal">{{ deal.stage?.name || 'Unknown' }}</v-chip>
                    </td>
                    <td class="text-right">
                      <Link :href="route('crm.deals.show', deal.id)">
                        <v-btn icon="mdi-eye" variant="text" size="small" title="View Deal"></v-btn>
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </v-table>
              <div v-else class="text-center py-8 text-medium-emphasis">
                No deals associated with this partner.
              </div>
            </v-card>
          </v-window-item>
        </v-window>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
