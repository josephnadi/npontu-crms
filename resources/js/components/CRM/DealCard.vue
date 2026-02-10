<script setup>
import { Link } from '@inertiajs/vue3';
import SvgSprite from '@/components/shared/SvgSprite.vue';

const props = defineProps({
  deal: {
    type: Object,
    required: true
  }
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: props.deal.currency || 'GHS'
  }).format(value);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString();
};
</script>

<template>
  <v-card class="mb-3 deal-card" elevation="3">
    <v-card-text class="pa-3">
      <div class="d-flex justify-space-between align-start mb-1">
        <div class="d-flex align-center">
          <v-icon size="small" color="primary" class="mr-2">mdi-briefcase-variant-outline</v-icon>
          <h4 class="text-subtitle-1 font-weight-bold text-truncate" style="max-width: 150px;">
            {{ deal.title }}
          </h4>
        </div>
        <v-chip size="x-small" :color="deal.stage?.color || 'primary'" variant="flat">
          {{ deal.probability }}%
        </v-chip>
      </div>
      
      <div class="text-caption text-medium-emphasis mb-2">
        <SvgSprite name="custom-user-fill" class="mr-1" style="width: 14px; height: 14px; vertical-align: middle;" />
        {{ deal.contact ? `${deal.contact.first_name} ${deal.contact.last_name}` : (deal.contact_name || 'No Contact') }}
      </div>

      <div v-if="deal.client" class="text-caption text-medium-emphasis mb-2">
        <SvgSprite name="custom-building" class="mr-1" style="width: 14px; height: 14px; vertical-align: middle;" />
        {{ deal.client.name }}
      </div>
      <div v-else-if="deal.client_name" class="text-caption text-medium-emphasis mb-2">
        <SvgSprite name="custom-building" class="mr-1" style="width: 14px; height: 14px; vertical-align: middle;" />
        {{ deal.client_name }}
      </div>

      <div class="d-flex justify-space-between align-center mt-2">
        <div class="text-subtitle-2 font-weight-bold text-primary">
          {{ formatCurrency(deal.value) }}
        </div>
        <div class="text-caption text-medium-emphasis">
          {{ formatDate(deal.expected_close_date) }}
        </div>
      </div>
    </v-card-text>
    
    <v-divider></v-divider>
    
    <v-card-actions class="pa-2">
      <v-spacer></v-spacer>
      <Link :href="route('crm.deals.show', deal.id)" class="text-decoration-none">
        <v-btn variant="text" size="small" color="primary">View Details</v-btn>
      </Link>
    </v-card-actions>
  </v-card>
</template>

<style scoped>
.deal-card {
  cursor: grab;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  animation: dealEntrance 0.5s cubic-bezier(0.4, 0, 0.2, 1) backwards;
}

.deal-card:active {
  cursor: grabbing;
  transform: scale(0.95);
  box-shadow: 0 5px 10px rgba(0,0,0,0.2) !important;
}

.deal-card:hover {
  transform: translateY(-8px) scale(1.04);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important;
  border-color: rgb(var(--v-theme-primary)) !important;
}

.deal-card:hover .text-primary {
  letter-spacing: 0.5px;
}

@keyframes dealEntrance {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
</style>
