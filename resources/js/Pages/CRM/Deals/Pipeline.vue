<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import DealPipeline from '@/Components/CRM/DealPipeline.vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';

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
</script>

<template>
  <Head title="Deal Pipeline" />
  
  <DashboardLayout>
    <v-row>
      <v-col cols="12" class="d-flex justify-space-between align-center">
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

      <v-col cols="12">
        <v-card variant="flat">
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
</style>
