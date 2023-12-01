<template>
  <div v-if="isSearching" class="mention-search-mordal absolute ml-4">
    <ul class="w-128 h-full overflow-y-auto bg-gray-100">
      <li
        v-for="user in searchUsers"
        :key="`searchUsers${String(user.id)}`"
        class="p-2 cursor-pointer hover:bg-cyan-500 hover:text-white border-b border-gray-200"
        @click="selectUser(user)"
      >
        {{ user.user_name }}
        <span class="text-xs" v-if="user.organization?.organization_name">
          ({{ user.organization.organization_name }})
        </span>
      </li>
    </ul>
  </div>
  <div
    class="mention-input-user ml-4 pl-2 pr-2 border rounded-xl w-128 mb-1 border-gray-500 overflow-x-scroll"
  >
    <span
      v-for="user in mentionUsers"
      :key="`mentionUser${String(user.id)}`"
      class="mention-user bg-cyan-500 text-white mx-1 rounded-md mb-1"
    >
      {{ user.user_name }}
      <fa
        icon="fa-x"
        class="text-white cursor-pointer pl-1 hover:text-gray-700"
        @click="removeUser(user.id)"
      />
    </span>
    <div
      id="input-user"
      class="search-user-input mb-1"
      :class="{ 'place-hide': mentionUsers.length > 0 }"
      contenteditable="true"
      placeholder="メンションするユーザを入力"
      @focus="startMention"
      @blur="onFocusout"
      @input="onInput"
      @keydown="keyCheck"
    ></div>
  </div>
</template>
<script setup lang="ts">
import axios from 'axios';
import { ref } from 'vue';
import { User } from '../../models/User';
import useAxios from '/@/js/compositions/UseAxios';

interface Props {
  mentionUsers: User[];
  userSelected: (user: User) => void;
  removeUser: (id: number) => void;
}
const props = defineProps<Props>();
const { axiosErrorHandling, errorToast } = useAxios();

const isSearching = ref(false);
const searchUsers = ref<User[]>([]);
const searchUserName = ref('');

const startMention = () => {
  searchUsers.value.length = 0;
  isSearching.value = true;
  searchUserName.value = '';
  searchMentionUsers();
};

const endMention = () => {
  console.log('endMention');
  isSearching.value = false;
  let elm = document.getElementById('input-user');
  if (elm) {
    elm.innerText = '';
  }
};

const onFocusout = () => {
  console.log('onFocusout');
  setTimeout(() => {
    endMention();
  }, 100);
};

const keyCheck = (e: KeyboardEvent) => {
  if (e.key == 'Enter') {
    // 改行は受け付けない
    e.preventDefault();
    return false;
  }
};

const onInput = (e: Event) => {
  const t = e.target as HTMLElement;
  searchUserName.value = t.innerText;
  searchMentionUsers();
};

const searchMentionUsers = () => {
  axios
    .get('/api/users/mention/user', {
      params: {
        search_name: searchUserName.value,
      },
    })
    .then((res) => {
      searchUsers.value.length = 0;
      searchUsers.value.push(...(res.data as User[]));
    })
    .catch((err) => {
      const result = axiosErrorHandling(err);
      errorToast(result.code, result.message);
    });
};

const selectUser = (user: User) => {
  console.log('selectUser');
  props.userSelected(user);
  endMention();
};
</script>
<style scoped>
.mention-input-user {
  line-height: 1.875rem;
  overflow: scroll;
  -ms-overflow-style: none;
  scrollbar-width: none;
  display: flex;
  flex-direction: row;
  padding: 0.125rem 0.25rem;
  flex-wrap: wrap;
}
.mention-input-user::-webkit-scrollbar {
  display: none;
}
.mention-user {
  padding: 0px 0.25rem;
}
.search-user-input {
  display: inline-block;
  flex-grow: 1;
}
.search-user-input:empty:before {
  content: attr(placeholder);
  pointer-events: none;
  display: block;
  color: rgb(156 163 175);
}
.search-user-input.place-hide:empty:before {
  display: none;
}
.search-user-input:focus {
  outline: 0px solid transparent;
}
.mention-search-mordal {
  top: -10rem;
  height: 10rem;
}
</style>
