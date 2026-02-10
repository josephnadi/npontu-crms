<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  task: Object,
  projects: Array,
  users: Array,
});

const form = useForm({
  title: props.task.title,
  description: props.task.description,
  status: props.task.status,
  priority: props.task.priority,
  due_date: props.task.due_date ? props.task.due_date.split('T')[0] : '',
  project_id: props.task.project_id,
  assigned_to: props.task.assigned_to,
});

const statuses = [
  { title: 'Pending', value: 'pending' },
  { title: 'In Progress', value: 'in_progress' },
  { title: 'Completed', value: 'completed' },
];

const priorities = [
  { title: 'Low', value: 'low' },
  { title: 'Medium', value: 'medium' },
  { title: 'High', value: 'high' },
];

const submit = () => {
  form.put(route('crm.tasks.update', props.task.id));
};
</script>

<template>
  <Head title="Edit Task" />
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
                <h1 class="text-h5 mb-0">Edit Task: {{ task.title }}</h1>
              </div>
            </v-card-text>
            <v-divider></v-divider>

            <v-form @submit.prevent="submit">
              <v-card-text class="pa-6">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.title"
                      label="Task Title"
                      variant="outlined"
                      :error-messages="form.errors.title"
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
                      v-model="form.project_id"
                      :items="projects"
                      item-title="name"
                      item-value="id"
                      label="Project"
                      variant="outlined"
                      clearable
                      :error-messages="form.errors.project_id"
                    ></v-select>
                  </v-col>

                  <v-col cols="12" sm="6">
                    <v-select
                      v-model="form.assigned_to"
                      :items="users"
                      item-title="name"
                      item-value="id"
                      label="Assigned To"
                      variant="outlined"
                      clearable
                      :error-messages="form.errors.assigned_to"
                    ></v-select>
                  </v-col>

                  <v-col cols="12">
                    <v-text-field
                      v-model="form.due_date"
                      label="Due Date"
                      type="date"
                      variant="outlined"
                      :error-messages="form.errors.due_date"
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
                  Update Task
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </DashboardLayout>
</template>
