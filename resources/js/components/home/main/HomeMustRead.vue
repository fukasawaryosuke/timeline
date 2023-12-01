<script setup lang="ts">
import dayjs from 'dayjs';
import { computed } from 'vue';
import { MustReadListModel } from '/@/js/models/MustRead';

interface Props {
  mustReadList: MustReadListModel;
  getDiary: (id: number) => void;
}
defineProps<Props>();

const checkNewMustRead = computed(() => (createDateStr: any) => {
  const createDate = dayjs(createDateStr);
  const now = dayjs().subtract(1, 'd'); // 本日の日付から1時間以内を最新とする
  // const now = dayjs().subtract(1, 'd'); // 本日の日付から1日以内を最新とする
  return createDate.isAfter(now);
});
const mustReadMessage = computed(() => (mustRead: any) => {
  // NOTE: "5分日報"等のdiaryの種類が増える予定
  const title = mustRead.diary.title != '' ? mustRead.diary.title : ' ';
  return `${mustRead.user.user_name}さんが${mustRead.diary.in_house_system.system_name}で必読投稿をしました。`;
});
</script>
<template>
  <div class="closest-notice w-96 border bg-white rounded-xl">
    <ul
      class="max-h-[calc(24rem_-_2px)] overflow-y-auto rounded-xl invisible-scroll-bar"
    >
      <li v-if="mustReadList.list.length == 0">
        <div class="px-4 py-2 flex flex-col text-sm">
          <p>必読投稿はありません</p>
        </div>
      </li>
      <li
        v-for="(mustRead, idx) in mustReadList.list"
        :key="`mustnotice_${String(idx)}`"
        class="px-4 py-2 border-b last:border-b-0 cursor-pointer flex text-gray-500"
        :class="{ 'bg-orange-100': checkNewMustRead(mustRead.created_at) }"
        @click="getDiary(mustRead.diary.id)"
      >
        <img
          class="w-[3.75rem] h-11 rounded-full border object-cover"
          :src="`/api/users/getProfileIcon/${String(mustRead.user.human_id)}`"
          alt="iconImage"
          loading="lazy"
          decoding="async"
        />
        <div class="pl-5 flex flex-col text-sm whitespace-pre-line">
          <p>
            <span>{{ mustReadMessage(mustRead) }}</span>
          </p>
          <p v-if="mustRead.diary.title" class="text-[16px] mt-2">
            <span>
              {{ mustRead.diary.title }}
            </span>
          </p>
          <span class="leading-7 text-xs">
            {{ mustRead.created_at }}
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>
