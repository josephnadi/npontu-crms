<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const form = useForm({
  name: '',
  type: 'email_campaign',
  status: 'draft',
  description: '',
  trigger_config: {},
  content_config: {},
});

const types = [
  { title: 'Email Campaign', value: 'email_campaign' },
  { title: 'Drip Sequence', value: 'drip_sequence' },
  { title: 'Newsletter', value: 'newsletter' },
  { title: 'SMS Alert', value: 'sms_alert' },
];

const statuses = [
  { title: 'Draft', value: 'draft' },
  { title: 'Active', value: 'active' },
  { title: 'Paused', value: 'paused' },
];

const submit = () => {
  form.post(route('crm.marketing-automations.store'));
};
</script>

<template>
  <Head title="Create Automation" />
  <DashboardLayout>
    <v-container fluid>
      <v-row justify="center">
        <v-col cols="12" md="8">
          <v-card elevation="0">
            <v-card-text class="d-flex align-center py-4">
              <v-btn
                icon="mdi-arrow-left"
                variant="text"
                class="mr-2"
                @click="$window.history.back()"
              ></v-btn>
              <div>
                <h1 class="text-h5 mb-0">Create Marketing Automation</h1>
              </div>
            </v-card-text>
            <v-divider></v-divider>

            <v-form @submit.prevent="submit">
              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.name"
                      label="Automation Name"
                      variant="outlined"
                      :error-messages="form.errors.name"
                      required
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.type"
                      :items="types"
                      label="Automation Type"
                      variant="outlined"
                      :error-messages="form.errors.type"
                      required
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.status"
                      :items="statuses"
                      label="Initial Status"
                      variant="outlined"
                      :error-messages="form.errors.status"
                      required
                    ></v-select>
                  </v-col>

                  <v-col cols="12">
                    <v-textarea
                      v-model="form.description"
                      label="Description"
                      variant="outlined"
                      rows="3"
                      :error-messages="form.errors.description"
                    ></v-textarea>
                  </v-col>

                  <v-col cols="12">
                    <v-alert
                      type="info"
                      variant="tonal"
                      class="mb-4"
                      text="Workflow triggers and content editor will be available after creating the basic automation."
                    ></v-alert>
                  </v-col>
                </v-row>
              </v-card-text>

              <v-divider></v-divider>
              <v-card-actions class="pa-6">
                <v-spacer></v-spacer>
                <v-btn
                  variant="text"
                  @click="$window.history.back()"
                  class="mr-2"
                >
                  Cancel
                </v-btn>
                <v-btn
                  color="primary"
                  type="submit"
                  size="large"
                  :loading="form.processing"
                >
                  Create Automation
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>
