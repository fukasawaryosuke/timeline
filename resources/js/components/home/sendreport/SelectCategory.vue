<template>
  <div class="w-full items-center flex justify-center">
    <div
      class="overflow-y-auto w-192 border rounded-lg p-4 mb-4 justify-center bg-white"
    >
      <div class="font-bold text-slate-700">{{ title }}</div>
      <div>
        <div class="break-words whitespace-pre-line text-sm pt-2">
          {{ description }}
        </div>
      </div>
    </div>
  </div>

  <!-- <div class="z-50 fixed w-screen h-screen ">
    modal
  </div> -->
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description: string;
}
defineProps<Props>();
</script>

