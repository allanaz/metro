<template>
  <station-selector 
    :stations="stations" 
    @station-selected="handleStationSelected" 
  />
  
  <train-list
    v-if="selectedStation" 
    :station="selectedStation" 
    :trains="trains" 
    :loading="loading" 
  />
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { type Station, type Train } from '@/types/metro';
import StationSelector from './StationSelector.vue';
import TrainList from './TrainList.vue';

// State variables
const stations = ref<Station[]>([]);
const selectedStation = ref<Station | null>(null);
const trains = ref<Train[]>([]);
const loading = ref(false);

// Get station data
const loadStations = async () => {
  try {
    loading.value = true;
    const response = await axios.get<{data: Station[]}>('http://localhost:8000/stations');
    stations.value = response.data.data;
    loading.value = false;
  } catch (error) {
    console.error('Error loading stations:', error);
    loading.value = false;
  }
};

// Get next trains for a station
const loadNextTrains = async (stationCode: string) => {
  if (!stationCode) return;
  
  try {
    loading.value = true;
    const response = await axios.get<{data: Train[]}>(`http://localhost:8000/nextTrains/${stationCode}`);
    trains.value = response.data.data;
    loading.value = false;
  } catch (error) {
    console.error('Error loading train data:', error);
    trains.value = [];
    loading.value = false;
  }
};

// Process station selection
const handleStationSelected = (station: Station | null) => {
  selectedStation.value = station;
  if (station) {
    loadNextTrains(station.code);
  } else {
    trains.value = [];
  }
};

// Load stations when component is mounted
onMounted(() => {
  loadStations();
});
</script>

<style scoped>
.v-card {
  overflow: hidden;
}
</style>