<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const props = defineProps({
  activityableType: {
    type: String,
    required: true
  },
  activityableId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['success']);

const form = useForm({
  type: 'note',
  subject: '',
  description: '',
  activity_date: new Date().toISOString().slice(0, 16),
  due_date: '',
  status: 'pending',
  activityable_type: props.activityableType,
  activityable_id: props.activityableId,
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
      form.reset('subject', 'description');
      emit('success');
    },
  });
};
</script>

<template>
  <form @submit.prevent="submit">
    <v-row>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.type"
          :items="types"
          label="Activity Type"
          required
          density="comfortable"
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
          density="comfortable"
          variant="outlined"
          prepend-inner-icon="mdi-calendar-clock"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.subject"
          label="Subject"
          required
          density="comfortable"
          :error-messages="form.errors.subject"
          variant="outlined"
          prepend-inner-icon="mdi-text-short"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.description"
          label="Description / Notes"
          rows="3"
          density="comfortable"
          variant="outlined"
          prepend-inner-icon="mdi-note-text-outline"
        ></v-textarea>
      </v-col>
      <v-col cols="12" class="d-flex justify-end">
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
</template>
