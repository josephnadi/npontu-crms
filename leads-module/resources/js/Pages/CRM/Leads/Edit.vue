<template>
  <DashboardLayout>
    <LeadEdit v-bind="$props" />
  </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/layouts/dashboard/DashboardLayout.vue';
import LeadEdit from '@/views/CRM/Leads/LeadEdit.vue';

defineProps({
  lead: Object,
  leadSources: Array,
  users: Array
});
</script>
