<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

const props = defineProps({
  leadSources: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
});

const pageTitle = ref('Create Lead');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: false, href: '/crm/leads' },
  { title: 'Create', disabled: true, href: '#' }
]);

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  mobile: '',
  company_name: '',
  job_title: '',
  lead_source_id: null,
  status: 'new',
  score: 0,
  address_line1: '',
  address_line2: '',
  city: '',
  state: '',
  postal_code: '',
  country: '',
  notes: '',
  owner_id: null
});

const statusOptions = [
  { value: 'new', title: 'New' },
  { value: 'contacted', title: 'Contacted' },
  { value: 'qualified', title: 'Qualified' },
  { value: 'lost', title: 'Lost' }
];

const submit = () => {
  form.post(route('crm.leads.store'), {
    onSuccess: () => {
      // Handle success
    }
  });
};
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />

  <v-row>
    <v-col cols="12" md="8">
      <UiParentCard title="Lead Details">
        <v-form @submit.prevent="submit">
          <v-row>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.first_name"
                label="First Name"
                variant="outlined"
                :error-messages="form.errors.first_name"
                required
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.last_name"
                label="Last Name"
                variant="outlined"
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
                :error-messages="form.errors.email"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.phone"
                label="Phone"
                variant="outlined"
                :error-messages="form.errors.phone"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.company_name"
                label="Company"
                variant="outlined"
                :error-messages="form.errors.company_name"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.job_title"
                label="Job Title"
                variant="outlined"
                :error-messages="form.errors.job_title"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="form.lead_source_id"
                :items="leadSources"
                item-title="name"
                item-value="id"
                label="Source"
                variant="outlined"
                :error-messages="form.errors.lead_source_id"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-select
                v-model="form.status"
                :items="statusOptions"
                label="Status"
                variant="outlined"
                :error-messages="form.errors.status"
              />
            </v-col>
            <v-col cols="12">
              <v-textarea
                v-model="form.notes"
                label="Notes"
                variant="outlined"
                rows="3"
                :error-messages="form.errors.notes"
              />
            </v-col>
          </v-row>

          <h3 class="text-subtitle-1 font-weight-bold my-4">Address Information</h3>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="form.address_line1"
                label="Address Line 1"
                variant="outlined"
                :error-messages="form.errors.address_line1"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.city"
                label="City"
                variant="outlined"
                :error-messages="form.errors.city"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.state"
                label="State/Province"
                variant="outlined"
                :error-messages="form.errors.state"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.country"
                label="Country"
                variant="outlined"
                :error-messages="form.errors.country"
              />
            </v-col>
            <v-col cols="12" md="6">
              <v-text-field
                v-model="form.postal_code"
                label="Postal Code"
                variant="outlined"
                :error-messages="form.errors.postal_code"
              />
            </v-col>
          </v-row>

          <v-divider class="my-6"></v-divider>

          <div class="d-flex justify-end gap-2">
            <v-btn
              color="secondary"
              variant="outlined"
              @click="$inertia.visit(route('crm.leads.index'))"
            >
              Cancel
            </v-btn>
            <v-btn
              color="primary"
              type="submit"
              :loading="form.processing"
            >
              Create Lead
            </v-btn>
          </div>
        </v-form>
      </UiParentCard>
    </v-col>

    <v-col cols="12" md="4">
      <UiParentCard title="Settings">
        <v-select
          v-model="form.owner_id"
          :items="users"
          item-title="name"
          item-value="id"
          label="Lead Owner"
          variant="outlined"
          :error-messages="form.errors.owner_id"
          hint="The user responsible for this lead"
          persistent-hint
          class="mb-4"
        />
        <v-slider
          v-model="form.score"
          label="Lead Score"
          min="0"
          max="100"
          step="1"
          thumb-label
          color="primary"
          class="mt-4"
        >
          <template v-slot:append>
            <v-text-field
              v-model="form.score"
              type="number"
              style="width: 70px"
              density="compact"
              hide-details
              variant="outlined"
            ></v-text-field>
          </template>
        </v-slider>
      </UiParentCard>
    </v-col>
  </v-row>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
