<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  partner: Object,
});

const form = useForm({
  name: props.partner.name,
  type: props.partner.type,
  status: props.partner.status,
  email: props.partner.email || '',
  phone: props.partner.phone || '',
  website: props.partner.website || '',
  description: props.partner.description || '',
  commission_rate: props.partner.commission_rate || 0,
});

const types = ['Referral', 'Reseller', 'Technological', 'Strategic'];
const statuses = ['Active', 'Inactive', 'Pending'];

const submit = () => {
  form.put(route('crm.partners.update', props.partner.id));
};
</script>

<template>
  <Head :title="'Edit Partner - ' + partner.name" />

  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.partners.show', partner.id)" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Edit Partner</h2>
        </div>

        <v-card class="pa-6" elevation="0" border>
          <form @submit.prevent="submit">
            <v-row>
              <v-col cols="12">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Basic Information</h3>
              </v-col>
              
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.name"
                  label="Partner Name"
                  required
                  :error-messages="form.errors.name"
                  variant="outlined"
                  prepend-inner-icon="mdi-handshake-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.type"
                  :items="types"
                  label="Partner Type"
                  required
                  :error-messages="form.errors.type"
                  variant="outlined"
                  prepend-inner-icon="mdi-tag-outline"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="statuses"
                  label="Status"
                  required
                  :error-messages="form.errors.status"
                  variant="outlined"
                  prepend-inner-icon="mdi-circle-medium"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.commission_rate"
                  label="Commission Rate (%)"
                  type="number"
                  min="0"
                  max="100"
                  :error-messages="form.errors.commission_rate"
                  variant="outlined"
                  prepend-inner-icon="mdi-percent"
                ></v-text-field>
              </v-col>

              <v-col cols="12" class="mt-4">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Contact Details</h3>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.email"
                  label="Email"
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
                  prepend-inner-icon="mdi-phone-outline"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.website"
                  label="Website"
                  placeholder="https://example.com"
                  :error-messages="form.errors.website"
                  variant="outlined"
                  prepend-inner-icon="mdi-earth"
                ></v-text-field>
              </v-col>

              <v-col cols="12" class="mt-4">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Additional Information</h3>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description"
                  rows="4"
                  :error-messages="form.errors.description"
                  variant="outlined"
                  prepend-inner-icon="mdi-text-box-outline"
                ></v-textarea>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.partners.show', partner.id)" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Update Partner
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
