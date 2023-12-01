<script setup lang="ts">
interface Props {
  str: string;
}
const props = defineProps<Props>();
</script>

<template>
  <span class="text-gray-50 border-b-2 border-cyan-50 pb-1">
    {{ str }}
  </span>
</template>
