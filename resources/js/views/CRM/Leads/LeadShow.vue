<script setup lang="ts">
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import LeadConversionModal from '@/Components/CRM/LeadConversionModal.vue';
import ActivityTimeline from '@/components/CRM/ActivityTimeline.vue';
import EngagementTimeline from '@/components/CRM/EngagementTimeline.vue';
import EngagementForm from '@/components/CRM/EngagementForm.vue';

const props = defineProps({
  lead: {
    type: Object,
    required: true
  },
  dealStages: {
    type: Array,
    default: () => []
  }
});

const pageTitle = ref('Lead Details');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: false, href: '/crm/leads' },
  { title: 'Details', disabled: true, href: '#' }
]);

const showConversionModal = ref(false);
const showEngagementModal = ref(false);

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    new: 'info',
    contacted: 'warning',
    qualified: 'purple',
    converted: 'success',
    lost: 'error'
  };
  return colors[status] || 'default';
};

const editLead = () => {
  router.visit(route('crm.leads.edit', props.lead.id));
};

const handleConverted = () => {
  showConversionModal.value = false;
  // Redirect happens from backend
};
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />

  <v-row>
    <v-col cols="12" md="8">
      <v-card elevation="10" class="mb-6">
        <v-card-item class="pa-6">
          <div class="d-flex align-center justify-space-between mb-6">
            <div class="d-flex align-center">
              <v-avatar size="64" color="lightprimary" class="mr-4">
                <span class="text-h4 text-primary">{{ lead.first_name[0] }}{{ lead.last_name[0] }}</span>
              </v-avatar>
              <div>
                <h2 class="text-h4 font-weight-bold mb-1">{{ lead.full_name }}</h2>
                <div class="text-subtitle-1 text-medium-emphasis">{{ lead.job_title }} at {{ lead.company_name || 'N/A' }}</div>
              </div>
            </div>
            <div class="d-flex gap-2">
              <v-btn color="primary" variant="outlined" @click="editLead">
                <v-icon left class="mr-1">mdi-pencil</v-icon>
                Edit
              </v-btn>
              <v-btn 
                v-if="lead.status === 'qualified'" 
                color="success" 
                @click="showConversionModal = true"
              >
                <v-icon left class="mr-1">mdi-account-convert</v-icon>
                Convert to Client
              </v-btn>
            </div>
          </div>

          <v-divider class="mb-6"></v-divider>

          <v-row>
            <v-col cols="12" md="6">
              <h3 class="text-subtitle-1 font-weight-bold mb-4">Contact Information</h3>
              <div class="d-flex align-center mb-4">
                <v-icon color="primary" class="mr-3">mdi-email-outline</v-icon>
                <div>
                  <div class="text-caption text-medium-emphasis">Email</div>
                  <div class="text-body-1">{{ lead.email || 'N/A' }}</div>
                </div>
              </div>
              <div class="d-flex align-center mb-4">
                <v-icon color="primary" class="mr-3">mdi-phone-outline</v-icon>
                <div>
                  <div class="text-caption text-medium-emphasis">Phone</div>
                  <div class="text-body-1">{{ lead.phone || 'N/A' }}</div>
                </div>
              </div>
              <div class="d-flex align-center mb-4">
                <v-icon color="primary" class="mr-3">mdi-cellphone</v-icon>
                <div>
                  <div class="text-caption text-medium-emphasis">Mobile</div>
                  <div class="text-body-1">{{ lead.mobile || 'N/A' }}</div>
                </div>
              </div>
            </v-col>
            <v-col cols="12" md="6">
              <h3 class="text-subtitle-1 font-weight-bold mb-4">Address</h3>
              <div class="d-flex align-start mb-4">
                <v-icon color="primary" class="mr-3 mt-1">mdi-map-marker-outline</v-icon>
                <div>
                  <div class="text-body-1">
                    {{ lead.address_line1 }}<br v-if="lead.address_line1">
                    {{ lead.address_line2 }}<br v-if="lead.address_line2">
                    {{ lead.city }}{{ lead.state ? ', ' + lead.state : '' }} {{ lead.postal_code }}<br>
                    {{ lead.country }}
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>
        </v-card-item>
      </v-card>

      <UiParentCard title="Notes" class="mb-6">
        <div class="text-body-1 white-space-pre-line">
          {{ lead.notes || 'No notes available for this lead.' }}
        </div>
      </UiParentCard>

      <UiParentCard title="Recent Activity" class="mb-6">
        <ActivityTimeline :activities="lead.activities" />
      </UiParentCard>

      <UiParentCard title="Engagement Timeline">
        <template v-slot:append>
          <v-btn
            color="warning"
            size="small"
            variant="flat"
            prepend-icon="mdi-star-plus-outline"
            @click="showEngagementModal = true"
          >
            Log Engagement
          </v-btn>
        </template>
        <EngagementTimeline :engagements="lead.engagements" />
      </UiParentCard>
    </v-col>

    <v-col cols="12" md="4">
      <UiParentCard title="Status & Score" class="mb-6">
        <div class="mb-6">
          <div class="text-caption text-medium-emphasis mb-1">Current Status</div>
          <v-chip :color="getStatusColor(lead.status)" label class="text-uppercase">
            {{ lead.status }}
          </v-chip>
        </div>
        <div class="mb-6">
          <div class="text-caption text-medium-emphasis mb-1">Lead Score</div>
          <div class="d-flex align-center">
            <v-progress-linear
              :model-value="lead.score"
              :color="lead.score >= 70 ? 'success' : lead.score >= 40 ? 'warning' : 'error'"
              height="8"
              rounded
              class="mr-4"
            ></v-progress-linear>
            <span class="text-h6 font-weight-bold">{{ lead.score }}%</span>
          </div>
        </div>
        <v-divider class="mb-6"></v-divider>
        <div class="mb-4">
          <div class="text-caption text-medium-emphasis">Lead Source</div>
          <div class="text-body-1 font-weight-medium">{{ lead.lead_source?.name || 'Unknown' }}</div>
        </div>
        <div>
          <div class="text-caption text-medium-emphasis">Lead Owner</div>
          <div class="d-flex align-center mt-1">
            <v-avatar size="24" color="primary" class="mr-2">
              <span class="text-caption text-white">{{ lead.owner?.name[0] }}</span>
            </v-avatar>
            <span class="text-body-2">{{ lead.owner?.name }}</span>
          </div>
        </div>
      </UiParentCard>

      <UiParentCard title="System Information">
        <div class="mb-4">
          <div class="text-caption text-medium-emphasis">Created By</div>
          <div class="text-body-2">{{ lead.creator?.name || 'System' }}</div>
        </div>
        <div class="mb-4">
          <div class="text-caption text-medium-emphasis">Created At</div>
          <div class="text-body-2">{{ new Date(lead.created_at).toLocaleString() }}</div>
        </div>
        <div>
          <div class="text-caption text-medium-emphasis">Last Updated</div>
          <div class="text-body-2">{{ new Date(lead.updated_at).toLocaleString() }}</div>
        </div>
      </UiParentCard>
    </v-col>
  </v-row>

  <LeadConversionModal
    v-if="lead"
    :show="showConversionModal"
    :lead="lead"
    :deal-stages="dealStages"
    @close="showConversionModal = false"
    @converted="handleConverted"
  />

  <!-- Log Engagement Modal -->
  <v-dialog v-model="showEngagementModal" max-width="700px">
    <v-card>
      <v-card-title class="pa-4 bg-warning text-white d-flex justify-space-between align-center">
        <span>Log Engagement for {{ lead.full_name }}</span>
        <v-btn icon="mdi-close" variant="text" size="small" @click="showEngagementModal = false"></v-btn>
      </v-card-title>
      <v-card-text class="pa-4 pt-6">
        <EngagementForm 
          engageable-type="App\Models\Lead" 
          :engageable-id="lead.id"
          @success="showEngagementModal = false"
        />
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.gap-2 { gap: 8px; }
.white-space-pre-line { white-space: pre-line; }
</style>
