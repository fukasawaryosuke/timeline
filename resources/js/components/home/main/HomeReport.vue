<template>
  <template v-for="(dairy, index) in indexdata" :key="index">
    <div
      v-if="isDateChange(dairy.created_at, Number(index))"
      class="bg-stone-50 flex w-full pt-4 pb-2 sticky top-36 z-10"
    >
      <div class="border-t-2 border-cyan-500 w-5/12" />
      <span
        class="leading-4 -mt-2 px-4 w-2/12 text-sm text-cyan-500 font-bold text-center"
      >
        {{ scrollDateString(dairy.created_at) }}
      </span>
      <div class="border-t-2 border-cyan-500 w-5/12" />
    </div>
    <div class="diary-wrapper border w-176 bg-white rounded-xl">
      <!-- 日報 -->
      <div class="pl-8 pr-4 pt-8 border-b border-gray-200">
        <!-- サムネイル周り -->
        <div class="flex justify-between items-center">
          <div class="flex items-center">
            <img
              class="w-11 h-11 rounded-full border object-cover"
              :src="`/api/users/getProfileIcon/${String(dairy.human_id)}`"
              alt="iconImage"
              loading="lazy"
              decoding="async"
            />
            <div class="pl-3">
              <div>
                <span class="text-slate-600 text-sm font-semibold">
                  {{ dairy?.in_house_system?.system_name }}
                </span>
              </div>
              <span
                class="text-gray-500 font-semibold cursor-pointer"
                @click="
                  () => {
                    selectedUser(dairy.user_name);
                    selectedUserOnlyDiary(dairy.created_by);
                  }
                "
              >
                {{ dairy.user_name }}
              </span>
              <div>
                <span class="align-top text-gray-400 text-xs">
                  {{ dairy.created_at }}
                </span>
              </div>
            </div>
          </div>
          <!-- 削除モーダル 作成ユーザーにのみ表示 -->
          <div v-if="dairy.created_by === loginUser.id">
            <fa
              icon="fa-pen"
              class="w-5 h-5 cursor-pointer font-semibold hover:text-gray-600 ¥text-3xl mr-6 text-gray-400"
              @click="gotoEditDiary(dairy.id)"
            />
            <fa
              icon="fa-trash"
              class="w-5 h-5 cursor-pointer font-semibold hover:text-gray-600 ¥text-3xl mr-3 text-gray-400"
              @click="deleteModalOpen(dairy.id, index)"
            />
          </div>
        </div>

        <!-- 日報投稿詳細 -->
        <div class="max-h-120 overflow-y-scroll">
          <div v-for="(day, diaryIdx) in dairy.diary.contents" :key="diaryIdx">
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
          :is-show="browsingHistories[dairy.id]"
          :diary-id="dairy.id"
          :brows-history="dairy.history"
          :toggle-history="toggleHistory"
          :close-history="closeHistory"
        />
        <!-- コメント -->
      </div>
      <div class="w-full">
        <div
          :id="'comment_list_diary' + String(dairy.id)"
          class="overflow-y-auto h-fit max-h-36 relative"
        >
          <!-- <div class="w-full font-bold text-2xl mt-14 opacity-20 text-center">
        コメントがありません
      </div> -->
          <div v-for="(com, idx) in dairy.comment" :key="idx" class="px-4 pt-2">
            <div class="flex w-full">
              <img
                class="w-[2.85rem] h-11 rounded-full border object-cover"
                :src="`/api/users/getProfileIcon/${String(com.user.human_id)}`"
                alt="コメントアイコン"
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
                    @click="commentReply(Number(dairy.id), com)"
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
                  class="comment pl-1 text-gray-600 whitespace-pre-wrap break-words w-full" v-html="convertToLinks(com.comment)"></pre>
              </div>
            </div>
          </div>
        </div>
        <div class="input-wrapper ml-6 flex items-end relative">
          <img
            class="w-11 h-11 rounded-full border self-center object-cover"
            :src="`/api/users/getProfileIcon/${String(loginUser.human_id)}`"
            alt="profile"
            loading="lazy"
            decoding="async"
          />
          <MentionInput
            :dairy-id="dairy.id"
            :content="diaryComments[dairy.id] ?? ''"
            :mention-start="mentionFlags[dairy.id]"
            :mention-users="mentionUsers[dairy.id]"
            :changed="
              (cmt) => {
                inputChanged(dairy, cmt);
              }
            "
            :add-user="addMentionUser"
            :remove-user="removeMentionUser"
            @comment-send="submitComment(dairy.id)"
          />
          <div
            class="flex text-gray-500 cursor-pointer self-center absolute right-32 bottom-3"
          >
            <div>
              <fa
                icon="fa-stamp"
                class="text-gray-500 cursor-pointer hover:text-gray-700"
                @click="changeEmojiVisible(dairy.id)"
              />
            </div>
          </div>
          <button
            class="bg-gray-300 rounded-lg border-gray-200 border ml-2 w-20 h-20 mb-2 hover:bg-cyan-500"
            @click="submitComment(dairy.id)"
          >
            <fa
              icon="fa-paper-plane"
              class="text-gray-500 z-50 cursor-pointer pr-2.5"
            />
          </button>
        </div>
        <div v-if="emojiVisible[dairy.id]" class="relative">
          <div
            class="emoji-wrapper absolute flex flex-wrap w-48 bg-gray-100 rounded-md p-1 border border-solid border-gray-500"
          >
            <div
              v-for="(emoji, idx) in emojiArray"
              :key="idx"
              class="cursor-pointer text-xl px-1.5"
              @click="emojiAdd(emoji, dairy.id)"
            >
              {{ emoji }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  <InfiniteLoading
    v-if="createdBy && loadingFlag"
    class="w-full flex justify-center pt-10"
    :identifier="infiniteId"
    @infinite="loadData(isHome, createdBy, divisionCodes, searchDate)"
  />
  <InfiniteLoading
    v-else-if="divisionCodes && loadingFlag"
    class="w-full flex justify-center pt-10"
    :identifier="infiniteId"
    @infinite="loadData(isHome, loginUser.id, divisionCodes, searchDate)"
  />
  <div
    v-if="!loadingFlag"
    class="w-full flex justify-center pt-10 text-gray-400 font-bold"
  >
    これ以上投稿がありません
  </div>
</template>

<script setup lang="ts">
import axios from 'axios';
import { ref, watch, computed, onMounted } from 'vue';
import InfiniteLoading from 'v3-infinite-loading';
import 'v3-infinite-loading/lib/style.css';
import { LoginUser, User } from '/@/js/models/User';
import MentionInput from '../../mention/MentionInput.vue';
import useReport from '../../../compositions/UseReport';
import HomeHistory from './HomeHistory.vue';
import UseAxios from '/@/js/compositions/UseAxios';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';
import WeatherResult from '/@/js/components/home/common/WeatherResult.vue';

interface Props {
  divisionCodes: number[];
  loginUser: LoginUser;
  isHome: boolean;
  searchDate: string;
  imageModalOpen: (path: string) => void;
  imageModalClose: () => void;
  movieModalOpen: (path: string) => void;
  movieModalClose: () => void;
  gotoEditDiary: (diaryId: number) => void;
  deleteModalOpen: (diaryId: number, diaryIndex: number) => void;
  selectedUser: (path: string) => void;
  indexdata: never | any;
  selectedUserOnlyDiary: (index: number) => void;
  createdBy: number;
  loadingFlag: boolean;
  loadData: (
    isHome: boolean,
    userId: number,
    divisionCodes: number[],
    searchDate: string
  ) => void;
  initLoading: () => void;
  browsingHistories: boolean[];
  toggleHistory: (id: number) => void;
  closeHistory: (id: number) => void;
  mentionFlags: boolean[];
  mentionUsers: User[][];
  selectedIhsId: number;
  diaryComments: string[];
  commentReply: (diaryId: number, targetComment: any) => void;
}
const props = defineProps<Props>();

// const comment = ref<string[]>([]);
const emojiVisible = ref<boolean[]>([]);

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

const { asyncMethodLock } = UseAxios();

let reportScrollDate = -1;

const infiniteId = ref<number>(0);
onMounted(() => {
  infiniteId.value = infiniteId.value + 1;
});

const isDateChange = (created_at: string, index: number) => {
  let d = new Date(created_at).getDate();
  if (reportScrollDate != d || index === 0) {
    reportScrollDate = d;
    return true;
  }
  return false;
};

const scrollDateString = (created_at: string) => {
  let d = new Date(created_at);
  let dStr = 'YYYY/MM/DD';
  dStr = dStr.replace('YYYY', ('0000' + d.getFullYear()).slice(-4));
  dStr = dStr.replace('MM', ('00' + (d.getMonth() + 1)).slice(-2));
  dStr = dStr.replace('DD', ('00' + d.getDate()).slice(-2));
  return dStr;
};

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
            let diary = props.indexdata.find((i: any) => {
              return i.id == com.diary_id;
            });
            if (diary) {
              diary.comment.push(com);
            }
          }

          // コメントをリストに反映したので入力欄を初期化
          props.diaryComments[com.diary_id] = '';
          // props.mentionFlags[com.diary_id] = false;
          // props.mentionUsers[com.diary_id].length = 0;
          emit('mentionReset', com.diary_id);

          let commentListId = 'comment_list_diary' + String(com.diary_id);
          setTimeout(() => {
            let commentList = document.getElementById(commentListId);
            if (commentList) {
              commentList.scrollTop = commentList.scrollHeight;
            }
          }, 300);
        }
      });
  });
};

interface Emits {
  (e: 'mentionStart', id: number): void;
  (e: 'addMentionUser', id: number, user: User): void;
  (e: 'removeMentionUser', id: number, userId: number): void;
  (e: 'mentionReset', diary_id: number): void;
}

const emit = defineEmits<Emits>();

const changeEmojiVisible = (id: number) => {
  emojiVisible.value[id] = !emojiVisible.value[id];
};

const emojiAdd = (emoji: string, id: any) => {
  console.log(!props.diaryComments[id]);
  if (!props.diaryComments[id]) {
    props.diaryComments[id] = '';
    props.diaryComments[id] = props.diaryComments[id] + emoji;
  } else {
    props.diaryComments[id] = props.diaryComments[id] + emoji;
  }
};

const mentionStart = (id: number) => {
  // props.mentionFlags[id] = !props.mentionFlags[id];
  emit('mentionStart', id);
};

const addMentionUser = (id: number, user: User) => {
  // props.mentionUsers[id].push(user);
  emit('addMentionUser', id, user);
};

const removeMentionUser = (id: number, userId: number) => {
  // const removeIdx = props.mentionUsers[id].findIndex((u: User) => {
  //   return u.id == userId;
  // });
  // if (removeIdx > -1) {
  //   props.mentionUsers[id].splice(removeIdx, 1);
  // }
  emit('removeMentionUser', id, userId);
};

watch(
  () => props.divisionCodes,
  () => {
    // if (props.divisionCodes.length > 0) {
    // 部署コードが変わったのでリストを作り直す
    props.initLoading();
    // }
  },
  { deep: true }
);

watch(
  () => props.isHome,
  (isHome) => {
    if (isHome) {
      // 所属部署を指定したのでリストを作り直す
      props.initLoading();
    }
  }
);

watch(
  () => props.searchDate,
  () => {
    // 表示日付を選択したのでリストを作り直す
    reportScrollDate = new Date(props.searchDate).getDate();
    props.initLoading();
  }
);

const modalOpen = (src: string) => {
  props.imageModalOpen(src);
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

const convertToLinks = (text: string): string => {
  const urlRegex = /(https?:\/\/[^\s]+)/g;
  return text.replace(urlRegex, (url) => {
    return `<a href="${url}" target="_blank" style="color: blue; text-decoration: underline;">${url}</a>`;
  });
}
</script>

<style scoped>
.emoji-wrapper {
  right: 6.625rem;
  bottom: 2.5rem;
}
pre.comment {
  font-family: initial;
}
</style>
