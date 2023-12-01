<script setup lang="ts">
interface Props {
  diary: any;
  reportPageMode: number;
}

const props = defineProps<Props>();

interface Emits {
  (e: 'update'): void;
}

const emits = defineEmits<Emits>();

const MODE = {
  EDIT: 1,
  CONFIRM: 2,
};
const tenkis = [
  {
    id: 'sunny',
    value: '晴れ',
    fa: 'fa-sun',
  },
  {
    id: 'cloudy',
    value: '曇り',
    fa: 'fa-cloud',
  },
  {
    id: 'rainy',
    value: '雨',
    fa: 'fa-cloud-rain',
  },
  {
    id: 'thunder',
    value: '雷',
    fa: 'fa-thunderstorm',
  },
];
</script>

<template>
    <templat v-for="(a, index) in tenkis" :key="a.id">
      <input
        type="radio"
        :id="a.id"
        :value="a.value"
        @change="$emit('update', $event.target.value)"
        :checked="a.value === diary"
        class="hidden"
      />
      <label
        :for="a.id"
        class="tenki_label border p-2 w-12 inline-block text-center rounded-md mr-2 last:mr-0 text-gray-400 mx-1 hover:text-gray-700"
      >
        <fa :icon="a.fa" />
      </label>

    </templat>
    <span class="ml-2 text-slate-600 text-sm">
      {{ diary }}
    </span>
</template>

<style>
input[type='radio']:checked + .tenki_label {
  background-color: rgb(55 65 81);
}
input[type='radio']:checked + .tenki_label fa,
input[type='radio']:checked + .tenki_label svg {
  color: #fff;
}
</style>
