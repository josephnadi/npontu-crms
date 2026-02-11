<template>
  <DashboardLayout>
    <LeadCreate v-bind="$props" />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import LeadCreate from '@/views/CRM/Leads/LeadCreate.vue';

defineProps({
  leadSources: Array,
  users: Array
});
</script>
