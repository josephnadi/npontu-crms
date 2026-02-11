<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  engageableType: {
    type: String,
    required: true
  },
  engageableId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['success']);

const form = useForm({
  type: 'email',
  subject: '',
  description: '',
  engagement_date: new Date().toISOString().slice(0, 16),
  score: 10,
  engageable_type: props.engageableType,
  engageable_id: props.engageableId,
  metadata: {}
});

const types = [
  { title: 'Email Open', value: 'email' },
  { title: 'Phone Call', value: 'call' },
  { title: 'Meeting', value: 'meeting' },
  { title: 'Webinar Attended', value: 'webinar' },
  { title: 'Form Submitted', value: 'form_submitted' },
  { title: 'Content Viewed', value: 'content_viewed' },
  { title: 'Other', value: 'other' },
];

const scores = [
  { title: 'Low (1-10)', value: 10 },
  { title: 'Medium (11-49)', value: 30 },
  { title: 'High (50-100)', value: 75 },
];

const submit = () => {
  form.post(window.route('crm.engagements.store'), {
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
          label="Engagement Type"
          required
          density="comfortable"
          variant="outlined"
          prepend-inner-icon="mdi-star-circle-outline"
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.score"
          :items="scores"
          label="Priority / Score"
          required
          density="comfortable"
          variant="outlined"
          prepend-inner-icon="mdi-trending-up"
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.engagement_date"
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
          label="Subject / Topic"
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
          label="Details"
          rows="3"
          density="comfortable"
          variant="outlined"
          prepend-inner-icon="mdi-note-text-outline"
        ></v-textarea>
      </v-col>
      <v-col cols="12" class="d-flex justify-end">
        <v-btn
          type="submit"
          color="warning"
          :loading="form.processing"
          prepend-icon="mdi-check"
        >
          Log Engagement
        </v-btn>
      </v-col>
    </v-row>
  </form>
</template>
