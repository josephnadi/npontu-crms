<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  deals: {
    type: Array,
    required: true
  }
});

const form = useForm({
  type: 'call',
  subject: '',
  description: '',
  activity_date: new Date().toISOString().slice(0, 16),
  due_date: '',
  status: 'pending',
  activityable_type: 'App\\Models\\Deal',
  activityable_id: null,
});

const types = [
  { title: 'Call', value: 'call' },
  { title: 'Meeting', value: 'meeting' },
  { title: 'Email', value: 'email' },
  { title: 'Task', value: 'task' },
  { title: 'Note', value: 'note' },
];

const submit = () => {
  form.post(window.route('crm.activities.store'), {
    onSuccess: () => {
      // Redirect to index or calendar
    },
  });
};
</script>

<template>
  <Head title="Schedule Activity" />
  
  <DashboardLayout>
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <div class="mb-6 d-flex align-center">
          <Link :href="route('crm.activities.index')" class="mr-4">
            <v-btn icon variant="text">
              <SvgSprite name="custom-chevron-left" style="width: 20px; height: 20px" />
            </v-btn>
          </Link>
          <h2 class="text-h4 font-weight-bold">Schedule Activity</h2>
        </div>

        <v-card class="pa-6">
          <form @submit.prevent="submit">
            <v-row>
              <v-col cols="12" md="6">
                <v-select
                  v-model="form.type"
                  :items="types"
                  label="Activity Type"
                  required
                  :error-messages="form.errors.type"
                  variant="outlined"
                  prepend-inner-icon="mdi-format-list-bulleted-type"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.activity_date"
                  label="Date & Time"
                  type="datetime-local"
                  required
                  :error-messages="form.errors.activity_date"
                  variant="outlined"
                  prepend-inner-icon="mdi-calendar-clock"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-select
                  v-model="form.activityable_id"
                  :items="deals"
                  item-title="title"
                  item-value="id"
                  label="Relate to Deal"
                  required
                  :error-messages="form.errors.activityable_id"
                  variant="outlined"
                  prepend-inner-icon="mdi-link-variant"
                ></v-select>
              </v-col>

              <v-col cols="12">
                <v-text-field
                  v-model="form.subject"
                  label="Subject"
                  required
                  :error-messages="form.errors.subject"
                  variant="outlined"
                  prepend-inner-icon="mdi-text-short"
                ></v-text-field>
              </v-col>

              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Description / Notes"
                  rows="4"
                  :error-messages="form.errors.description"
                  variant="outlined"
                  prepend-inner-icon="mdi-note-text-outline"
                ></v-textarea>
              </v-col>

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.status"
                  :items="['pending', 'completed', 'cancelled']"
                  label="Status"
                  required
                  :error-messages="form.errors.status"
                  variant="outlined"
                  prepend-inner-icon="mdi-check-circle-outline"
                ></v-select>
              </v-col>

              <v-col cols="12" class="d-flex justify-end gap-2">
                <Link :href="route('crm.activities.index')" class="text-decoration-none mr-2">
                  <v-btn variant="outlined">Cancel</v-btn>
                </Link>
                <v-btn
                  type="submit"
                  color="primary"
                  :loading="form.processing"
                >
                  Save Activity
                </v-btn>
              </v-col>
            </v-row>
          </form>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
