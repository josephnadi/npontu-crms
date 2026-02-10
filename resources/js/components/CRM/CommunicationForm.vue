<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  communicableType: String,
  communicableId: Number,
  defaultTo: String,
});

const emit = defineEmits(['success']);

const form = useForm({
  type: 'email',
  direction: 'outbound',
  subject: '',
  content: '',
  from_identifier: 'system@crm.com', // Default system email
  to_identifier: props.defaultTo || '',
  status: 'sent',
  metadata: {},
  communicable_type: props.communicableType,
  communicable_id: props.communicableId,
});

const types = [
  { title: 'Email', value: 'email' },
  { title: 'SMS', value: 'sms' },
  { title: 'WhatsApp', value: 'whatsapp' },
  { title: 'Call', value: 'call' },
];

const directions = [
  { title: 'Inbound', value: 'inbound' },
  { title: 'Outbound', value: 'outbound' },
];

const submit = () => {
  form.post(route('crm.communications.store'), {
    onSuccess: () => {
      form.reset();
      emit('success');
    },
  });
};
</script>

<template>
  <v-form @submit.prevent="submit">
    <v-row>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.type"
          :items="types"
          label="Interaction Type"
          variant="outlined"
          :error-messages="form.errors.type"
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-select
          v-model="form.direction"
          :items="directions"
          label="Direction"
          variant="outlined"
          :error-messages="form.errors.direction"
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.from_identifier"
          label="From"
          placeholder="Email or Phone"
          variant="outlined"
          :error-messages="form.errors.from_identifier"
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.to_identifier"
          label="To"
          placeholder="Email or Phone"
          variant="outlined"
          :error-messages="form.errors.to_identifier"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.subject"
          label="Subject"
          placeholder="Enter subject..."
          variant="outlined"
          :error-messages="form.errors.subject"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.content"
          label="Content / Message"
          placeholder="What was discussed?"
          variant="outlined"
          rows="4"
          :error-messages="form.errors.content"
        ></v-textarea>
      </v-col>
      <v-col cols="12" class="d-flex justify-end gap-2">
        <v-btn color="primary" type="submit" :loading="form.processing">Log Interaction</v-btn>
      </v-col>
    </v-row>
  </v-form>
</template>
