<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

const props = defineProps({
  lead: {
    type: Object,
    required: true
  },
  leadSources: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
});

const pageTitle = ref('Edit Lead');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: false, href: '/leads' },
  { title: props.lead.full_name || 'Lead', disabled: false, href: `/leads/${props.lead.id}` },
  { title: 'Edit', disabled: true, href: '#' }
]);

const form = useForm({
  first_name: props.lead.first_name || '',
  last_name: props.lead.last_name || '',
  email: props.lead.email || '',
  phone: props.lead.phone || '',
  mobile: props.lead.mobile || '',
  company_name: props.lead.company_name || '',
  job_title: props.lead.job_title || '',
  lead_source_id: props.lead.lead_source?.id || null,
  status: props.lead.status || 'new',
  score: props.lead.score || 50,
  address_line1: props.lead.address?.line1 || '',
  address_line2: props.lead.address?.line2 || '',
  city: props.lead.address?.city || '',
  state: props.lead.address?.state || '',
  postal_code: props.lead.address?.postal_code || '',
  country: props.lead.address?.country || '',
  notes: props.lead.notes || '',
  owner_id: props.lead.owner?.id || null
});

const statusOptions = [
  { value: 'new', title: 'New' },
  { value: 'contacted', title: 'Contacted' },
  { value: 'qualified', title: 'Qualified' },
  { value: 'converted', title: 'Converted' },
  { value: 'lost', title: 'Lost' }
];

const submit = () => {
  form.put(`/leads/${props.lead.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Will redirect to show page from controller
    }
  });
};

const cancel = () => {
  router.visit(`/leads/${props.lead.id}`);
};
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <v-row>
    <v-col cols="12">
      <UiParentCard title="Edit Lead Information">
        <v-form @submit.prevent="submit">
          <!-- Personal Information -->
          <v-row>
            <v-col cols="12">
              <h3 class="text-h6 mb-4">Personal Information</h3>
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.first_name"
                label="First Name *"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.first_name"
                required
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.last_name"
                label="Last Name *"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.last_name"
                required
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.email"
                label="Email"
                type="email"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.email"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.phone"
                label="Phone"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.phone"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.mobile"
                label="Mobile"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.mobile"
              />
            </v-col>
          </v-row>

          <v-divider class="my-6" />

          <!-- Company Information -->
          <v-row>
            <v-col cols="12">
              <h3 class="text-h6 mb-4">Company Information</h3>
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.company_name"
                label="Company Name"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.company_name"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.job_title"
                label="Job Title"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.job_title"
              />
            </v-col>
          </v-row>

          <v-divider class="my-6" />

          <!-- Lead Details -->
          <v-row>
            <v-col cols="12">
              <h3 class="text-h6 mb-4">Lead Details</h3>
            </v-col>
            
            <v-col cols="12" md="4">
              <v-select
                v-model="form.lead_source_id"
                :items="leadSources"
                item-title="name"
                item-value="id"
                label="Lead Source"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.lead_source_id"
                clearable
              />
            </v-col>
            
            <v-col cols="12" md="4">
              <v-select
                v-model="form.status"
                :items="statusOptions"
                label="Status *"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.status"
              />
            </v-col>
            
            <v-col cols="12" md="4">
              <v-select
                v-model="form.owner_id"
                :items="users"
                item-title="name"
                item-value="id"
                label="Owner"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.owner_id"
                clearable
              />
            </v-col>
            
            <v-col cols="12">
              <label class="text-subtitle-2 mb-2 d-block">
                Lead Score: {{ form.score }}
              </label>
              <v-slider
                v-model="form.score"
                :min="0"
                :max="100"
                :step="5"
                thumb-label
                color="primary"
                :error-messages="form.errors.score"
              />
            </v-col>
          </v-row>

          <v-divider class="my-6" />

          <!-- Address -->
          <v-row>
            <v-col cols="12">
              <h3 class="text-h6 mb-4">Address</h3>
            </v-col>
            
            <v-col cols="12">
              <v-text-field
                v-model="form.address_line1"
                label="Address Line 1"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.address_line1"
              />
            </v-col>
            
            <v-col cols="12">
              <v-text-field
                v-model="form.address_line2"
                label="Address Line 2"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.address_line2"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.city"
                label="City"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.city"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.state"
                label="State/Province"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.state"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.postal_code"
                label="Postal Code"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.postal_code"
              />
            </v-col>
            
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.country"
                label="Country"
                variant="outlined"
                density="comfortable"
                :error-messages="form.errors.country"
              />
            </v-col>
          </v-row>

          <v-divider class="my-6" />

          <!-- Notes -->
          <v-row>
            <v-col cols="12">
              <h3 class="text-h6 mb-4">Additional Notes</h3>
            </v-col>
            
            <v-col cols="12">
              <v-textarea
                v-model="form.notes"
                label="Notes"
                variant="outlined"
                rows="4"
                :error-messages="form.errors.notes"
              />
            </v-col>
          </v-row>

          <!-- Form Actions -->
          <v-row class="mt-4">
            <v-col cols="12" class="d-flex gap-2">
              <v-btn
                type="submit"
                color="primary"
                size="large"
                :loading="form.processing"
                :disabled="form.processing"
              >
                Update Lead
              </v-btn>
              <v-btn
                color="secondary"
                variant="outlined"
                size="large"
                @click="cancel"
                :disabled="form.processing"
              >
                Cancel
              </v-btn>
            </v-col>
          </v-row>
        </v-form>
      </UiParentCard>
    </v-col>
  </v-row>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
