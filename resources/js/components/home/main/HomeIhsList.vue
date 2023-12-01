<template>
  <div class="closest-notice border rounded-xl">
    <ul class="w-max max-h-96 overflow-y-auto rounded-xl invisible-scroll-bar">
      <li
        v-for="(ihs, idx) in ihsList"
        :key="`notice_${String(idx)}`"
        class="px-4 py-2 border-b last:border-b-0 cursor-pointer flex text-gray-500 bg-white hover:bg-slate-100 group"
        @click.stop="selectIhs(ihs.id, ihs.system_name)"
      >
        <div class="flex flex-col text-sm group-hover:text-slate-600">
          <span>{{ ihs.system_name }}</span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { InHouseSystem } from '/@/js/models/User';

interface Props {
  ihsList: InHouseSystem[];
}
defineProps<Props>();

interface Emits {
  (e: 'selectIhs', ihsId, ihsName): void;
}
const emit = defineEmits<Emits>();

const selectIhs = (ihsId: number, ihsName: string) => {
  emit('selectIhs', ihsId, ihsName);
};
</script>
