<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  engagements: {
    type: Array,
    required: true
  }
});

const deleteEngagement = (id) => {
  if (confirm('Are you sure you want to delete this engagement?')) {
    router.delete(route('crm.engagements.destroy', id), {
      preserveScroll: true
    });
  }
};

const getIcon = (type) => {
  switch (type) {
    case 'email': return 'custom-mail';
    case 'call': return 'custom-phone';
    case 'meeting': return 'custom-users';
    case 'webinar': return 'custom-video';
    case 'form_submitted': return 'custom-file-text';
    case 'content_viewed': return 'custom-eye';
    default: return 'custom-calendar';
  }
};

const getColor = (type) => {
  switch (type) {
    case 'email': return 'warning';
    case 'call': return 'info';
    case 'meeting': return 'primary';
    case 'webinar': return 'secondary';
    case 'form_submitted': return 'success';
    case 'content_viewed': return 'info';
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
      <div v-if="engagements.length === 0" class="text-center py-8">
        <v-icon icon="mdi-history" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
        <p class="text-grey">No engagements recorded yet.</p>
      </div>
      
      <v-timeline side="end" align="start" density="compact">
        <v-timeline-item
          v-for="engagement in engagements"
          :key="engagement.id"
          :dot-color="getColor(engagement.type)"
          size="small"
        >
          <template v-slot:icon>
            <SvgSprite :name="getIcon(engagement.type)" style="width: 16px; height: 16px; color: white" />
          </template>
          
          <div class="d-flex justify-space-between align-start mb-1">
            <div>
              <div class="d-flex align-center">
                <h4 class="text-subtitle-1 font-weight-bold mr-2">{{ engagement.subject }}</h4>
                <v-chip size="x-small" :color="engagement.score >= 50 ? 'success' : 'grey'" variant="tonal">
                  Score: {{ engagement.score }}
                </v-chip>
              </div>
              <span class="text-caption text-grey">{{ formatDate(engagement.engagement_date) }}</span>
            </div>
            <v-menu>
              <template v-slot:activator="{ props }">
                <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
              </template>
              <v-list size="small">
                <v-list-item @click="deleteEngagement(engagement.id)" prepend-icon="mdi-delete" color="error">
                  <v-list-item-title>Delete</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </div>
          
          <p class="text-body-2 text-medium-emphasis mb-2" v-if="engagement.description">
            {{ engagement.description }}
          </p>
          
          <div v-if="engagement.metadata" class="d-flex flex-wrap" style="gap: 4px;">
            <v-chip v-for="(val, key) in engagement.metadata" :key="key" size="x-small" variant="outlined">
              {{ key }}: {{ val }}
            </v-chip>
          </div>
        </v-timeline-item>
      </v-timeline>
    </v-card-text>
  </v-card>
</template>
