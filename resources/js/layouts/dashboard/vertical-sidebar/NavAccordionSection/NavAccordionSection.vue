<script setup>
import { computed } from 'vue';
import NavItem from '../NavItem/NavItem.vue';

const props = defineProps({
  item: { type: Object, required: true },
  isOpen: { type: Boolean, default: false }
});

defineEmits(['toggle']);

const hasSubCaption = computed(() => !!props.item.sectionSubCaption);
</script>

<template>
  <div class="nav-accordion-section">
    <div
      class="accordion-header"
      :class="{ 'accordion-header--open': isOpen }"
      role="button"
      tabindex="0"
      aria-expanded="isOpen"
      @click="$emit('toggle')"
      @keydown.enter.prevent="$emit('toggle')"
      @keydown.space.prevent="$emit('toggle')"
    >
      <span class="accordion-header__title">{{ item.sectionHeader }}</span>
      <span v-if="hasSubCaption" class="accordion-header__subtitle hide-menu">{{ item.sectionSubCaption }}</span>
      <v-icon
        class="accordion-header__icon"
        :class="{ 'accordion-header__icon--open': isOpen }"
        size="small"
      >
        mdi-chevron-down
      </v-icon>
    </div>

    <v-expand-transition>
      <div v-if="isOpen" class="accordion-content">
        <div
          v-for="(child, i) in item.children"
          :key="child.to || child.title"
          class="accordion-item"
          :style="{ '--waterfall-delay': `${i * 40}ms` }"
        >
          <NavItem :item="child" :level="0" />
        </div>
      </div>
    </v-expand-transition>
  </div>
</template>

<style scoped>
.nav-accordion-section {
  margin-bottom: 10px;
}

.accordion-header {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
  padding: 12px 16px;
  margin: 6px 12px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  color: rgb(var(--v-theme-lightText));
  position: relative;
  padding-right: 40px;
}

.accordion-header:hover {
  background: rgba(var(--v-theme-primary), 0.08);
  color: rgb(var(--v-theme-primary));
}

.accordion-header--open {
  background: rgba(var(--v-theme-primary), 0.1);
  color: rgb(var(--v-theme-primary));
}

.accordion-header__title {
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.accordion-header__subtitle {
  font-size: 0.7rem;
  opacity: 0.85;
  text-transform: none;
  font-weight: 500;
}

.accordion-header__icon {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.accordion-header__icon--open {
  transform: translateY(-50%) rotate(180deg);
}

.accordion-content {
  overflow: hidden;
  padding-left: 8px;
  padding-top: 4px;
  padding-bottom: 4px;
}

.accordion-item {
  animation: waterfall-in 0.35s cubic-bezier(0.4, 0, 0.2, 1) backwards;
  animation-delay: var(--waterfall-delay, 0ms);
}

@keyframes waterfall-in {
  from {
    opacity: 0;
    transform: translateY(-6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
