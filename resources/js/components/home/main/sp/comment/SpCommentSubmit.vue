<script setup lang="ts">
import { LoginUser } from '/@/js/models/User';
import { ref, watch } from 'vue';
import { User } from '/@/js/models/User';
import SpMentionUsers from '../main/SpMentionUsers.vue';
interface Props {
  loginUser: LoginUser;
  diaryDetail: any;
  dairyId: number;
  mentionUsers: User[];
  content: string;
  changed: (cmt: string) => void;
  removeUser: (id: number, userId: number) => void;
  addMentionUser: (id: number, user: User) => void;
}
const submitComment = (id) => {
  emit('comentSubmit', id);
};
const emojiAdd = (id, str) => {
  emit('submitEmoji', id, str);
};
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
const props = defineProps<Props>();
const changeEmojiVisible = (id: number) => {
  emojiVisible.value[id] = !emojiVisible.value[id];
};
watch(
  () => props.content,
  () => {
    innerContent.value = props.content;
  }
);
const innerContent = ref('');
const emit = defineEmits<Emits>();
interface Emits {
  (e: 'commentSend'): void;
  (e: 'comentSubmit', diaryDetail: number): void;
  (e: 'submitEmoji', emoji: string, diaryDetail: number): void;
  (e: 'removeMentionUser', id: number, userId: number): void;
}
const checkKey = (e: KeyboardEvent) => {
  // エンターキーが押された
  if (e.key == 'Enter') {
    // shift, ctrlまたはcommandキーも押されている
    if (e.ctrlKey || e.metaKey) {
      // テキストが入力されていれば送信
      if (innerContent.value.length > 0) {
        emit('commentSend');
      }
    }
  }
};
const inputComment = (e: Event) => {
  const t = e.target as HTMLInputElement;
  props.changed(t.value);
};
const inputFocusout = () => {};
const userSelected = (user: User) => {
  props.addMentionUser(props.dairyId, user);
};
const removeUserInput = (id: number) => {
  props.removeUser(props.dairyId, id);
};
</script>
<template>
  <div class="single_main comment_bg">
    <div>
      <div class="comment_input">
        <div class="comment_left">
          <SpMentionUsers
            :login-user="loginUser"
            :mention-users="mentionUsers"
            :remove-user="removeUserInput"
            :user-selected="userSelected"
          />
          <textarea
            v-model="innerContent"
            @input="inputComment"
            @focusout="inputFocusout"
            autocomplete="on"
            placeholder="コメント入力欄"
          ></textarea>
        </div>
        <div class="submit_btn_area">
          <div @click="changeEmojiVisible(diaryDetail.id)">
            <i class="fa fa-smile-o" />
          </div>
          <button class="send_btn" @click="submitComment(diaryDetail.id)">
            <i class="fa fa-paper-plane" />
          </button>
        </div>
        <div v-if="emojiVisible[diaryDetail.id]" class="relative">
          <div class="emoji-wrapper">
            <div
              v-for="(emoji, idx) in emojiArray"
              :key="idx"
              class="emoji"
              @click="emojiAdd(emoji, diaryDetail.id)"
            >
              {{ emoji }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.emoji-wrapper {
  display: flex;
  position: absolute;
  right: 42px;
  bottom: 110px;
  flex-wrap: wrap;
  height: 130px;
  width: 85%;
  background-color: rgb(243 244 246);
  border-radius: 6px;
  padding: 4px;
  border: 1px solid rgb(107 114 128);
}
.emoji {
  padding: 0 8px;
  display: flex;
  align-items: center;
  font-size: 30px;
}
</style>
