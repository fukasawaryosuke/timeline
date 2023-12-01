<!-- NOTE: 画像取得APIが作成されたら各human_id等でsrcを設定 -->
<template>
  <div>
    <div>
      <div class="read_icons">
        <img
          class="history-image"
          v-for="(b, i) in browsFirstFiveList"
          :key="String(`browshistory${i}`)"
          :src="`/api/users/getProfileIcon/${String(b.human_id)}`"
          :title="b.name"
          loading="lazy"
          decoding="async"
        />
      </div>
      <div class="view_num_txt">
        <label @click="toggleHistory(diaryId)">
          {{ browsCount }}人が閲覧しました。
          <i icon="fa fa-clock-o" />
        </label>
      </div>
    </div>
    <div v-if="isShow" class="history-popup">
      <div class="sp-between">
        <span>閲覧({{ total }})</span>
        <fa icon="fa-xmark" class="xmark" @click="closeHistory(diaryId)" />
      </div>
      <ul>
        <li
          v-for="(history, idx) in historyList.contents"
          :key="idx"
          class="flex"
        >
          <div class="history-contents">
            <img
              class="history-open-image"
              :key="String(`browshistory${idx}`)"
              :src="`/api/users/getProfileIcon/${String(history.human_id)}`"
              :title="history.name"
              loading="lazy"
              decoding="async"
            />
            <span class="name">{{ history.name }}</span>
          </div>
          <span>{{ history.browsed_at }}</span>
        </li>
        <InfiniteLoading
          v-if="loadingFlag"
          :identifier="infiniteId"
          @infinite="spLoadData(diaryId)"
        />
      </ul>
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import 'v3-infinite-loading/lib/style.css';
import { BrowsHistory } from '/@/js/models/BrowsHistory';
import useHistory from '/@/js/compositions/UseHistory';

interface Props {
  isShow: boolean;
  diaryId: number;
  browsHistory: string;
  toggleHistory: (id: number) => void;
  closeHistory: (id: number) => void;
}
const props = defineProps<Props>();
const browsList: BrowsHistory[] = props.browsHistory
  ? JSON.parse(props.browsHistory)
  : [];
const browsCount = computed(() => {
  return browsList ? String(browsList.length) : '0';
});
const { loadingFlag, infiniteId, historyList, total, spLoadData } =
  useHistory();

const browsFirstFiveList = computed(() => {
  return browsList ? browsList.reverse().slice(0, 5) : [];
});
</script>
<style scoped>
.history-popup {
  width: 100%;
  height: 100%;
  background-color: rgb(229 231 235);
  white-space: nowrap;
  border-width: 1px;
  border-radius: 4px;
  position: fixed;
  font-size: 12px;
  overflow-y: scroll;
  top: 38px;
  left: 0;
  z-index: 5001;
  padding: 8px;
  padding-bottom: 70px;
}
.history-image {
  width: 20px;
  height: 20px;
  border: rgb(180, 176, 176) 1px solid;
  border-radius: 100%;
  object-fit: cover;
}
.history-open-image {
  width: 38px;
  height: 38px;
  border: rgb(180, 176, 176) 1px solid;
  border-radius: 100%;
  object-fit: cover;
}
.flex {
  display: flex;
  align-items: center;
  padding: 8px 0;
  justify-content: space-between;
  font-size: 13px;
}
.flex span {
  padding-left: 5px;
}
.sp-between {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  align-items: center;
  height: 47px;
  font-size: 14px;
}
.history-contents {
  display: flex;
  align-items: center;
}
.xmark {
  font-size: 28px;
  position: fixed;
  top: 52px;
  right: 21px;
}
.name {
  font-weight: bold;
}
</style>
