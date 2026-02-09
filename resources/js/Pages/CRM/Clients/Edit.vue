<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  client: Object,
});

const form = useForm({
  name: props.client.name,
  industry: props.client.industry,
  website: props.client.website,
  phone: props.client.phone,
  address: props.client.address,
  city: props.client.city,
  state: props.client.state,
  country: props.client.country,
  postal_code: props.client.postal_code,
  notes: props.client.notes,
});

const submit = () => {
  form.put(route('crm.clients.update', props.client.id));
};
</script>

<template>
  <Head :title="'Edit Client - ' + client.name" />

  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="10" lg="8">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.clients.index')" class="mr-4">
            <v-btn icon variant="text">
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Edit Client: {{ client.name }}</h2>
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
                  label="Company Name"
                  required
                  :error-messages="form.errors.name"
                  variant="outlined"
                  prepend-inner-icon="mdi-domain"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.industry"
                  label="Industry"
                  :error-messages="form.errors.industry"
                  variant="outlined"
                  prepend-inner-icon="mdi-factory"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.website"
                  label="Website"
                  placeholder="https://example.com"
                  :error-messages="form.errors.website"
                  variant="outlined"
                  prepend-inner-icon="mdi-earth"
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
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Location Details</h3>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.address"
                  label="Street Address"
                  rows="2"
                  :error-messages="form.errors.address"
                  variant="outlined"
                  prepend-inner-icon="mdi-map-marker"
                ></v-textarea>
              </v-col>

              <v-col cols="12" md="6" lg="3">
                <v-text-field
                  v-model="form.city"
                  label="City"
                  :error-messages="form.errors.city"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6" lg="3">
                <v-text-field
                  v-model="form.state"
                  label="State/Province"
                  :error-messages="form.errors.state"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6" lg="3">
                <v-text-field
                  v-model="form.country"
                  label="Country"
                  :error-messages="form.errors.country"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" md="6" lg="3">
                <v-text-field
                  v-model="form.postal_code"
                  label="Postal Code"
                  :error-messages="form.errors.postal_code"
                  variant="outlined"
                ></v-text-field>
              </v-col>

              <v-col cols="12" class="mt-4">
                <h3 class="text-subtitle-1 font-weight-bold mb-4">Additional Notes</h3>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.notes"
                  label="Notes"
                  rows="4"
                  :error-messages="form.errors.notes"
                  variant="outlined"
                  prepend-inner-icon="mdi-note-text-outline"
                ></v-textarea>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.clients.index')" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Update Client
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
