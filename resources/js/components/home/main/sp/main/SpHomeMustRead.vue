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
  // const now = dayjs().subtract(1, 'h'); // 本日の日付から1時間以内を最新とする
  const now = dayjs().subtract(1, 'd'); // 本日の日付から1日以内を最新とする
  return createDate.isAfter(now);
});
const mustReadMessage = computed(() => (mustRead: any) => {
  // NOTE: "5分日報"等のdiaryの種類が増える予定
  const title = mustRead.diary.title != '' ? mustRead.diary.title : ' ';
  return `${mustRead.user.user_name}さんが${mustRead.diary.in_house_system.system_name}で必読投稿をしました。`;
});
</script>
<template>
  <div>
    <div v-if="mustReadList.list.length == 0">
      <div>
        <p class="non_post">必読投稿はありません</p>
      </div>
    </div>
    <div
      v-for="(mustRead, idx) in mustReadList.list"
      :key="`mustnotice_${String(idx)}`"
      class="menu_page-item"
      @click="getDiary(mustRead.diary.id)"
    >
      <div
        class="menu_page-item-btn"
        :class="{ active_menu: checkNewMustRead(mustRead.created_at) }"
      >
        <img
          class="icon_trimming"
          :src="`/api/users/getProfileIcon/${String(mustRead.user.human_id)}`"
          alt="iconImage"
          loading="lazy"
          decoding="async"
        />
        <div>
          <div>{{ mustReadMessage(mustRead) }}</div>
          <p v-if="mustRead.diary.title" class="title">
            <span>
              {{ mustRead.diary.title }}
            </span>
          </p>
          <p v-else="!mustRead.diary.title" class="title">
            <span>タイトルなし</span>
          </p>
          <div class="comment_date">
            <i class="fa fa-calendar fa_blank" />
            {{ mustRead.created_at }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.non_post {
  margin-top: 30px;
  margin-bottom: 80px;
  color: rgb(156 163 175);
  display: flex;
  justify-content: center;
}
.title {
  font-size: 15px;
  font-weight: bold;
  padding-top: 8px;
}
</style>
