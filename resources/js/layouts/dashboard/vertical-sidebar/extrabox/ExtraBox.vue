<script setup lang="ts">
import SvgSprite from '@/components/shared/SvgSprite.vue';

// assets
import avatarGroup from '@/assets/images/welcome-banner.png';
</script>

<template>
  <v-sheet rounded="md" color="rgba(255, 255, 255, 0.05)" class="pa-3 ExtraBox hide-menu text-center pb-3" border style="border-color: rgba(255, 255, 255, 0.15) !important;">
    <div class="d-flex align-center flex-column">
      <v-img :src="avatarGroup" alt="book" class="mb-3" width="169px" cover></v-img>
      <div class="px-3">
        <h5 class="text-h5 mb-1 line-height-none text-white">Able Pro</h5>
        <small class="text-white opacity-70 text-h6">Checkout pro features</small>
      </div>
    </div>
    <div class="mt-5">
      <v-btn
        href="https://codedthemes.com/item/able-pro-vuejs-admin-dashboard/"
        target="_blank"
        variant="flat"
        color="white"
        class="primary-shadow rounded-md"
        style="color: rgb(var(--v-theme-primary)) !important;"
      >
        <template v-slot:prepend>
          <SvgSprite name="custom-logout-1" style="width: 18px; height: 18px" />
        </template>
        Upgrade to pro
      </v-btn>
    </div>
  </v-sheet>
</template>
<style lang="scss">
.ExtraBox {
  position: relative;
  overflow: hidden;
}
.line-height-none {
  line-height: normal;
}
</style>
