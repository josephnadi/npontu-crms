<template>
  <DashboardLayout>
    <LeadIndex v-bind="$props" />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import LeadIndex from '@/views/CRM/Leads/LeadIndex.vue';

defineProps({
  leads: Object,
  leadSources: Array,
  filters: Object,
  dealStages: Array
});
</script>
