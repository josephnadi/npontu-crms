<template>
  <DashboardLayout>
    <LeadShow v-bind="$props" />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import LeadShow from '@/views/CRM/Leads/LeadShow.vue';

defineProps({
  lead: Object,
  dealStages: Array
});
</script>
