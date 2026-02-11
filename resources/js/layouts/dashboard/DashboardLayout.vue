<script setup lang="ts">
import LoaderWrapper from './LoaderWrapper.vue';
import VerticalSidebarVue from './vertical-sidebar/VerticalSidebar.vue';
import VerticalHeaderVue from './vertical-header/VerticalHeader.vue';
import FooterPanel from './footer/FooterPanel.vue';
import { useCustomizerStore } from '../../stores/customizer';
import { usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';

const customizer = useCustomizerStore();
const page = usePage();

const snackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const flash = computed(() => page.props.flash);

watch(() => page.props.flash, (newFlash) => {
  if (newFlash.success) {
    snackbarText.value = newFlash.success;
    snackbarColor.value = 'success';
    snackbar.value = true;
  } else if (newFlash.error) {
    snackbarText.value = newFlash.error;
    snackbarColor.value = 'error';
    snackbar.value = true;
  } else if (newFlash.warning) {
    snackbarText.value = newFlash.warning;
    snackbarColor.value = 'warning';
    snackbar.value = true;
  } else if (newFlash.info) {
    snackbarText.value = newFlash.info;
    snackbarColor.value = 'info';
    snackbar.value = true;
  }
}, { deep: true, immediate: true });
</script>

<template>
  <v-locale-provider>
    <v-app
      :theme="customizer.actTheme"
      :class="[
        customizer.actTheme,
        customizer.fontTheme,
        customizer.mini_sidebar ? 'mini-sidebar' : '',
        customizer.setHorizontalLayout ? 'horizontalLayout' : 'verticalLayout',
        customizer.inputBg ? 'inputWithbg' : ''
      ]"
    >
      <VerticalSidebarVue />
      <VerticalHeaderVue />

      <v-main class="page-wrapper">
        <v-container fluid>
          <div>
            <!-- Loader start -->
            <LoaderWrapper />
            <!-- Loader end -->
            <slot />
          </div>
        </v-container>
        <v-container fluid class="pt-0">
          <div>
            <FooterPanel />
          </div>
        </v-container>
      </v-main>

      <!-- Global Snackbar for Flash Messages -->
      <v-snackbar
        v-model="snackbar"
        :color="snackbarColor"
        location="top right"
        elevation="24"
      >
        {{ snackbarText }}
        <template v-slot:actions>
          <v-btn
            variant="text"
            @click="snackbar = false"
          >
            Close
          </v-btn>
        </template>
      </v-snackbar>
    </v-app>
  </v-locale-provider>
</template>
