<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { computed } from 'vue';

const props = defineProps({
  deals: {
    type: Array,
    required: true
  },
  leads: {
    type: Array,
    default: () => []
  },
  contacts: {
    type: Array,
    default: () => []
  },
  clients: {
    type: Array,
    default: () => []
  },
  projects: {
    type: Array,
    default: () => []
  },
  initial_activityable_type: String,
  initial_activityable_id: [String, Number],
});

const form = useForm({
  type: 'call',
  subject: '',
  description: '',
  activity_date: new Date().toISOString().slice(0, 16),
  due_date: '',
  status: 'pending',
  activityable_type: props.initial_activityable_type ? (props.initial_activityable_type.includes('\\') ? props.initial_activityable_type : `App\\Models\\${props.initial_activityable_type}`) : 'App\\Models\\Deal',
  activityable_id: props.initial_activityable_id ? parseInt(props.initial_activityable_id) : null,
  duration_minutes: null,
});

const entityTypes = [
  { title: 'Deal', value: 'App\\Models\\Deal' },
  { title: 'Lead', value: 'App\\Models\\Lead' },
  { title: 'Contact', value: 'App\\Models\\Contact' },
  { title: 'Client', value: 'App\\Models\\Client' },
  { title: 'Project', value: 'App\\Models\\Project' },
];

const activityTypes = [
  { title: 'Call', value: 'call' },
  { title: 'Meeting', value: 'meeting' },
  { title: 'Email', value: 'email' },
  { title: 'Task', value: 'task' },
  { title: 'Note', value: 'note' },
];

const availableEntities = computed(() => {
  switch (form.activityable_type) {
    case 'App\\Models\\Deal':
      return props.deals.map(d => ({ title: d.title, value: d.id }));
    case 'App\\Models\\Lead':
      return props.leads.map(l => ({ title: `${l.first_name} ${l.last_name}`, value: l.id }));
    case 'App\\Models\\Contact':
      return props.contacts.map(c => ({ title: `${c.first_name} ${c.last_name}`, value: c.id }));
    case 'App\\Models\\Client':
      return props.clients.map(c => ({ title: c.name, value: c.id }));
    case 'App\\Models\\Project':
      return props.projects.map(p => ({ title: p.name, value: p.id }));
    default:
      return [];
  }
});

const submit = () => {
  form.post(window.route('crm.activities.store'), {
    onSuccess: () => {
      // Success logic
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
                  :items="activityTypes"
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

              <v-col cols="12" md="6">
                <v-select
                  v-model="form.activityable_type"
                  :items="entityTypes"
                  label="Relate To"
                  required
                  :error-messages="form.errors.activityable_type"
                  variant="outlined"
                  prepend-inner-icon="mdi-link-variant"
                  @update:model-value="form.activityable_id = null"
                ></v-select>
              </v-col>

              <v-col cols="12" md="6">
                <v-autocomplete
                  v-model="form.activityable_id"
                  :items="availableEntities"
                  label="Select Entity"
                  required
                  :error-messages="form.errors.activityable_id"
                  variant="outlined"
                  prepend-inner-icon="mdi-magnify"
                  :disabled="!form.activityable_type"
                ></v-autocomplete>
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
                <v-text-field
                  label="Duration (minutes)"
                  type="number"
                  v-model="form.duration_minutes"
                  :error-messages="form.errors.duration_minutes"
                  variant="outlined"
                  prepend-inner-icon="mdi-timer-outline"
                ></v-text-field>
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
