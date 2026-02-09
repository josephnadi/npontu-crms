<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  lead: Object,
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
});

const statuses = [
  { title: 'New', value: 'new' },
  { title: 'Contacted', value: 'contacted' },
  { title: 'Qualified', value: 'qualified' },
  { title: 'Converted', value: 'converted' },
  { title: 'Lost', value: 'lost' },
];

const sources = ['Website', 'Referral', 'Cold Call', 'LinkedIn', 'Event', 'Partner', 'Other'];

const submit = () => {
  form.put(route('crm.leads.update', props.lead.id));
};
</script>

<template>
  <Head :title="'Edit Lead - ' + lead.first_name" />

  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.leads.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Edit Lead: {{ lead.first_name }} {{ lead.last_name }}</h2>
        </div>

        <v-card class="pa-6" elevation="0" border>
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
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.last_name"
                  label="Last Name"
                  required
                  :error-messages="form.errors.last_name"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.email"
                  label="Email Address"
                  type="email"
                  :error-messages="form.errors.email"
                  variant="outlined"
                  prepend-inner-icon="mdi-email-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.phone"
                  label="Phone Number"
                  :error-messages="form.errors.phone"
                  variant="outlined"
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
                  prepend-inner-icon="mdi-domain"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.job_title"
                  label="Job Title"
                  :error-messages="form.errors.job_title"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-combobox
                  v-model="form.source"
                  :items="sources"
                  label="Lead Source"
                  :error-messages="form.errors.source"
                  variant="outlined"
                ></v-combobox>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="statuses"
                  label="Status"
                  :error-messages="form.errors.status"
                  variant="outlined"
                ></v-select>
              </v-col>

              <v-col cols="12">
                <div class="text-subtitle-2 mb-2">Lead Score: {{ form.score }}%</div>
                <v-slider
                  v-model="form.score"
                  color="primary"
                  thumb-label
                  step="5"
                ></v-slider>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Lead Notes"
                  rows="4"
                  :error-messages="form.errors.notes"
                  variant="outlined"
                ></v-textarea>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.leads.index')" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Update Lead
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
