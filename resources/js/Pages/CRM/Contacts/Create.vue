<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  clients: Array,
});

const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  job_title: '',
  client_id: null,
  status: 'active',
  address: '',
  city: '',
  state: '',
  country: '',
  postal_code: '',
  notes: '',
  tags: [],
});

const statuses = [
  { title: 'Active', value: 'active' },
  { title: 'Inactive', value: 'inactive' },
];

const submit = () => {
  form.post(route('crm.contacts.store'));
};
</script>

<template>
  <Head title="Add Contact" />

  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.contacts.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Add New Contact</h2>
        </div>

        <v-card class="pa-6" elevation="0" border>
          <form @submit.prevent="submit">
            <v-row>
              <v-col cols="12">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Personal Information</h3>
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
                <v-select
                  v-model="form.client_id"
                  :items="clients"
                  item-title="name"
                  item-value="id"
                  label="Client / Company"
                  :error-messages="form.errors.client_id"
                  variant="outlined"
                  clearable
                ></v-select>
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
                <v-select
                  v-model="form.status"
                  :items="statuses"
                  label="Status"
                  :error-messages="form.errors.status"
                  variant="outlined"
                ></v-select>
              </v-col>

              <v-col cols="12" class="mt-4">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Address Information</h3>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.address"
                  label="Street Address"
                  :error-messages="form.errors.address"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.city"
                  label="City"
                  :error-messages="form.errors.city"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.state"
                  label="State / Province"
                  :error-messages="form.errors.state"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.country"
                  label="Country"
                  :error-messages="form.errors.country"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.postal_code"
                  label="Postal Code"
                  :error-messages="form.errors.postal_code"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" class="mt-4">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Additional Info</h3>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Internal Notes"
                  rows="4"
                  :error-messages="form.errors.notes"
                  variant="outlined"
                ></v-textarea>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.contacts.index')" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Create Contact
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
