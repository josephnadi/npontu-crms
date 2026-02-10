<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  lead: Object,
  users: Array,
});

const form = useForm({
  first_name: props.lead.first_name,
  last_name: props.lead.last_name,
  email: props.lead.email,
  phone: props.lead.phone,
  company_name: props.lead.company_name,
  job_title: props.lead.job_title,
  source: props.lead.source,
  status: props.lead.status,
  score: props.lead.score,
  notes: props.lead.notes,
  owner_id: props.lead.owner_id,
});

const statuses = [
  { title: 'New', value: 'new' },
  { title: 'Contacted', value: 'contacted' },
  { title: 'Qualified', value: 'qualified' },
  { title: 'Converted', value: 'converted' },
  { title: 'Lost', value: 'lost' },
];

const sources = [
  'Website Form',
  'Referral',
  'Social Media',
  'Cold Call',
  'Email Campaign',
  'Event/Trade Show',
  'Other'
];

const submit = () => {
  form.put(route('crm.leads.update', props.lead.id));
};
</script>

<template>
  <Head :title="'Edit Lead - ' + lead.first_name" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="mb-0">
        <div class="d-flex align-center text-caption text-medium-emphasis mb-2">
          <span>Home</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span>CRM</span>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <Link :href="route('crm.leads.index')" class="text-decoration-none text-medium-emphasis">Leads</Link>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span class="text-high-emphasis">Edit Lead</span>
        </div>
        <h2 class="text-h3 font-weight-bold mb-6">Edit Lead: {{ lead.first_name }} {{ lead.last_name }}</h2>
      </v-col>

      <v-col cols="12" md="8">
        <v-card elevation="0" border>
          <v-card-title class="pa-4 border-bottom">
            <span class="text-h6 font-weight-bold">Lead Details</span>
          </v-card-title>

          <v-card-text class="pa-6">
            <form @submit.prevent="submit">
              <v-row>
                <v-col cols="12">
                  <h3 class="text-subtitle-1 font-weight-bold mb-4">Contact Information</h3>
                </v-col>
                
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.first_name"
                    label="First Name"
                    required
                    :error-messages="form.errors.first_name"
                    variant="outlined"
                    density="comfortable"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.last_name"
                    label="Last Name"
                    required
                    :error-messages="form.errors.last_name"
                    variant="outlined"
                    density="comfortable"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.email"
                    label="Email Address"
                    type="email"
                    :error-messages="form.errors.email"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-email-outline"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.phone"
                    label="Phone Number"
                    :error-messages="form.errors.phone"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-phone"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" class="mt-4">
                  <h3 class="text-subtitle-1 font-weight-bold mb-4">Professional Details</h3>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.company_name"
                    label="Company Name"
                    :error-messages="form.errors.company_name"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-domain"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.job_title"
                    label="Job Title"
                    :error-messages="form.errors.job_title"
                    variant="outlined"
                    density="comfortable"
                  ></v-text-field>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.source"
                    :items="sources"
                    label="Lead Source"
                    :error-messages="form.errors.source"
                    variant="outlined"
                    density="comfortable"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.status"
                    :items="statuses"
                    label="Status"
                    :error-messages="form.errors.status"
                    variant="outlined"
                    density="comfortable"
                  ></v-select>
                </v-col>

                <v-col cols="12" md="6">
                  <v-select
                    v-model="form.owner_id"
                    :items="users"
                    :item-title="item => `${item.first_name} ${item.last_name}`"
                    item-value="id"
                    label="Lead Owner"
                    :error-messages="form.errors.owner_id"
                    variant="outlined"
                    density="comfortable"
                    prepend-inner-icon="mdi-account-tie"
                  ></v-select>
                </v-col>

                <v-col cols="12">
                  <div class="text-subtitle-2 mb-2">Lead Score: {{ form.score }}%</div>
                  <v-slider
                    v-model="form.score"
                    color="primary"
                    thumb-label
                    step="5"
                    hide-details
                  ></v-slider>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    v-model="form.notes"
                    label="Lead Notes"
                    rows="4"
                    :error-messages="form.errors.notes"
                    variant="outlined"
                    density="comfortable"
                  ></v-textarea>
                </v-col>

                <v-col cols="12" class="d-flex justify-end gap-2 mt-4">
                  <Link :href="route('crm.leads.index')" class="text-decoration-none mr-2">
                    <v-btn variant="outlined" density="comfortable">Cancel</v-btn>
                  </Link>
                  <v-btn
                    type="submit"
                    color="primary"
                    :loading="form.processing"
                    flat
                    density="comfortable"
                  >
                    Update Lead
                  </v-btn>
                </v-col>
              </v-row>
            </form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>

<style scoped>
.border-bottom {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}
.gap-2 {
  gap: 8px;
}
</style>
