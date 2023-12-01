<template>
  <div>
    <div class="single_header">{{ title }}</div>
    <div class="single_description">
      <span class="font-14">
        {{ description }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description: string;
}
defineProps<Props>();
</script>

<style scoped>
.font-14 {
  font-size: 14px;
  white-space: pre-line;
  line-height: initial;
}
</style>
