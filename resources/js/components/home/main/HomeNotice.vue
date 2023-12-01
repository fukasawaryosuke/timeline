<template>
  <div class="closest-notice w-96 border bg-white rounded-xl">
    <ul
      class="max-h-[calc(24rem_-_2px)] overflow-y-auto rounded-xl invisible-scroll-bar"
    >
      <!-- <li class="bg-white text-center px-4 py-2 border-b sticky top-0">
        <span class="font-bold">お知らせ</span>
      </li> -->
      <li v-if="noticeList.list.length == 0">
        <div class="px-4 py-2 flex flex-col text-sm">
          <p>お知らせはありません</p>
        </div>
      </li>
      <li
        v-for="(notice, idx) in noticeList.list"
        :key="`notice_${String(idx)}`"
        class="px-4 py-2 border-b last:border-b-0 cursor-pointer flex text-gray-500"
        :class="{ 'bg-orange-100': checkNewNotice(notice.comment.created_at) }"
        @click="selectedNoticeDiary(notice.comment.diary_id)"
      >
        <img
          class="w-[4.75rem] h-11 rounded-full border object-cover"
          :src="`/api/users/getProfileIcon/${String(
            notice.comment.created_by.human_id
          )}`"
          alt="iconImage"
          loading="lazy"
          decoding="async"
        />
        <div class="pl-5 flex flex-col text-sm">
          <p>
            <span>{{ noticeMessage(notice) }}</span>
          </p>
          <span class="leading-7 text-xs">
            {{ notice.comment.created_at }}
          </span>
        </div>
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { onMounted, computed } from 'vue';
import useNotice from '../../../compositions/UseNotice';
import dayjs from 'dayjs';
// const { noticeList, getNotice } = useNotice();
import { NoticeListModel } from '../../../models/Notice';

interface Props {
  noticeList: NoticeListModel;
}
defineProps<Props>();

interface Emits {
  (e: 'selectNoticeDiary', diaryId): void;
}
const emit = defineEmits<Emits>();

const selectedNoticeDiary = (diaryId: number) => {
  emit('selectNoticeDiary', diaryId);
};

const noticeMessage = computed(() => (notice: any) => {
  const dateStr = dayjs(notice.comment.diary.created_at).format('MM月DD日');
  if (notice.notification_type === 1) {
    return `${dateStr}の${notice.system_name}で${notice.comment.created_by.user_name}さんからメンションがありました。`;
  } else if (notice.notification_type === 2) {
    return `${dateStr}の${notice.system_name}で${notice.comment.created_by.user_name}さんがコメントしました。`;
  } else {
    return '';
  }
});

const checkNewNotice = computed(() => (createDateStr: any) => {
  const createDate = dayjs(createDateStr);
  const now = dayjs().subtract(1, 'd'); // 本日の日付から1日以内を最新とする
  return createDate.isAfter(now);
});

// onMounted(() => {
//   getNotice();
// });
</script>
