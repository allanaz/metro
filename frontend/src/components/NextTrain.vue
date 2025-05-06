<template>
  <v-container class="train-app py-4">
    <v-card class="mx-auto" max-width="1200">
      <v-card-title class="text-h5 font-weight-bold">
        Train Schedule
      </v-card-title>
      
      <v-card-text>
        <!-- Station selection component -->
        <station-selector
          :stations="stations"
          @station-selected="handleStationSelected"
          class="mb-4"
        />
        
        <!-- Main content area - only shown when a station is selected -->
        <div v-if="selectedStation" class="mt-4">
          <v-divider class="mb-4"></v-divider>
          
          <!-- Primary station trains -->
          <v-sheet class="mb-4 pa-2" rounded>
            <train-list
              :station="selectedStation"
              :trains="trains"
              :loading="loading"
            />
          </v-sheet>
          
          <!-- Connected station trains (only shown if applicable) -->
          <v-sheet v-if="selectedStation.stationTogether1" class="pa-2 mt-4" rounded>
            <v-chip color="purple" class="mb-2">Transfer Station</v-chip>
            <train-list
              :station="selectedStation"
              :trains="connectedTrains"
              :loading="connectedLoading"
            />
          </v-sheet>
        </div>
        
        <!-- No station selected message -->
        <v-alert
          v-else
          type="info"
          class="mt-4"
        >
          Please select a station to view train information
        </v-alert>
      </v-card-text>
    </v-card>
  </v-container>
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
const connectedTrains = ref<Train[]>([]);
const loading = ref(false);
const connectedLoading = ref(false);

// Get station data
const loadStations = async () => {
  try {
    loading.value = true;
    const response = await axios.get<{data: Station[]}>('http://localhost:8000/stations');
    const uniqueStations = Array.from(
      new Map(response.data.data.map(station => [station.name, station])).values()
    );
    stations.value = uniqueStations;
    loading.value = false;
  } catch (error) {
    console.error('Error loading stations:', error);
    loading.value = false;
  }
};

// Get next trains for a station
const loadNextTrains = async (stationCode: string, isConnected: boolean = false) => {
  if (!stationCode) return;
  
  try {
    loading.value = true;
    connectedLoading.value = isConnected;
    const stationResponse = await axios.get<{data: Train[]}>(`http://localhost:8000/nextTrains/${stationCode}`);
    if (isConnected){
      connectedTrains.value = stationResponse.data.data;
      connectedLoading.value = false;
    } else {
      trains.value = stationResponse.data.data;
      loading.value = false;
    }
  } catch (error) {
    console.error('Error loading train data:', error);
    trains.value = [];
    loading.value = false;
    connectedLoading.value = false;
  }
};

// Process station selection
const handleStationSelected = (station: Station | null) => {
  selectedStation.value = station;
  trains.value = [];
  connectedTrains.value = [];
  if (station) {
    loadNextTrains(station.code);
    if (station.stationTogether1 != null){
      loadNextTrains(station.stationTogether1, true)
    }
  } else {
    trains.value = [];
  }
};

// Load stations when component is mounted
onMounted(() => {
  loadStations();
});
</script>