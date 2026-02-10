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

const isUpdating = ref(false);

const onMove = (evt) => {
  const { added, removed, moved } = evt;
  
  if (added) {
    const deal = added.element;
    const toStageId = Object.keys(localDealsByStage.value).find(id => 
      localDealsByStage.value[id].includes(deal)
    );

    isUpdating.value = true;
    router.put(window.route('crm.deals.updateStage', deal.id), {
      deal_stage_id: toStageId
    }, {
      preserveScroll: true,
      onFinish: () => {
        isUpdating.value = false;
      },
      onSuccess: () => {
        // Data is refreshed via props
      }
    });
  }
};
</script>

<template>
  <div class="pipeline-wrapper pa-4 position-relative">
    <v-fade-transition>
      <div v-if="isUpdating" class="updating-overlay d-flex align-center justify-center">
        <v-chip color="primary" class="px-4 py-2" elevation="4">
          <v-progress-circular indeterminate size="16" width="2" class="mr-2"></v-progress-circular>
          Updating Pipeline...
        </v-chip>
      </div>
    </v-fade-transition>
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
          </v-card-title>
          
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

.updating-overlay {
  position: absolute;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 100;
  pointer-events: none;
}
</style>
