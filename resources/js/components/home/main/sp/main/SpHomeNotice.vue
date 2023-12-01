<template>
  <div>
    <div v-if="noticeList.list.length == 0">
      <div>
        <p>お知らせはありません</p>
      </div>
    </div>
    <div
      v-for="(notice, idx) in noticeList.list"
      :key="`notice_${String(idx)}`"
      class="menu_page-item"
      @click="selectedNoticeDiary(notice.comment.diary_id)"
    >
      <div
        class="menu_page-item-btn"
        :class="{ active_menu: checkNewNotice(notice.comment.created_at) }"
      >
        <img
          class="icon_trimming"
          :src="`/api/users/getProfileIcon/${String(
            notice.comment.created_by.human_id
          )}`"
          alt="iconImage"
          loading="lazy"
          decoding="async"
        />
        <div>
          <span class="mt-3">{{ noticeMessage(notice) }}</span>
          <div class="comment_date">
            <i class="fa fa-calendar fa_blank" />
            {{ notice.comment.created_at }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import dayjs from 'dayjs';
import { NoticeListModel } from '/@/js/models/Notice';

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
</script>
<style scoped>
.menu_page p {
  padding-top: 30px;
  padding-bottom: 80px;
  color: rgb(156 163 175);
  display: flex;
  justify-content: center;
}
.mt-3 {
  margin-top: 3px;
}
</style>
