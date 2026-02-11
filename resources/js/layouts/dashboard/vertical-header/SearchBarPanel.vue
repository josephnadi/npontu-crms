<script setup lang="ts">
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { onMounted, onUnmounted, ref } from 'vue';

const emit = defineEmits(['open-search']);

const searchBarRef = ref<HTMLInputElement | null>(null);

const handleKeyDown = (event: KeyboardEvent) => {
  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault();
    emit('open-search');
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown);
});
</script>

<template>
  <!-- ---------------------------------------------- -->
  <!-- searchbar -->
  <!-- ---------------------------------------------- -->
  <v-text-field ref="searchBarRef" persistent-placeholder placeholder="Ctrl + k" color="primary" variant="outlined" hide-details>
    <template v-slot:prepend-inner>
      <div class="text-lightText d-flex align-center">
        <SvgSprite name="custom-search" style="width: 16px; height: 16px" />
      </div>
    </template>
  </v-text-field>
</template>
