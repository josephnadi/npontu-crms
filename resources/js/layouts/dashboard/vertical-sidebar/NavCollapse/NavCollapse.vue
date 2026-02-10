<script setup>
import SvgSprite from '@/components/shared/SvgSprite.vue';
import NavItem from '../NavItem/NavItem.vue';

const props = defineProps({ item: Object, level: Number });
</script>

<template>
  <!-- ---------------------------------------------- -->
  <!---Item Childern -->
  <!-- ---------------------------------------------- -->
  <v-list-group no-action>
    <!-- ---------------------------------------------- -->
    <!---Dropdown  -->
    <!-- ---------------------------------------------- -->
    <template v-slot:activator="{ props }">
      <v-list-item v-bind="props" :value="item.title" rounded>
        <!---Icon  -->
        <template v-slot:prepend>
          <SvgSprite :name="item.icon || ''" :level="level" />
        </template>
        <!---Title  -->
        <v-list-item-title class="mr-auto">{{ item.title }}</v-list-item-title>
        <!---If Caption-->
        <v-list-item-subtitle v-if="item.subCaption" class="text-caption mt-0 hide-menu">
          {{ item.subCaption }}
        </v-list-item-subtitle>
      </v-list-item>
    </template>
    <!-- ---------------------------------------------- -->
    <!---Sub Item-->
    <!-- ---------------------------------------------- -->
    <template v-for="(subitem, i) in item.children" :key="i">
      <NavCollapse :item="subitem" v-if="subitem.children" :level="props.level + 1" />
      <NavItem :item="subitem" :level="props.level + 1" v-else></NavItem>
    </template>
  </v-list-group>

  <!-- ---------------------------------------------- -->
  <!---End Item Sub Header -->
  <!-- ---------------------------------------------- -->
</template>

<style scoped>
.v-list-item {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.v-list-item:hover {
  background-color: rgba(var(--v-theme-primary), 0.08) !important;
}

.v-list-item:hover :deep(.pc-icon) {
  transform: scale(1.1) translateX(2px);
}

.v-list-item:hover .v-list-item-title {
  transform: translateX(4px);
}

:deep(.pc-icon),
.v-list-item-title {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Rotate arrow icon on hover/open if needed, but Vuetify handles group arrows */
</style>
