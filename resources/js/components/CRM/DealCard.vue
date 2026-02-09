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
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: props.deal.currency || 'USD'
  }).format(value);
};

const formatDate = (date) => {
  if (!date) return 'N/A';
  return new Date(date).toLocaleDateString();
};
</script>

<template>
  <v-card class="mb-3 deal-card" elevation="2">
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
        {{ deal.contact_name || 'No Contact' }}
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
  transition: transform 0.2s;
}
.deal-card:active {
  cursor: grabbing;
}
.deal-card:hover {
  transform: translateY(-2px);
}
</style>
