<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import draggable from 'vuedraggable';
import DealCard from './DealCard.vue';

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

// Group deals by stage
const localDealsByStage = ref({});

// Initialize stages with deals
props.stages.forEach(stage => {
  localDealsByStage.value[stage.id] = props.deals.filter(deal => deal.deal_stage_id === stage.id);
});

const onMove = (evt) => {
  const { added, removed, moved } = evt;
  
  if (added) {
    const deal = added.element;
    const toStageId = Object.keys(localDealsByStage.value).find(id => 
      localDealsByStage.value[id].includes(deal)
    );

    router.put(window.route('crm.deals.updateStage', deal.id), {
      deal_stage_id: toStageId
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: refresh data or show notification
      }
    });
  }
};

const getStageTotal = (stageId) => {
  const deals = localDealsByStage.value[stageId] || [];
  const total = deals.reduce((sum, deal) => sum + parseFloat(deal.value), 0);
  return new Intl.NumberFormat('en-GH', {
    style: 'currency',
    currency: 'GHS'
  }).format(total);
};
</script>

<template>
  <div class="pipeline-wrapper pa-4">
    <div class="pipeline-container d-flex overflow-x-auto pb-4">
      <div 
        v-for="stage in stages" 
        :key="stage.id" 
        class="stage-column mx-2"
        style="min-width: 300px; width: 300px;"
      >
        <v-card color="grey-lighten-4" flat class="h-100">
          <v-card-title class="d-flex justify-space-between align-center py-3 px-4">
            <span class="text-subtitle-1 font-weight-bold">{{ stage.name }}</span>
            <v-chip size="small" :color="stage.color" variant="tonal">
              {{ localDealsByStage[stage.id]?.length || 0 }}
            </v-chip>
          </v-card-title>
          
          <v-card-subtitle class="px-4 pb-2 text-caption text-medium-emphasis">
            Total: {{ getStageTotal(stage.id) }}
          </v-card-subtitle>

          <v-divider></v-divider>

          <v-card-text class="pa-2 h-100">
            <draggable
              v-model="localDealsByStage[stage.id]"
              group="deals"
              item-key="id"
              class="draggable-list"
              @change="onMove"
              :animation="200"
              ghost-class="ghost-card"
            >
              <template #item="{ element }">
                <DealCard :deal="element" />
              </template>
            </draggable>
          </v-card-text>
        </v-card>
      </div>
    </div>
  </div>
</template>

<style scoped>
.pipeline-container {
  min-height: calc(100vh - 250px);
}
.draggable-list {
  min-height: 100px;
  height: 100%;
}
.ghost-card {
  opacity: 0.5;
  background: #c8ebfb;
}
.pipeline-wrapper::-webkit-scrollbar {
  height: 8px;
}
.pipeline-wrapper::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}
</style>
