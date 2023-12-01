<template>
  <section
    v-if="reportModalOpen"
    id="modalArea"
    :class="{
      modalArea: true,
      openModal: modalOpen,
      closeModal: !modalOpen,
      'closest-notice': true,
    }"
  >
    <div id="modalBg" class="modalBg" @click="innerClose" />
    <div
      class="modalWrapper px-8 bg-white border rounded-lg border-gray-400 w-176"
    >
      <fa
        icon="fa-xmark"
        class="cursor-pointer font-semibold text-gray-400 hover:text-gray-600 absolute top-2 right-4 text-3xl"
        @click="innerClose"
      />
      <div class="modalContents">
        <div class="modal_inner">
          <div class="border-b border-gray-200 pl-4 w-full">
            <div class="flex">
              <img
                class="w-11 h-11 rounded-full border object-cover"
                :src="`/api/users/getProfileIcon/${String(
                  diaryDetail.human_id
                )}`"
                alt="iconImage"
                loading="lazy"
              />
              <div class="pl-3">
                <span class="text-gray-500 font-semibold">
                  {{ diaryDetail.user_name }}
                </span>
                <div>
                  <span class="align-top text-gray-400 text-xs">
                    {{ diaryDetail.created_at }}
                  </span>
                </div>
              </div>
            </div>
            <div class="max-h-96 overflow-y-scroll">
              <div
                v-for="(day, diaryIdx) in diaryDetail.diary.contents"
                :key="diaryIdx"
              >
                <div
                  v-if="
                    (day.contentType === REPORT_CONTENT_TYPE.TEXT ||
                      day.contentType === REPORT_CONTENT_TYPE.TEXTAREA ||
                      day.contentType === REPORT_CONTENT_TYPE.DATETIME) &&
                    day.content !== null
                  "
                  class="pt-4"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <div class="pt-0.5">
                    <div class="text-slate-600 whitespace-pre-wrap break-words" v-html="convertToLinks(day.content)">
                    
                    </div>
                  </div>
                </div>
                <div
                  v-if="
                    day.contentType === REPORT_CONTENT_TYPE.IMAGE &&
                    day.path !== null
                  "
                  class="pt-4 max-w-xs"
                  @click="modalOpen(day.path)"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <img :src="day.path" loading="lazy" decoding="async" />
                </div>
                <div
                  v-if="day.contentType === REPORT_CONTENT_TYPE.MOVIE"
                  class="pt-4 font-semibold mr-4 text-gray-400"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <label
                    v-if="day.path !== null"
                    class="cursor-pointer hover:text-gray-600"
                    @click="movieModalOpen(day.path)"
                  >
                    <fa icon="fa-video" class="w-4 h-4 mr-2" />
                    <span>{{ day.content }}</span>
                  </label>
                  <div v-else>投稿なし</div>
                </div>
                <div
                  v-if="day.contentType === REPORT_CONTENT_TYPE.TENKI"
                  class="pt-4"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <WeatherResult :diary="day.content" />
                </div>
                <div
                  v-if="day.contentType === REPORT_CONTENT_TYPE.CHOICE"
                  class="pt-4"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <div class="pt-0.5">
                    <div class="text-slate-600 whitespace-pre-wrap break-words">
                      {{ getCheckedTitle(day) }}
                    </div>
                  </div>
                </div>
                <div
                  v-if="day.contentType === REPORT_CONTENT_TYPE.SELECT"
                  class="pt-4"
                >
                  <h1 class="text-lg font-bold text-gray-700">
                    {{ day.title }}
                  </h1>
                  <div class="pt-0.5">
                    <div class="text-slate-600 whitespace-pre-wrap break-words">
                      {{ getSelectTitle(day) }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- 日報閲覧履歴 -->
            <HomeHistory
              :is-show="isHistoryShow"
              :diary-id="diaryDetail.id"
              :brows-history="diaryDetail.history"
              :toggle-history="
                () => {
                  isHistoryShow = !isHistoryShow;
                }
              "
              :close-history="
                () => {
                  isHistoryShow = false;
                }
              "
            />
          </div>
          <div class="w-full">
            <div
              :id="'comment_list_diary' + String(diaryDetail.id)"
              class="overflow-y-auto h-fit max-h-36 relative"
            >
              <div
                v-for="(com, idx) in diaryDetail.comment"
                :key="idx"
                class="pl-4 pt-2"
              >
                <div class="flex w-full">
                  <img
                    class="w-11 h-11 rounded-full border object-cover"
                    :src="`/api/users/getProfileIcon/${String(
                      com.user.human_id
                    )}`"
                    alt="commentuser"
                    loading="lazy"
                    decoding="async"
                  />
                  <div class="pl-1.5 w-full">
                    <div class="leading-7 flex items-center">
                      <span class="font-bold text-sm">
                        {{ com.user.user_name }}
                      </span>
                      <span
                        class="text-xs"
                        v-if="com.user.organization?.organization_name"
                      >
                        ({{ com.user.organization.organization_name }})
                      </span>
                      <span class="leading-7 pl-1 text-xs text-gray-500">
                        {{ com.created_at }}
                      </span>
                      <fa
                        icon="fa-reply"
                        class="text-gray-500 cursor-pointer ml-auto"
                        @click="commentReply(Number(diaryDetail.id), com)"
                      />
                    </div>
                    <div class="flex flex-wrap gap-1">
                      <span
                        v-for="(mension, mIdx) in com.mensions"
                        :key="mIdx"
                        class="bg-cyan-50 text-cyan-500 p-1 rounded-full text-xs"
                      >
                        @{{ mension.user.user_name }}
                      </span>
                    </div>
                    <pre
                      class="comment pl-1 text-gray-600 whitespace-pre-wrap break-words w-132" v-html="convertToLinks(com.comment)"
                      ></pre
                    >
                  </div>
                </div>
              </div>
            </div>
            <div class="input-wrapper ml-6 flex items-end relative">
              <img
                class="w-11 h-11 rounded-full border self-center object-cover"
                :src="`/api/users/getProfileIcon/${String(loginUser.human_id)}`"
                alt="loginuser"
                loading="lazy"
                decoding="async"
              />
              <MentionInput
                :dairy-id="diaryDetail.id"
                :content="diaryComments[diaryDetail.id] ?? ''"
                :mention-start="mentionFlags[diaryDetail.id]"
                :mention-users="mentionUsers[diaryDetail.id]"
                :changed="
                  (cmt) => {
                    inputChanged(diaryDetail, cmt);
                  }
                "
                :add-user="addMentionUser"
                :remove-user="removeMentionUser"
                @comment-send="submitComment(diaryDetail.id)"
              />
              <div
                class="flex text-gray-500 cursor-pointer self-center absolute right-24 bottom-3"
              >
                <div>
                  <fa
                    icon="fa-stamp"
                    class="text-gray-500 cursor-pointer pl-3 hover:text-gray-700"
                    @click="changeEmojiVisible(diaryDetail.id)"
                  />
                </div>
              </div>
              <button
                class="bg-gray-300 rounded-lg border-gray-200 border ml-2 w-20 h-20 mb-2 hover:bg-cyan-500"
                @click="submitComment(diaryDetail.id)"
              >
                <fa
                  icon="fa-paper-plane"
                  class="text-gray-500 z-50 cursor-pointer pr-2.5"
                />
              </button>
            </div>
            <div v-if="emojiVisible[diaryDetail.id]" class="relative">
              <div
                class="emoji-wrapper absolute flex flex-wrap w-48 bg-gray-100 rounded-md p-1 border border-solid border-gray-500"
              >
                <div
                  v-for="(emoji, idx) in emojiArray"
                  :key="idx"
                  class="cursor-pointer text-xl px-1.5"
                  @click="emojiAdd(emoji, diaryDetail.id)"
                >
                  {{ emoji }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { User } from '../../models/User';
import MentionInput from '../mention/MentionInput.vue';
import HomeHistory from '../home/main/HomeHistory.vue';
import UseAxios from '/@/js/compositions/UseAxios';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import WeatherResult from '/@/js/components/home/common/WeatherResult.vue';

interface Props {
  reportModalOpen: boolean;
  diaryDetail: any;
  loginUser: User;
  imageModalOpen: (path: string) => void;
  imageModalClose: () => void;
  movieModalOpen: (path: string) => void;
  diaryComments: string[];
  commentReply: (diaryId: number, targetComment: any) => void;
  mentionUsers: User[][];
  mentionReset: (dialyId: number) => void;
  // imageModalClose: () => void;
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
    isHistoryShow.value = false;
  }
);

// const comment = ref<string[]>([]);
const emojiVisible = ref<boolean[]>([]);
const mentionFlags = ref<boolean[]>([]);
// const mentionUsers = ref<User[][]>([]);
const emojiArray = ref<string[]>([
  '😆',
  '😊',
  '🥰',
  '😝',
  '😱',
  '👌',
  '🙏',
  '👏',
  '🙌',
  '👍',
]);

const isHistoryShow = ref<boolean>(false);

const { axiosErrorHandling, asyncMethodLock, errorToast } = UseAxios();

const inputChanged = (dairy: any, cmt: string) => {
  props.diaryComments[dairy.id] = cmt;
};
const submitComment = async (diary_id: number) => {
  await asyncMethodLock(async () => {
    await axios
      .post('/api/comment/create', {
        diary_id: diary_id,
        comment: props.diaryComments[diary_id],
        mentions: props.mentionUsers[diary_id],
      })
      .then((res) => {
        if (res.data.success) {
          let com = res.data.received_comment;
          if (com) {
            let diary = props.diaryDetail;
            if (diary) {
              diary.comment.push(com);
            }
          }

          // コメントをリストに反映したので入力欄を初期化
          props.diaryComments[com.diary_id] = '';
          mentionFlags.value[com.diary_id] = false;
          props.mentionUsers[com.diary_id].length = 0;

          let commentListId = 'comment_list_diary' + String(com.diary_id);
          setTimeout(() => {
            let commentList = document.getElementById(commentListId);
            if (commentList) {
              commentList.scrollTop = commentList.scrollHeight;
            }
          }, 300);
        }
      })
      .catch((err) => {
        const result = axiosErrorHandling(err);
        errorToast(result.code, result.message);
      });
  });
};
const changeEmojiVisible = (id: number) => {
  emojiVisible.value[id] = !emojiVisible.value[id];
};

const emojiAdd = (emoji: string, id: any) => {
  if (!props.diaryComments[id]) {
    props.diaryComments[id] = '';
    props.diaryComments[id] = props.diaryComments[id] + emoji;
  } else {
    props.diaryComments[id] = props.diaryComments[id] + emoji;
  }
};
const modalOpen = (src: string) => {
  props.imageModalOpen(src);
};
const movieModalOpen = (src: string) => {
  props.movieModalOpen(src);
};
const mentionStart = (id: number) => {
  mentionFlags.value[id] = !mentionFlags.value[id];
};

const addMentionUser = (id: number, user: User) => {
  props.mentionUsers[id].push(user);
};
const removeMentionUser = (id: number, userId: number) => {
  const removeIdx = props.mentionUsers[id].findIndex((u: User) => {
    return u.id == userId;
  });
  if (removeIdx > -1) {
    props.mentionUsers[id].splice(removeIdx, 1);
  }
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
.openModal {
  transition: 0.5s;
  opacity: 1;
}
.closeModal {
  transition: 0.5s;
  opacity: 0;
}
.preLine {
  white-space: pre-wrap;
}

.modalArea {
  display: block;
  position: fixed;
  z-index: 150;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: #00000099;
}

.modalBg {
  width: 100%;
  height: 100%;
  background-color: rgba(30, 30, 30, 0);
}

.modalWrapper {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  min-width: 750px;
  /* max-width: 75%; */
  /* padding: 40px 5%; */
  height: auto;
  overflow: auto;
  box-sizing: inherit;
}

.closeModal.cancel_top {
  position: absolute;
  top: 0.5rem;
  right: 1rem;
  cursor: pointer;
}

.cancel_top .cancel {
  padding: 10px 20px;
  color: #fff;
  font-weight: bold;
  letter-spacing: 5px;
  margin: 0 0 0 0;
  font-size: 100%;
}

.modalimport {
  width: 95% !important;
  max-width: 100% !important;
}

.h1_wrap i {
  display: block;
  font-size: 250%;
  margin: 0 0 15px 0;
}

.modalWrapper .modalContents .h1_wrap h1::before {
  background: transparent;
}

.fa-exclamation-triangle,
.fa-file-upload {
  color: #f05353;
}

.fa-check-circle {
  color: #80b74f;
}

.modalWrapper .fa-download {
  color: #0087ff;
}

/* .modalContents h1 {
  text-align: center;
  font-size: 115%;
  border-bottom: 2px dashed #9ab583;
  display: inline-block;
  margin: 0 auto;
} */

.modal_inner {
  margin: 30px 0 0 0;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.update_wrap .search_submit {
  width: 100%;
}

.update_wrap .search_submit:last-child {
  margin: 20px 3% 0 0;
}

.h1_wrap {
  text-align: center;
  padding: 0 15px;
}

.search_submit {
  padding: 10px 30px;
  color: #fff;
  font-weight: bold;
  width: 150px;
  letter-spacing: 5px;
  margin: 0 3% 0 0;
  font-size: 100%;
}

.search_submit:last-child {
  margin: 0;
}

.modal_btn_gray {
  background-color: #bdbdbd;
}

.modal_btn_orange {
  background-color: #ff8902;
}
.emoji-wrapper {
  right: 5.5rem;
  bottom: 2.5rem;
}
pre.comment {
  font-family: initial;
}

@media screen and (max-width: 959px) {
  .modal_inner button {
    width: 150px !important;
  }
}
</style>
