<script setup lang="ts">
import { ref, shallowRef } from 'vue';
import { useCustomizerStore } from '../../../stores/customizer';
import sidebarItems from './sidebarItem';

import NavGroup from './NavGroup/NavGroup.vue';
import NavItem from './NavItem/NavItem.vue';
import NavCollapse from './NavCollapse/NavCollapse.vue';
import NavAccordionSection from './NavAccordionSection/NavAccordionSection.vue';
import ExtraBox from './extrabox/ExtraBox.vue';
import Logo from '../logo/LogoMain.vue';
import AIChatWidget from '../../../components/ai/AIChatWidget.vue';

const customizer = useCustomizerStore();
const sidebarMenu = shallowRef(sidebarItems);
const isChatOpen = ref(false);

/** Only one accordion section open at a time */
const openSection = ref<string | null>(null);

function toggleSection(key: string | undefined) {
  if (!key) return;
  openSection.value = openSection.value === key ? null : key;
}
</script>

<template>
  <v-navigation-drawer
    left
    v-model="customizer.Sidebar_drawer"
    elevation="0"
    rail-width="90"
    mobile-breakpoint="lg"
    width="279"
    app
    class="leftSidebar"
    :rail="customizer.mini_sidebar"
    expand-on-hover
  >
    <div class="d-flex flex-column leftSidebar-inner" style="height: 100%;">
      <div class="pa-4 flex-shrink-0">
        <v-btn
          color="primary"
          block
          class="mt-2"
          @click="isChatOpen = !isChatOpen"
        >
          <v-icon left>mdi-robot</v-icon>
          AI Assistant
        </v-btn>
      </div>

      <transition name="slide-fade">
        <AIChatWidget v-model="isChatOpen" v-if="isChatOpen" />
      </transition>

      <!---Navigation (scrollable) -->
      <perfect-scrollbar class="scrollnavbar flex-grow-1" style="min-height: 0;" :options="{ suppressScrollX: true }">
        <v-list aria-busy="true" class="px-2" aria-label="menu list">
          <template v-for="(item, i) in sidebarMenu" :key="item.sectionKey || item.header || i">
            <NavAccordionSection
              v-if="item.sectionKey && item.sectionHeader && item.children"
              :item="item"
              :is-open="openSection === item.sectionKey"
              @toggle="toggleSection(item.sectionKey)"
            />
            <NavGroup v-else-if="item.header" :item="item" />
            <v-divider v-else-if="item.divider" class="my-4" />
            <NavCollapse v-else-if="item.children" class="leftPadding" :item="item" :level="0" />
            <NavItem v-else :item="item" />
          </template>
        </v-list>
      </perfect-scrollbar>
      <!---ExtraBox pinned to bottom -->
      <div class="px-4 pt-3 pb-3 flex-shrink-0">
        <ExtraBox />
      </div>
      <!---Logo part -->
      <div class="pa-5 flex-shrink-0">
        <Logo />
      </div>
    </div>
  </v-navigation-drawer>
</template>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.3s ease-out;
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}
</style>