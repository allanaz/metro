<template>
  <v-container class="fill-height" max-width="900">
    <div>
      <v-row>
        <v-col cols="12">
          <v-card
            class="py-4"
            color="surface-variant"
            image="https://cdn.vuetifyjs.com/docs/images/one/create/feature.png"
            rounded="lg"
            variant="tonal"
          >
            <v-select label="Select" :items="stations">
            </v-select>
            <v-data-table :items="helloWorldText" hide-default-footer></v-data-table>
          </v-card>
        </v-col>
      </v-row>
    </div>
  </v-container>
</template>

<script setup lang="ts">
  import axios from 'axios';
  import { ref } from 'vue';

  const helloWorldText = ref([]);

  const loadText = async () => {
    const response = await axios.get('http://localhost:8000/nextTrains/B03');
    helloWorldText.value = response.data['data'];
  };

  const stations = ref([]);

  const loadStations = async () => {
    const response = await axios.get('http://localhost:8000/stations');
    stations.value = response.data['data'];
  }
  loadStations();
  loadText();

</script>
