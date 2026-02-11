<script setup>
import { ref } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

const props = defineProps({
  leadSources: {
    type: Array,
    default: () => []
  },
  users: {
    type: Array,
    default: () => []
  }
});

const pageTitle = ref('Create Lead - Test');
const breadcrumbs = ref([
  { title: 'CRM', disabled: false, href: '#' },
  { title: 'Leads', disabled: false, href: '/leads' },
  { title: 'Create', disabled: true, href: '#' }
]);

console.log('LeadCreateTest - Props:', props);
console.log('LeadSources:', props.leadSources);
console.log('Users:', props.users);
</script>

<template>
  <BaseBreadcrumb :title="pageTitle" :breadcrumbs="breadcrumbs" />
  
  <v-row>
    <v-col cols="12">
      <UiParentCard title="Create Lead - Debug">
        <h1>Create Lead Page Test</h1>
        <p>If you see this, the page is loading correctly!</p>
        
        <div class="mt-4">
          <h3>Lead Sources:</h3>
          <pre>{{ leadSources }}</pre>
        </div>
        
        <div class="mt-4">
          <h3>Users:</h3>
          <pre>{{ users }}</pre>
        </div>
      </UiParentCard>
    </v-col>
  </v-row>
</template>
