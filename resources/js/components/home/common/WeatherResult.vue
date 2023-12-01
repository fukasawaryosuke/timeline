<script setup lang="ts">
interface Props {
  diary: any;
}

const props = defineProps<Props>();
</script>

<template>
  <div class="flex items-center whitespace-pre-wrap break-words sp">
    <div v-if="!diary" class="text-sm font-normal text-gray-700">
      選択していない
    </div>
    <div
      v-else
      class="border p-2 w-12 inline-block text-center rounded-md mr-2 text-gray-700 sp-mr"
    >
      <template v-if="diary == '晴れ'">
        <fa icon="fa-sun"></fa>
      </template>
      <template v-if="diary == '曇り'">
        <fa icon="fa-cloud"></fa>
      </template>
      <template v-if="diary == '雨'">
        <fa icon="fa-cloud-rain"></fa>
      </template>
      <template v-if="diary == '雷'">
        <fa icon="fa-thunderstorm"></fa>
      </template>
    </div>
    <span class="ml-2 text-slate-600 text-sm">
      {{ diary }}
    </span>
  </div>
</template>

<style scoped>
@media screen and (max-width: 767px) {
  .sp {
    display: flex;
  }
  .sp-mr {
    margin-right: 6px;
  }
}
</style>
