<template>
    <div>
      <v-data-table
        v-if="trains.length > 0"
        :headers="headers"
        :items="trains"
        hide-default-footer
        class="mt-2"
        density="compact"
      >
        <template v-slot:item.line="{ item }">
          <line-chip :line="item.line" size="small" />
        </template>
        
        <template v-slot:item.arrival="{ item }">
          <span>{{ item.arrival }}</span>
        </template>
      </v-data-table>
      
      <v-alert v-else-if="station && !loading" type="info" class="mt-4">
        No train information available for this station.
      </v-alert>
      
      <v-progress-linear v-if="loading" indeterminate color="primary" class="mt-4"></v-progress-linear>
    </div>
  </template>
  
  <script setup lang="ts">
  import { 
    type Station, 
    type Train, 
    type TrainHeader
  } from '@/types/metro';
  import LineChip from './LineChip.vue';
  
  interface Props {
    station: Station | null;
    trains: Train[];
    loading: boolean;
  }
  
  const props = defineProps<Props>();
  
  const headers: TrainHeader[] = [
    { title: 'Line', key: 'line', width: '100px' },
    { title: 'Cars', key: 'length', width: '80px' },
    { title: 'Destination', key: 'destination' },
    { title: 'Arrival', key: 'arrival', width: '100px' }
  ];
  </script>
  
  <style scoped>
  .v-data-table {
    border-radius: 8px;
    overflow: hidden;
  }
  </style>