<script setup lang="ts">
import { MustReadListModel } from '/@/js/models/MustRead';

interface Props {
  mustReadList: MustReadListModel;
  getDiary: (id: number) => void;
}
defineProps<Props>();
</script>
<template>
  <div
    class="w-80 border bg-white rounded-xl overflow-hidden"
    v-if="mustReadList.list.length > 0"
  >
    <div class="py-2 text-xl font-bold text-center text-gray-700 border-b">
      必読投稿
    </div>
    <ul>
      <li
        class="p-3 border-b last:border-b-0 cursor-pointer hover:bg-slate-100"
        v-for="mustRead in mustReadList.list"
        @click="getDiary(mustRead.diary.id)"
      >
        <dl class="flex flex-col">
          <dt class="font-semibold text-slate-600">
            {{ mustRead.diary.in_house_system.system_name }}
          </dt>
          <dd class="py-1 font-bold text-xl text-gray-700">
            {{ mustRead.diary.title }}
          </dd>
        </dl>
        <dl class="flex flex-col">
          <dt class="text-sm text-gray-500">
            {{ mustRead.user.user_name }}
          </dt>
          <dd class="text-xs text-gray-400">{{ mustRead.created_at }}</dd>
        </dl>
      </li>
    </ul>
  </div>
</template>
