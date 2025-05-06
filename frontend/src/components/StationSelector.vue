<template>
    <v-select
      v-model="selected"
      label="Select a Metro Station"
      :items="stationItems"
      @update:model-value="handleSelection"
      return-object
    > 
      
    </v-select>
  </template>
  
  <script setup lang="ts">
  import { type Station, type StationItem } from '@/types/metro';
  import { ref, computed } from 'vue';
  
  interface Props {
    stations: Station[];
  }
  
  const props = defineProps<Props>();
  const emit = defineEmits<{
    (e: 'station-selected', station: Station | null): void;
  }>();
  
  const selected = ref<StationItem | null>(null);
  
  const stationItems = computed<StationItem[]>(() => {
    return props.stations.map(station => ({
      title: station.name,
      value: station,
      raw: station
    }));
  });
  
  const handleSelection = (item: StationItem | null) => {
    selected.value = item;
    emit('station-selected', item ? item.value : null);
  };
  
  </script>