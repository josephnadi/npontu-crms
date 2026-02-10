<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  clients: Array,
  deals: Array,
});

const form = useForm({
  name: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  start_date: '',
  end_date: '',
  budget: '',
  client_id: null,
  deal_id: null,
});

const priorities = [
  { title: 'Low', value: 'low' },
  { title: 'Medium', value: 'medium' },
  { title: 'High', value: 'high' },
  { title: 'Urgent', value: 'urgent' },
];

const statuses = [
  { title: 'Pending', value: 'pending' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Completed', value: 'completed' },
  { title: 'Cancelled', value: 'cancelled' },
];

const submit = () => {
  form.post(route('crm.projects.store'));
};
</script>

<template>
  <Head title="Create Project" />
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
                <h1 class="text-h5 mb-0">Create New Project</h1>
              </div>
            </v-card-text>
            <v-divider></v-divider>

            <v-form @submit.prevent="submit">
              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.name"
                      label="Project Name"
                      variant="outlined"
                      :error-messages="form.errors.name"
                      required
                    ></v-text-field>
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

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.status"
                      :items="statuses"
                      label="Status"
                      variant="outlined"
                      :error-messages="form.errors.status"
                      required
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.priority"
                      :items="priorities"
                      label="Priority"
                      variant="outlined"
                      :error-messages="form.errors.priority"
                      required
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.client_id"
                      :items="clients"
                      item-title="name"
                      item-value="id"
                      label="Client"
                      variant="outlined"
                      clearable
                      :error-messages="form.errors.client_id"
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.deal_id"
                      :items="deals"
                      item-title="title"
                      item-value="id"
                      label="Source Deal"
                      variant="outlined"
                      clearable
                      :error-messages="form.errors.deal_id"
                      prepend-inner-icon="mdi-handshake-outline"
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="form.start_date"
                      label="Start Date"
                      type="date"
                      variant="outlined"
                      :error-messages="form.errors.start_date"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="form.end_date"
                      label="End Date"
                      type="date"
                      variant="outlined"
                      :error-messages="form.errors.end_date"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      v-model="form.budget"
                      label="Budget"
                      type="number"
                      prefix="$"
                      variant="outlined"
                      :error-messages="form.errors.budget"
                    ></v-text-field>
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
                  :loading="form.processing"
                >
                  Create Project
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>
