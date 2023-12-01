<template>
  <div v-if="isSearching" >
    <ul class="search-user-list">
      <li
        v-for="user in searchUsers"
        :key="`searchUsers${String(user.id)}`"
        class="p-2"
        @click="selectUser(user)"
      >
        {{ user.user_name }}
        <span v-if="user.organization?.organization_name">
          ({{ user.organization.organization_name }})
        </span>
      </li>
    </ul>
  </div>
  <div class="mention-input-user">
    <span
      v-for="user in mentionUsers"
      :key="`mentionUser${String(user.id)}`"
      class="mention-user"
    >
      {{ user.user_name }}
      <fa icon="fa-x" @click="removeUser(user.id)" />
    </span>
    <div
      id="input-user"
      class="search-user-input"
      :class="{ 'place-hide': mentionUsers?.length > 0 }"
      contenteditable="true"
      placeholder="メンションするユーザー"
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
import { User, LoginUser } from '/@/js/models/User';
import useAxios from '/@/js/compositions/UseAxios';

interface Props {
  loginUser: LoginUser;
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
  height: 40px;
  overflow-y: scroll;
  line-height: 1.875rem;
  overflow: scroll;
  -ms-overflow-style: none;
  scrollbar-width: none;
  display: flex;
  flex-direction: row;
  padding: 0.125rem 0.25rem;
  flex-wrap: wrap;
  margin-bottom: 5px;
  align-items: center;
  border: 1px solid #b6b6b6;
  border-radius: 5px;
  background-color: white;
}
.mention-input-user::-webkit-scrollbar {
  display: none;
}
.mention-user {
  padding: 0px 0.25rem;
  margin-bottom: 0.25rem;
  padding: 0 0.5rem;
  border-radius: 12px;
  overflow-x: scroll;
  background-color: rgb(6 182 212);
  color: white;
}
.search-user-input {
  display: inline-block;
  flex-grow: 1;
  color: #b6b6b6;
}
.search-user-input:empty:before {
  content: attr(placeholder);
  pointer-events: none;
  display: block;
  color: #b6b6b6;
  font-size: 90%;
}
.search-user-input.place-hide:empty:before {
  display: none;
}
.search-user-input:focus {
  outline: 0px solid transparent;
}
.search-user-list {
  position: absolute;
  bottom: 9rem;
  list-style: none;
  overflow-y: auto;
  max-height: 10rem;
  min-height: 1rem;
  border-radius: 4px;
  background-color: rgb(229 231 235);
}
.search-user-list :last-child {
  border-bottom: none;
}
.p-2 {
  padding: 0.75rem;
  cursor: pointer;
  background-color: rgb(229 231 235);
  border-bottom: 1px solid rgb(210, 211, 213);
}
.p-2 :hover {
  background-color: rgb(6 182 212);
  color: white;
}
</style>
