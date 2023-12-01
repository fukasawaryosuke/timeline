<template>
  <div>
    <div
      v-for="(ihs, idx) in ihsList"
      :key="`notice_${String(idx)}`"
      class="menu_page-item"
      @click.stop="selectIhs(ihs.id, ihs.system_name)"
    >
      <div class="menu_page-item-btn">
        <span>{{ ihs.system_name }}</span>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { InHouseSystem } from '/@/js/models/User';

interface Props {
  ihsList: InHouseSystem[];
}
const props = defineProps<Props>();

interface Emits {
  (e: 'selectIhs', ihsId, ihsName): void;
}
const emit = defineEmits<Emits>();

const selectIhs = (ihsId: number, ihsName: string) => {
  emit('selectIhs', ihsId, ihsName);
};
</script>
