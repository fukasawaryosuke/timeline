<template>
  <div v-if="reportModalOpen" class="modalArea"
      :style="{ top: headerHeight }"
  >
    <div class="single_article mb-25">
      <div class="single_control">
        <img
          class="home_icons"
          :src="`/api/users/getProfileIcon/${String(
            diaryDetail.human_id
          )}`"
          alt="iconImage"
          loading="lazy"
        />
      </div>
      <div class="single_header">
        <h2>
          {{ diaryDetail?.in_house_system?.system_name }}
        </h2>
      </div>
      <div class="single_description pl-10">
        <span>
          <i class="fa fa-user" />
          {{ diaryDetail.user_name }}
        </span>
        <div>
          <span>
            {{ diaryDetail.created_at }}
          </span>
        </div>
      </div>
      <div
        v-for="(day, diaryIdx) in diaryDetail.diary.contents"
        :key="diaryIdx"
        class="single_main"
      >
        <div
          v-if="
            (day.contentType === REPORT_CONTENT_TYPE.TEXT ||
              day.contentType === REPORT_CONTENT_TYPE.TEXTAREA ||
              day.contentType === REPORT_CONTENT_TYPE.DATETIME) &&
            day.content !== null
          "
        >
          <h3>
            {{ day.title }}
          </h3>
          <p>
            <div class="preline" v-html="convertToLinks( day.content)">
            </div>
          </p>
        </div>
        <div
          v-if="
            day.contentType === REPORT_CONTENT_TYPE.IMAGE &&
            day.path !== null
          "
          @click="modalOpen(day.path)"
        >
          <h1>
            {{ day.title }}
          </h1>
          <img :src="day.path" loading="lazy" decoding="async" />
        </div>
        <div
          v-if="day.contentType === REPORT_CONTENT_TYPE.MOVIE"
        >
          <h1>
            {{ day.title }}
          </h1>
          <label
            v-if="day.path !== null"
            @click="movieModalOpen(day.path)"
          >
            <fa icon="fa-video" />
            <span>{{ day.content }}</span>
          </label>
          <div v-else>投稿なし</div>
        </div>
        <div
          v-if="day.contentType === REPORT_CONTENT_TYPE.TENKI"
        >
          <h1>
            {{ day.title }}
          </h1>
          <WeatherResult :diary="day.content" class="flex"/>
        </div>
        <div
          v-if="day.contentType === REPORT_CONTENT_TYPE.CHOICE"
        >
          <h1>
            {{ day.title }}
          </h1>
          <div>
            <div>
              {{ getCheckedTitle(day) }}
            </div>
          </div>
        </div>
        <div
          v-if="day.contentType === REPORT_CONTENT_TYPE.SELECT"
        >
          <h1>
            {{ day.title }}
          </h1>
          <div>
            <div>
              {{ getSelectTitle(day) }}
            </div>
          </div>
        </div>
      </div>
      <!-- 日報閲覧履歴 -->
      <div class="single_main view_num">
        <SpHomeHistory
          :is-show="isHistoryShow"
          :diary-id="diaryDetail.id"
          :brows-history="diaryDetail.history"
          :toggle-history="
            () => {
              isHistoryShow = !isHistoryShow;
              footerArea(false);
            }
          "
          :close-history="
            () => {
              isHistoryShow = false;
              footerArea(true);
            }
          "
        />
      </div>
      <div
        :id="'comment_list_diary' + String(diaryDetail.id)"
      >
        <div
          v-for="(com, idx) in diaryDetail.comment"
          :key="idx"
          class="bt-gray"
        >
          <div class="single_comment">
            <img
              class="home_icons"
              :src="`/api/users/getProfileIcon/${String(
                com.user.human_id
              )}`"
              alt="commentuser"
              loading="lazy"
              decoding="async"
            />
            <div class="commentcontents">
              <div>
                <span>
                  {{ com.user.user_name }}
                </span>
                <span
                  v-if="com.user.organization?.organization_name"
                >
                  ({{ com.user.organization.organization_name }})
                </span>
                <span>
                  {{ com.created_at }}
                </span>
              </div>
              <div class="mention_area">
                <span
                  v-for="(mension, mIdx) in com.mensions"
                  :key="mIdx"
                  class="mention_user"
                >
                  @{{ mension.user.user_name }}
                </span>
              </div>
              <pre class="comment" v-html="convertToLinks( com.comment)"></pre>
            </div>
            <div class="replay">
              <span class="replay">
                <i class="fa fa-reply" aria-hidden="true" @click="commentReply(Number(diaryDetail.id), com)"></i>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div id="close_detail" @click="innerClose">
      <i class="fa fa-times-circle"></i>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { ref, watch, computed } from 'vue';
import { User } from '/@/js/models/User';
import SpHomeHistory from '/@/js/components/home/main/sp/main/SpHomeHistory.vue';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import WeatherResult from '/@/js/components/home/common/WeatherResult.vue';

interface Props {
  loginUser: User;
  reportModalOpen: boolean;
  diaryDetail: any;
  imageModalOpen: (path: string) => void;
  imageModalClose: () => void;
  movieModalOpen: (path: string) => void;
  headerHeight: string;
  footerArea: (flg: boolean) => void;
  commentReply: (diaryId: number, targetComment: any) => void;
  mentionReset: (dialyId: number) => void;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'close'): void;
}
const emit = defineEmits<Emits>();

watch(
  () => props.diaryDetail,
  () => {
    mentionFlags.value[props.diaryDetail.id] = false;
    mentionUsers.value[props.diaryDetail.id] = [] as User[];
    isHistoryShow.value = false;
  }
);

const mentionFlags = ref<boolean[]>([]);
const mentionUsers = ref<User[][]>([]);
const isHistoryShow = ref<boolean>(false);

const modalOpen = (src: string) => {
  props.imageModalOpen(src);
};
const movieModalOpen = (src: string) => {
  props.movieModalOpen(src);
};
const getCheckedTitle = computed(() => (diary) => {
  const notSelect = '選択していない';
  if (diary.checked.length < 1) return notSelect;
  const checkedTitle: Array<string> = [];
  diary.choices?.forEach((c) => {
    if (diary.checked?.includes(c.value)) {
      checkedTitle.push(c.title);
    }
  });
  return checkedTitle.join(', ');
});
const getSelectTitle = computed(() => (diary) => {
  const notSelect = '選択していない';
  if (!diary.content) return notSelect; // NOTE: この関数を呼んでいる時点で選択肢がないことはあり得ないので非考慮でいい
  for (let i = 0; i < diary.choices.length; i++) {
    if (diary.choices[i].value == diary.content) {
      return diary.choices[i].title;
    }
  }
  return notSelect;
});
const innerClose = () => {
  props.mentionReset(props.diaryDetail.id);
  emit('close');
};

const convertToLinks = (text: string): string => {
  const urlRegex = /(https?:\/\/[^\s]+)/g;
  return text.replace(urlRegex, (url) => {
    return `<a href="${url}" target="_blank" style="color: blue; text-decoration: underline;">${url}</a>`;
  });
};
</script>
<style scoped>
.modalArea {
  display: block;
  position: fixed;
  width: 100%;
  height: 100%;
  overflow-y: scroll;
}
.mb-25 {
  margin-bottom: 250px;
  overflow-x: hidden;
}
.bt-gray {
  border-top: 1px solid #ccc;
  padding-top: 10px;
}
.pl-10 {
  padding-left: 10px;
}
.commentcontents {
  flex: 1;
}
.replay {
  color: rgb(107 114 128);
  margin-right: 10px;
}
.preline {
  white-space: pre-line;
}
.comment {
  white-space: pre-line;
}
</style>
