<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  communications: Array,
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
  <v-timeline side="end" align="start" density="compact">
    <template v-if="communications && communications.length > 0">
      <v-timeline-item
        v-for="comm in communications"
        :key="comm.id"
        :dot-color="getTypeColor(comm.type)"
        size="small"
      >
        <template v-slot:opposite>
          <div class="text-caption text-medium-emphasis">{{ new Date(comm.created_at).toLocaleDateString() }}</div>
        </template>
        
        <v-card elevation="0" border class="mb-2">
          <v-card-text class="pa-3">
            <div class="d-flex justify-space-between align-center mb-1">
              <div class="d-flex align-center gap-2">
                <v-icon :color="getTypeColor(comm.type)" size="16">{{ getTypeIcon(comm.type) }}</v-icon>
                <span class="text-subtitle-2 font-weight-bold">{{ comm.subject || (comm.type.toUpperCase() + ' Interaction') }}</span>
              </div>
              <v-chip size="x-small" :color="comm.direction === 'inbound' ? 'info' : 'secondary'" variant="tonal">
                {{ comm.direction.toUpperCase() }}
              </v-chip>
            </div>
            <div class="text-body-2 text-medium-emphasis text-truncate mb-2">{{ comm.content }}</div>
            <div class="d-flex justify-space-between align-center">
              <span class="text-caption text-medium-emphasis">{{ new Date(comm.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
              <Link :href="route('crm.communications.show', comm.id)" class="text-caption text-primary text-decoration-none">View Details</Link>
            </div>
          </v-card-text>
        </v-card>
      </v-timeline-item>
    </template>
    <div v-else class="text-center py-6">
      <v-icon size="48" color="grey-lighten-2" class="mb-2">mdi-message-off-outline</v-icon>
      <div class="text-body-2 text-medium-emphasis">No interactions logged yet.</div>
    </div>
  </v-timeline>
</template>
