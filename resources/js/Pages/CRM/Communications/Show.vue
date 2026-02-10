<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';

const props = defineProps({
  communication: Object,
});

const getTypeIcon = (type) => {
  switch (type) {
    case 'email': return 'mdi-email-outline';
    case 'sms': return 'mdi-message-text-outline';
    case 'whatsapp': return 'mdi-whatsapp';
    case 'call': return 'mdi-phone-outline';
    default: return 'mdi-information-outline';
  }
};

const getTypeColor = (type) => {
  switch (type) {
    case 'email': return 'primary';
    case 'sms': return 'info';
    case 'whatsapp': return 'success';
    case 'call': return 'warning';
    default: return 'grey';
  }
};
</script>

<template>
  <Head :title="'Interaction: ' + (communication.subject || communication.type)" />

  <DashboardLayout>
    <v-row>
      <v-col cols="12">
        <div class="d-flex align-center text-caption text-medium-emphasis mb-2">
          <Link :href="route('crm.communications.index')" class="text-decoration-none text-medium-emphasis">Unified Inbox</Link>
          <v-icon size="14" class="mx-1">mdi-chevron-right</v-icon>
          <span class="text-high-emphasis">Interaction Details</span>
        </div>
        <div class="d-flex justify-space-between align-center mb-6">
          <h2 class="text-h3 font-weight-bold">{{ communication.subject || 'Interaction Details' }}</h2>
          <v-btn variant="outlined" :href="route('crm.communications.index')" prepend-icon="mdi-arrow-left">Back to Inbox</v-btn>
        </div>
      </v-col>

      <v-col cols="12" md="8">
        <v-card elevation="0" border class="mb-6">
          <v-card-item>
            <template v-slot:prepend>
              <v-avatar :color="getTypeColor(communication.type) + '-lighten-5'" size="48">
                <v-icon :color="getTypeColor(communication.type)">{{ getTypeIcon(communication.type) }}</v-icon>
              </v-avatar>
            </template>
            <v-card-title class="text-h5 font-weight-bold">{{ communication.type.toUpperCase() }} Interaction</v-card-title>
            <v-card-subtitle>
              <v-chip size="x-small" :color="communication.direction === 'inbound' ? 'info' : 'secondary'" variant="tonal" class="mr-2">
                {{ communication.direction.toUpperCase() }}
              </v-chip>
              {{ new Date(communication.created_at).toLocaleString() }}
            </v-card-subtitle>
          </v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-6">
            <div class="mb-6">
              <div class="text-subtitle-2 font-weight-bold text-medium-emphasis mb-1">Subject</div>
              <div class="text-h6">{{ communication.subject || '(No Subject)' }}</div>
            </div>
            <div class="mb-6">
              <div class="text-subtitle-2 font-weight-bold text-medium-emphasis mb-1">Message Content</div>
              <div class="text-body-1 white-space-pre-wrap pa-4 bg-grey-lighten-4 rounded" style="white-space: pre-wrap;">{{ communication.content }}</div>
            </div>
            <v-row v-if="communication.metadata">
              <v-col cols="12">
                <div class="text-subtitle-2 font-weight-bold text-medium-emphasis mb-1">Technical Metadata</div>
                <pre class="pa-4 bg-grey-darken-4 text-white rounded text-caption overflow-x-auto">{{ JSON.stringify(communication.metadata, null, 2) }}</pre>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>

      <v-col cols="12" md="4">
        <v-card elevation="0" border class="mb-6">
          <v-card-item title="Participants"></v-card-item>
          <v-divider></v-divider>
          <v-list>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="medium-emphasis">mdi-account-arrow-right</v-icon>
              </template>
              <v-list-item-title class="text-caption font-weight-bold">FROM</v-list-item-title>
              <v-list-item-subtitle class="text-body-1 font-weight-bold text-high-emphasis">{{ communication.from_identifier }}</v-list-item-subtitle>
            </v-list-item>
            <v-list-item>
              <template v-slot:prepend>
                <v-icon color="medium-emphasis">mdi-account-arrow-left</v-icon>
              </template>
              <v-list-item-title class="text-caption font-weight-bold">TO</v-list-item-title>
              <v-list-item-subtitle class="text-body-1 font-weight-bold text-high-emphasis">{{ communication.to_identifier }}</v-list-item-subtitle>
            </v-list-item>
          </v-list>
        </v-card>

        <v-card v-if="communication.communicable" elevation="0" border>
          <v-card-item title="Linked Record"></v-card-item>
          <v-divider></v-divider>
          <v-card-text class="pa-4">
            <div class="d-flex align-center gap-3">
              <v-avatar color="primary" size="40">
                <v-icon color="white">mdi-link-variant</v-icon>
              </v-avatar>
              <div>
                <div class="text-subtitle-2 font-weight-bold">{{ communication.communicable_type.split('\\').pop() }}</div>
                <div class="text-body-2">{{ communication.communicable.first_name || communication.communicable.title || communication.communicable.name }}</div>
              </div>
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>
