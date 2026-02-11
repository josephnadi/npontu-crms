<script setup>
import { ref } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

const pageTitle = ref('Leads - Test Page');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: true, href: '#' }
]);
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <v-row>
    <v-col cols="12">
      <UiParentCard title="Leads Test">
        <h1>Leads page is working!</h1>
        <p>If you can see this, the route and authentication are working correctly.</p>
      </UiParentCard>
    </v-col>
  </v-row>
</template>
