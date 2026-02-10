<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import LeadConversionModal from '@/Components/CRM/LeadConversionModal.vue';

const props = defineProps({
  lead: {
    type: Object,
    default: () => ({})
  },
  dealStages: {
    type: Array,
    default: () => []
  }
});

console.log('LeadShow - Received lead:', props.lead);

const pageTitle = ref(props.lead?.full_name || 'Lead Details');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: false, href: '/leads' },
  { title: props.lead?.full_name || 'Lead', disabled: true, href: '#' }
]);

// Conversion modal state
const showConversionModal = ref(false);

// Status colors
const getStatusColor = (status) => {
  const colors = {
    new: 'info',
    contacted: 'warning',
    qualified: 'primary',
    converted: 'success',
    lost: 'error'
  };
  return colors[status] || 'default';
};

// Score color
const scoreColor = computed(() => {
  if (props.lead.score >= 70) return 'success';
  if (props.lead.score >= 40) return 'warning';
  return 'default';
});

// Check if lead can be converted
const canConvert = computed(() => props.lead?.status === 'qualified');

// Check if lead exists
const leadExists = computed(() => props.lead && props.lead.id);

// Navigation
const editLead = () => {
  router.visit(`/leads/${props.lead.id}/edit`);
};

const deleteLead = () => {
  if (confirm(`Are you sure you want to delete lead "${props.lead.full_name}"?`)) {
    router.delete(`/leads/${props.lead.id}`);
  }
};

const openConversionModal = () => {
  showConversionModal.value = true;
};

const handleConverted = () => {
  // The controller will redirect and show success message
};

const backToList = () => {
  router.visit('/leads');
};
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <!-- Error state -->
  <v-row v-if="!leadExists">
    <v-col cols="12">
      <UiParentCard title="Lead Not Found">
        <v-alert type="error">
          Lead could not be loaded. Please try again or contact support.
        </v-alert>
        <v-btn color="primary" @click="backToList" class="mt-4">
          Back to Leads List
        </v-btn>
      </UiParentCard>
    </v-col>
  </v-row>
  
  <v-row v-else>
    <!-- Main Lead Information -->
    <v-col cols="12" md="8">
      <UiParentCard title="Lead Details">
        <template v-slot:action>
          <div class="d-flex gap-2">
            <v-btn
              v-if="canConvert"
              color="success"
              @click="openConversionModal"
              prepend-icon="mdi-account-convert"
            >
              Convert Lead
            </v-btn>
            <v-btn
              color="primary"
              variant="outlined"
              @click="editLead"
              icon="mdi-pencil"
            />
            <v-btn
              color="error"
              variant="outlined"
              @click="deleteLead"
              icon="mdi-delete"
            />
          </div>
        </template>

        <!-- Personal Information -->
        <div class="mb-6">
          <h3 class="text-h6 mb-4">Personal Information</h3>
          <v-row>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">First Name</div>
                <div class="text-body-1">{{ lead.first_name }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Last Name</div>
                <div class="text-body-1">{{ lead.last_name }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Email</div>
                <div class="text-body-1">{{ lead.email || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Phone</div>
                <div class="text-body-1">{{ lead.phone || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Mobile</div>
                <div class="text-body-1">{{ lead.mobile || '-' }}</div>
              </div>
            </v-col>
          </v-row>
        </div>

        <v-divider class="my-6" />

        <!-- Company Information -->
        <div class="mb-6">
          <h3 class="text-h6 mb-4">Company Information</h3>
          <v-row>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Company Name</div>
                <div class="text-body-1">{{ lead.company_name || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Job Title</div>
                <div class="text-body-1">{{ lead.job_title || '-' }}</div>
              </div>
            </v-col>
          </v-row>
        </div>

        <v-divider class="my-6" />

        <!-- Address -->
        <div class="mb-6">
          <h3 class="text-h6 mb-4">Address</h3>
          <v-row>
            <v-col cols="12">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Street Address</div>
                <div class="text-body-1">
                  {{ lead.address?.line1 || '-' }}<br v-if="lead.address?.line2" />
                  {{ lead.address?.line2 }}
                </div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">City</div>
                <div class="text-body-1">{{ lead.address?.city || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">State/Province</div>
                <div class="text-body-1">{{ lead.address?.state || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Postal Code</div>
                <div class="text-body-1">{{ lead.address?.postal_code || '-' }}</div>
              </div>
            </v-col>
            <v-col cols="12" sm="6">
              <div class="mb-3">
                <div class="text-caption text-medium-emphasis">Country</div>
                <div class="text-body-1">{{ lead.address?.country || '-' }}</div>
              </div>
            </v-col>
          </v-row>
        </div>

        <v-divider class="my-6" />

        <!-- Notes -->
        <div v-if="lead.notes">
          <h3 class="text-h6 mb-4">Notes</h3>
          <div class="text-body-1">{{ lead.notes }}</div>
        </div>
      </UiParentCard>
    </v-col>

    <!-- Sidebar -->
    <v-col cols="12" md="4">
      <!-- Lead Status Card -->
      <UiParentCard title="Lead Status" class="mb-4">
        <v-row>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis mb-2">Status</div>
              <v-chip
                :color="getStatusColor(lead.status)"
                size="default"
                label
              >
                {{ lead.status.charAt(0).toUpperCase() + lead.status.slice(1) }}
              </v-chip>
            </div>
          </v-col>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis mb-2">Lead Score</div>
              <v-chip
                :color="scoreColor"
                size="large"
                variant="flat"
              >
                {{ lead.score }}/100
              </v-chip>
            </div>
          </v-col>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis">Source</div>
              <div class="text-body-1">
                {{ lead.lead_source?.name || '-' }}
              </div>
            </div>
          </v-col>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis">Owner</div>
              <div class="text-body-1">
                {{ lead.owner?.name || '-' }}
              </div>
            </div>
          </v-col>
        </v-row>
      </UiParentCard>

      <!-- Metadata Card -->
      <UiParentCard title="Metadata">
        <v-row>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis">Created By</div>
              <div class="text-body-2">
                {{ lead.created_by?.name || '-' }}
              </div>
            </div>
          </v-col>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis">Created At</div>
              <div class="text-body-2">
                {{ lead.created_at ? new Date(lead.created_at).toLocaleString() : '-' }}
              </div>
            </div>
          </v-col>
          <v-col cols="12">
            <div class="mb-3">
              <div class="text-caption text-medium-emphasis">Last Updated</div>
              <div class="text-body-2">
                {{ lead.updated_at ? new Date(lead.updated_at).toLocaleString() : '-' }}
              </div>
            </div>
          </v-col>
        </v-row>
      </UiParentCard>

      <!-- Actions -->
      <v-btn
        color="secondary"
        variant="outlined"
        block
        class="mt-4"
        @click="backToList"
      >
        Back to Leads List
      </v-btn>
    </v-col>
  </v-row>

  <!-- Lead Conversion Modal -->
  <LeadConversionModal
    :show="showConversionModal"
    :lead="lead"
    :deal-stages="dealStages"
    @close="showConversionModal = false"
    @converted="handleConverted"
  />
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
