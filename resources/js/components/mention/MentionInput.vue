<template>
  <div class="mention-wrapper relative py-1">
    <MentionUsers
      :mention-users="mentionUsers"
      :remove-user="removeUserInput"
      :user-selected="userSelected"
    />
    <textarea
      v-model="innerContent"
      class="mention-input-content ml-4 pl-2 pr-12 border rounded-xl w-128 h-full border-gray-500 resize-none"
      autocomplete="on"
      placeholder="Ctrl + Enterキーで送信"
      @keydown="checkKey"
      @input="inputComment"
      @focusout="inputFocusout"
    ></textarea>
  </div>
</template>
<script setup lang="ts">
import { User } from '../../models/User';
import { watch, ref } from 'vue';
import MentionUsers from './MentionUsers.vue';
interface Props {
  dairyId: number;
  content: string;
  mentionStart: boolean;
  mentionUsers: User[];
  changed: (cmt: string) => void;
  addUser: (id: number, user: User) => void;
  removeUser: (id: number, userId: number) => void;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'commentSend'): void;
}
const emit = defineEmits<Emits>();

// const cursorPointer = ref(0);

const innerContent = ref('');

// let searchTimerId: any = -1;

watch(
  () => props.content,
  () => {
    innerContent.value = props.content;
  }
);

const checkKey = (e: KeyboardEvent) => {
  // if (e.isComposing) {
  //   console.log("全角入力中");
  //   // 全角入力中
  //   return;
  // }

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

  // const t = e.target as HTMLInputElement;
  // if (e.key === "@" && t.selectionStart == t.value.length) {
  //   // カーソルがテキストエリアの末端でなければメンションを開始しない
  //   cursorPointer.value = t.selectionStart ?? 0;
  //   startMention();
  // } else if (e.key === "ArrowRight" || e.key === "ArrowLeft" || e.key === " ") {
  //   endMention();
  // }
};

const inputComment = (e: Event) => {
  const t = e.target as HTMLInputElement;
  props.changed(t.value);
};
const inputFocusout = () => {};

const removeUserInput = (id: number) => {
  props.removeUser(props.dairyId, id);
};

const userSelected = (user: User) => {
  props.addUser(props.dairyId, user);
};
</script>
<style scoped>
.mention-input-content {
  height: 5.5rem;
  transform: rotate(0.03deg);
  -moz-transform: rotate(0.03deg);
  -ms-transform: rotate(0.03deg);
  -o-transform: rotate(0.03deg);
  -webkit-transform: rotate(0.03deg);
}
</style>
