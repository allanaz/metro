<template>
    <v-chip :color="color" :size="size" variant="flat">{{ line }}</v-chip>
  </template>
  
  <script setup lang="ts">
  import { computed } from 'vue';
  import { getLineColor } from '../types/metro';
  
  interface Props {
    line: string | null;
    size?: 'x-small' | 'small' | 'default' | 'large' | 'x-large';
  }
  
  const props = withDefaults(defineProps<Props>(), {
    size: 'default'
  });
  
  const color = computed(() => getLineColor(props.line));
  </script>