<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import DealPipeline from '@/components/CRM/DealPipeline.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { computed, onMounted, onUnmounted, watch, ref } from 'vue';

const props = defineProps({
  stages: {
    type: Array,
    required: true
  },
  deals: {
    type: Array,
    required: true
  }
});

const totalDeals = computed(() => props.deals.length);
const totalValue = computed(() => {
  return props.deals.reduce((sum, deal) => sum + parseFloat(deal.value), 0);
});

const getStageMetrics = (stageId) => {
  const stageDeals = props.deals.filter(d => d.deal_stage_id === stageId);
  const value = stageDeals.reduce((sum, deal) => sum + parseFloat(deal.value), 0);
  return {
    count: stageDeals.length,
    value: value
  };
};

// Animation logic for value changes
const animateUpdate = ref(false);
watch([totalDeals, totalValue, () => props.deals], () => {
  animateUpdate.value = true;
  setTimeout(() => {
    animateUpdate.value = false;
  }, 500);
}, { deep: true });

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: 'GHS',
  }).format(value);
};

// Real-time polling
let pollInterval;
onMounted(() => {
  pollInterval = setInterval(() => {
    router.reload({ 
      only: ['deals'],
      preserveScroll: true 
    });
  }, 30000); // Poll every 30 seconds
});

onUnmounted(() => {
  if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
  <Head title="Deal Pipeline" />
  
  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center mb-4">
        <div>
          <h2 class="text-h4 font-weight-bold">Deal Pipeline</h2>
          <p class="text-subtitle-1 text-medium-emphasis">Manage and track your sales opportunities</p>
        </div>
        <div class="d-flex gap-2">
          <Link :href="route('crm.deals.index')" class="text-decoration-none mr-2">
            <v-btn variant="outlined" color="primary">
              <template v-slot:prepend>
                <SvgSprite name="custom-list-outline" style="width: 18px; height: 18px" />
              </template>
              List View
            </v-btn>
          </Link>
          <Link :href="route('crm.deals.create')" class="text-decoration-none">
            <v-btn color="primary">
              <template v-slot:prepend>
                <SvgSprite name="custom-plus" style="width: 18px; height: 18px" />
              </template>
              New Deal
            </v-btn>
          </Link>
        </div>
      </v-col>

      <!-- Pipeline Dashboard Metrics -->
      <v-col cols="12" md="3">
        <v-card class="pa-4 metric-card" elevation="2">
          <div class="d-flex align-center mb-1">
            <v-avatar color="blue-lighten-5" size="32" class="mr-2 icon-avatar">
              <v-icon color="primary" size="small">mdi-briefcase-outline</v-icon>
            </v-avatar>
            <span class="text-caption font-weight-medium text-medium-emphasis">Total Deals</span>
          </div>
          <div class="text-h5 font-weight-bold counter-value" :class="{ 'counter-value-updated': animateUpdate }">{{ totalDeals }}</div>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="pa-4 metric-card" elevation="2">
          <div class="d-flex align-center mb-1">
            <v-avatar color="green-lighten-5" size="32" class="mr-2 icon-avatar">
              <v-icon color="success" size="small">mdi-currency-usd</v-icon>
            </v-avatar>
            <span class="text-caption font-weight-medium text-medium-emphasis">Pipeline Value</span>
          </div>
          <div class="text-h5 font-weight-bold text-success counter-value" :class="{ 'counter-value-updated': animateUpdate }">{{ formatCurrency(totalValue) }}</div>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="pa-4 metric-card" elevation="2">
          <div class="d-flex align-center mb-1">
            <v-avatar color="orange-lighten-5" size="32" class="mr-2 icon-avatar">
              <v-icon color="warning" size="small">mdi-trending-up</v-icon>
            </v-avatar>
            <span class="text-caption font-weight-medium text-medium-emphasis">Avg. Deal Value</span>
          </div>
          <div class="text-h5 font-weight-bold counter-value" :class="{ 'counter-value-updated': animateUpdate }">{{ formatCurrency(totalDeals ? totalValue / totalDeals : 0) }}</div>
        </v-card>
      </v-col>

      <v-col cols="12" md="3">
        <v-card class="pa-4 metric-card" elevation="2">
          <div class="d-flex align-center mb-1">
            <v-avatar color="purple-lighten-5" size="32" class="mr-2 icon-avatar">
              <v-icon color="secondary" size="small">mdi-check-circle-outline</v-icon>
            </v-avatar>
            <span class="text-caption font-weight-medium text-medium-emphasis">Active Stages</span>
          </div>
          <div class="text-h5 font-weight-bold counter-value">{{ stages.length }}</div>
        </v-card>
      </v-col>

      <!-- Stage Specific Dashboard Cards -->
      <v-col v-for="stage in stages" :key="stage.id" cols="12" sm="6" md="3" lg="2">
        <v-card class="pa-3 metric-card stage-metric-card" elevation="2">
          <div class="d-flex align-center mb-1">
            <v-avatar :color="`${stage.color}-lighten-5`" size="28" class="mr-2 icon-avatar">
              <v-icon :color="stage.color" size="x-small">mdi-circle-slice-8</v-icon>
            </v-avatar>
            <span class="text-caption font-weight-bold text-truncate" :style="`color: rgb(var(--v-theme-${stage.color}))`" style="max-width: 100px;">
              {{ stage.name }}
            </span>
          </div>
          <div class="d-flex flex-column">
            <div class="text-subtitle-1 font-weight-bold counter-value" :class="{ 'counter-value-updated': animateUpdate }">
              {{ getStageMetrics(stage.id).count }} Deals
            </div>
            <div class="text-caption font-weight-medium text-medium-emphasis counter-value" :class="{ 'counter-value-updated': animateUpdate }">
              {{ formatCurrency(getStageMetrics(stage.id).value) }}
            </div>
          </div>
        </v-card>
      </v-col>

      <v-col cols="12">
        <v-card variant="flat" class="mt-2">
          <DealPipeline :stages="stages" :deals="deals" />
        </v-card>
      </v-col>
    </v-row>
  </DashboardLayout>
</template>


<style scoped>
.gap-2 {
  gap: 8px;
}

.metric-card {
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
  animation: metricEntrance 0.6s cubic-bezier(0.4, 0, 0.2, 1) backwards;
}

.metric-card:hover {
  transform: translateY(-8px) scale(1.02);
  box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
  border-color: rgb(var(--v-theme-primary)) !important;
}

.stage-metric-card:hover {
  border-color: currentColor !important;
}

.icon-avatar {
  transition: all 0.3s ease;
}

.metric-card:hover .icon-avatar {
  transform: scale(1.1) rotate(5deg);
}

.counter-value {
  transition: all 0.3s ease;
  display: inline-block;
}

/* Flash effect when value changes */
.counter-value-updated {
  animation: valueUpdate 0.5s ease-out;
}

@keyframes metricEntrance {
  from {
    opacity: 0;
    transform: translateY(20px) scale(0.98);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes valueUpdate {
  0% { transform: scale(1); }
  50% { transform: scale(1.1); color: rgb(var(--v-theme-primary)); }
  100% { transform: scale(1); }
}
</style>
