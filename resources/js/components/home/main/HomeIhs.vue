<template>
  <div class="w-80 border bg-white rounded-xl py-2 mb-2">
    <div v-if="system_name" class="px-8 py-2">
      <p class="text-lg font-bold text-gray-700">{{ system_name }}</p>
    </div>
    <div v-if="icon_path" class="px-8 py-2">
      <img class="max-h-24" :src="icon_path" />
    </div>
    <div v-if="description" class="px-8 py-2">
      <pre
        class="overflow-y-scroll text-slate-600 text-sm whitespace-pre-line break-words"
        >{{ description }}</pre
      >
    </div>
  </div>
</template>
<script setup lang="ts">
import axios from 'axios';
import { InHouseSystemModel } from '/@/js/models/InHouseSystem';
import { watch, ref, toRefs } from 'vue';
import useAxios from '/@/js/compositions/UseAxios';
interface Props {
  selectedIhsId: number;
}
const props = defineProps<Props>();
const system_name = ref<string>('');
const icon_path = ref<string>('');
const description = ref<string>('');
const { axiosErrorHandling, errorToast } = useAxios();

watch(
  () => props.selectedIhsId,
  (ihsId) => {
    if (ihsId) {
      axios
        .get('/api/ihs/' + ihsId, {})
        .then((res) => {
          const ihs = new InHouseSystemModel(res.data.data);
          system_name.value = ihs.system_name;
          icon_path.value = ihs.icon_path;
          description.value = ihs.description;
        })
        .catch((err) => {
          const result = axiosErrorHandling(err);
          errorToast(result.code, result.message);
        });
    }
  },
  { immediate: true }
);
</script>
<style scoped>
pre {
  font-family: initial;
}
</style>
