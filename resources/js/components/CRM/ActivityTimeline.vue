<script setup>
import { ref } from 'vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  activities: {
    type: Array,
    required: true
  }
});

const getIcon = (type) => {
  switch (type) {
    case 'call': return 'custom-phone';
    case 'meeting': return 'custom-users';
    case 'email': return 'custom-mail';
    case 'task': return 'custom-check';
    case 'note': return 'custom-file-text';
    default: return 'custom-calendar';
  }
};

const getColor = (type) => {
  switch (type) {
    case 'call': return 'info';
    case 'meeting': return 'primary';
    case 'email': return 'warning';
    case 'task': return 'success';
    case 'note': return 'secondary';
    default: return 'grey';
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};
</script>

<template>
  <v-card variant="flat">
    <v-card-text>
      <div v-if="activities.length === 0" class="text-center py-8">
        <v-icon icon="mdi-calendar-blank" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
        <p class="text-grey">No activities recorded yet.</p>
      </div>
      
      <v-timeline side="end" align="start" density="compact">
        <v-timeline-item
          v-for="activity in activities"
          :key="activity.id"
          :dot-color="getColor(activity.type)"
          size="small"
        >
          <template v-slot:icon>
            <SvgSprite :name="getIcon(activity.type)" style="width: 16px; height: 16px; color: white" />
          </template>
          
          <div class="d-flex justify-space-between align-center mb-1">
            <h4 class="text-subtitle-1 font-weight-bold">{{ activity.subject }}</h4>
            <span class="text-caption text-grey">{{ formatDate(activity.activity_date) }}</span>
          </div>
          
          <p class="text-body-2 mb-2">{{ activity.description }}</p>
          
          <div class="d-flex align-center gap-2">
            <v-chip size="x-small" :color="getColor(activity.type)" variant="tonal" class="text-capitalize">
              {{ activity.type }}
            </v-chip>
            <v-chip v-if="activity.status === 'completed'" size="x-small" color="success" variant="flat">
              Completed
            </v-chip>
            <v-chip v-else-if="activity.status === 'pending'" size="x-small" color="warning" variant="tonal">
              Pending
            </v-chip>
          </div>
        </v-timeline-item>
      </v-timeline>
    </v-card-text>
  </v-card>
</template>

<style scoped>
.gap-2 {
  gap: 8px;
}
</style>
