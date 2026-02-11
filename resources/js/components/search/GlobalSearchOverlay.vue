<script setup lang="ts">
import { ref, watch, computed } from 'vue';
import SvgSprite from '@/components/shared/SvgSprite.vue';
import { useRouter } from 'vue-router';

const props = defineProps<{
  modelValue: boolean;
}>();

const emit = defineEmits(['update:modelValue']);
const router = useRouter();

const searchInput = ref('');

const dialog = ref(props.modelValue);

watch(() => props.modelValue, (newVal) => {
  dialog.value = newVal;
});

watch(dialog, (newVal) => {
  emit('update:modelValue', newVal);
  if (!newVal) {
    searchInput.value = ''; // Clear search input when dialog closes
  }
});

const closeSearch = () => {
  dialog.value = false;
};

// Static list of searchable items (replace with dynamic data from API later)
const searchableItems = ref([
  { title: 'Dashboard', to: '/dashboard' },
  { title: 'Leads', to: '/leads' },
  { title: 'Contacts', to: '/contacts' },
  { title: 'Companies', to: '/companies' },
  { title: 'Deals', to: '/deals' },
  { title: 'Tasks', to: '/tasks' },
  { title: 'Calendar', to: '/calendar' },
  { title: 'Settings', to: '/settings' },
  { title: 'Users', to: '/users' },
  { title: 'Roles & Permissions', to: '/roles-permissions' },
]);

const filteredResults = computed(() => {
  if (!searchInput.value) {
    return [];
  }
  const query = searchInput.value.toLowerCase();
  return searchableItems.value.filter(item =>
    item.title.toLowerCase().includes(query)
  );
});

const selectResult = (item: { title: string; to: string }) => {
  router.push(item.to);
  closeSearch();
};
</script>

<template>
  <v-dialog v-model="dialog" fullscreen :scrim="true" transition="dialog-bottom-transition">
    <v-card>
      <v-toolbar dark color="primary">
        <v-btn icon dark @click="closeSearch">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Global Search</v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-container>
          <v-row>
            <v-col cols="12">
              <v-text-field
                v-model="searchInput"
                label="Search..."
                placeholder="Type to search..."
                variant="outlined"
                clearable
                autofocus
                @keydown.esc="closeSearch"
              >
                <template v-slot:prepend-inner>
                  <SvgSprite name="custom-search" style="width: 20px; height: 20px" />
                </template>
              </v-text-field>
            </v-col>
          </v-row>
          <v-row v-if="searchInput && filteredResults.length > 0">
            <v-col cols="12">
              <v-card>
                <v-card-title>Search Results</v-card-title>
                <v-card-text>
                  <v-list>
                    <v-list-item
                      v-for="item in filteredResults"
                      :key="item.to"
                      :to="item.to"
                      @click="selectResult(item)"
                      link
                    >
                      <v-list-item-title>{{ item.title }}</v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
          <v-row v-else-if="searchInput && filteredResults.length === 0">
            <v-col cols="12">
              <v-card>
                <v-card-title>Search Results</v-card-title>
                <v-card-text>
                  <p>No results found for "{{ searchInput }}".</p>
                </v-card-text>
              </v-card>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.v-dialog.v-dialog--fullscreen {
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  margin: 0;
  max-width: 100%;
  max-height: 100%;
}
</style>