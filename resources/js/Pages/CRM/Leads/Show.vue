<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import { ref } from 'vue';

const props = defineProps({
  lead: Object,
  dealStages: Array,
});

const showConvertModal = ref(false);

const convertForm = useForm({
  create_client: true,
  create_deal: true,
  deal_title: `${props.lead.company_name || props.lead.first_name} Deal`,
  deal_value: 0,
  deal_stage_id: props.dealStages[0]?.id || null,
});

const submitConvert = () => {
  convertForm.post(route('crm.leads.convert', props.lead.id), {
    onSuccess: () => {
      showConvertModal.value = false;
    }
  });
};

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
</script>

<template>
  <Head :title="'Lead Details - ' + lead.first_name" />

  <DashboardLayout>
    <v-row>
      <!-- Header -->
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div class="d-flex align-center">
          <Link :href="route('crm.leads.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <div>
            <h2 class="text-h3 font-weight-bold">{{ lead.first_name }} {{ lead.last_name }}</h2>
            <div class="d-flex align-center text-subtitle-1 text-medium-emphasis">
              <v-chip :color="getStatusColor(lead.status)" size="small" variant="flat" class="text-capitalize mr-3">
                {{ lead.status }}
              </v-chip>
              <v-icon size="small" class="mr-1">mdi-domain</v-icon>
              {{ lead.company_name || 'Individual' }}
            </div>
          </div>
        </div>
        <div class="d-flex gap-2">
          <v-btn 
            v-if="lead.status !== 'converted'"
            color="success" 
            prepend-icon="mdi-account-convert"
            @click="showConvertModal = true"
            class="mr-2"
          >
            Convert to Contact
          </v-btn>
          <Link :href="route('crm.leads.edit', lead.id)">
            <v-btn variant="outlined" color="primary" prepend-icon="mdi-pencil">Edit Lead</v-btn>
          </Link>
        </div>
      </v-col>

      <!-- Sidebar Info -->
      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Lead Information"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="mb-4 text-center">
              <div class="text-caption text-medium-emphasis mb-1">Lead Score</div>
              <v-progress-circular
                :model-value="lead.score"
                :color="lead.score > 70 ? 'success' : (lead.score > 40 ? 'warning' : 'error')"
                size="80"
                width="8"
              >
                <span class="text-h6 font-weight-bold">{{ lead.score }}%</span>
              </v-progress-circular>
            </div>

            <v-divider class="mb-4"></v-divider>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Email</div>
              <div class="text-body-2">{{ lead.email || 'N/A' }}</div>
            </div>
            
            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Phone</div>
              <div class="text-body-2">{{ lead.phone || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Job Title</div>
              <div class="text-body-2">{{ lead.job_title || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Source</div>
              <div class="text-body-2">{{ lead.source || 'N/A' }}</div>
            </div>

            <div class="mb-4">
              <div class="text-caption text-medium-emphasis mb-1">Created At</div>
              <div class="text-body-2">{{ new Date(lead.created_at).toLocaleDateString() }}</div>
            </div>

            <div class="mb-0">
              <div class="text-caption text-medium-emphasis mb-1">Owner</div>
              <div class="d-flex align-center">
                <v-avatar size="24" color="primary" class="mr-2">
                  <span class="text-caption text-white">{{ lead.owner?.name.charAt(0) }}</span>
                </v-avatar>
                <span class="text-body-2">{{ lead.owner?.name }}</span>
              </div>
            </div>
          </v-card-text>
        </v-card>

        <v-card elevation="0" border>
          <v-card-item title="Internal Notes"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <p class="text-body-2" style="white-space: pre-wrap;">{{ lead.notes || 'No notes available for this lead.' }}</p>
          </v-card-text>
        </v-card>
      </v-col>

      <!-- Main Content -->
      <v-col cols="12" md="8">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Activity Timeline"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-8">
            <!-- Timeline will go here - reuse ActivityTimeline if available -->
            <div class="text-center text-medium-emphasis">
              <v-icon size="48" class="mb-2">mdi-history</v-icon>
              <p>No recent activities for this lead.</p>
              <v-btn variant="text" color="primary" prepend-icon="mdi-plus">Log Activity</v-btn>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Convert Lead Modal -->
    <v-dialog v-model="showConvertModal" max-width="600px">
      <v-card>
        <v-card-title class="pa-4 bg-primary text-white">
          Convert Lead to Contact
        </v-card-title>
        <v-card-text class="pa-4 pt-6">
          <p class="mb-4">Converting this lead will create a new <strong>Contact</strong>. You can also optionally create a Client and a Deal.</p>
          
          <v-checkbox
            v-model="convertForm.create_client"
            label="Create new Client from company"
            hide-details
            class="mb-2"
            :disabled="!lead.company_name"
          ></v-checkbox>
          <v-checkbox
            v-model="convertForm.create_deal"
            label="Create a new Deal for this contact"
            hide-details
            class="mb-4"
          ></v-checkbox>

          <v-expand-transition>
            <div v-if="convertForm.create_deal">
              <v-text-field
                v-model="convertForm.deal_title"
                label="Deal Title"
                variant="outlined"
                class="mb-4"
              ></v-text-field>
              <v-row>
                <v-col cols="6">
                  <v-text-field
                    v-model="convertForm.deal_value"
                    label="Deal Value"
                    type="number"
                    prefix="$"
                    variant="outlined"
                  ></v-text-field>
                </v-col>
                <v-col cols="6">
                  <v-select
                    v-model="convertForm.deal_stage_id"
                    :items="dealStages"
                    item-title="name"
                    item-value="id"
                    label="Initial Stage"
                    variant="outlined"
                  ></v-select>
                </v-col>
              </v-row>
            </div>
          </v-expand-transition>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions class="pa-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" @click="showConvertModal = false">Cancel</v-btn>
          <v-btn 
            color="success" 
            variant="flat" 
            :loading="convertForm.processing"
            @click="submitConvert"
          >
            Confirm Conversion
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </DashboardLayout>
</template>
