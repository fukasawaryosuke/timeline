<script lang="ts" setup>
import axios from 'axios';
import { computed, onMounted, ref } from 'vue';


interface Props {
    selectedUser: (path: string) => void;
    selectedUserOnlyDiary: (index: number) => void;
}
const props = defineProps<Props>();

interface Emits {
  (e: 'modeChange', mode: number): void;
}
const emit = defineEmits<Emits>();

interface UserName {
    id: number;
    user_name: string;
}

const user_name_list = ref<UserName[]>([]);
const search_flg = ref<boolean>(false);

onMounted(() => {
    axios.get('/api/users/user_name').then((res) => {
        user_name_list.value = res.data;
        console.log(user_name_list.value);
    });
});

const searchKeyword = ref<string>('');
const filteredItems = computed(() => {
    const filteredList = user_name_list.value.filter(item => item.user_name.includes(searchKeyword.value));
  return filteredList.slice(0, 5); // 最大5件に制限
});


const user_data = ref<UserName[]>([]);
const err_flg = ref<boolean>(false);

const selectedUser = (name: string) => {
  user_data.value = user_name_list.value.filter((item) => item.user_name == name);
  if (user_data.value.length == 0) {
    err_flg.value = true;
    return;
  }else{
    props.selectedUser(name);
    props.selectedUserOnlyDiary(user_data.value[0].id);
    emit('modeChange', 0);
    err_flg.value = false;
    search_flg.value = false;
    searchKeyword.value = '';
  }

};

const clickName = (name:string)=>{0
  search_flg.value = false;
  searchKeyword.value = name;
  return
}

const keywordCount = () => {
  if (searchKeyword.value.length == 0) {
    search_flg.value = false;
  } else {
    search_flg.value = true;
  }
};
</script>

<template>
    <div class="search_user_main">
        <div class="search_user_contents">
            <h1 class="title">ユーザー検索</h1>
            <div class="search_area">
                <input type="text" placeholder="ユーザー名" @input="keywordCount" v-model="searchKeyword">
                <ul id="searchResults" class="search-results" v-if="search_flg">
                    <li v-for="(user) in filteredItems" :value="user.id" @click="clickName(user.user_name)">{{ user.user_name }}</li>
                </ul>
            </div>
            <button class="search_button bg-assist-grad" @click="selectedUser(searchKeyword)">表示</button>
        </div>
    </div>
</template>

<style scoped>
.search_user_main {
    background-color: white;
    height: 100vh;
}

.search_user_contents{
    display: flex;
    flex-flow: column;
    padding-top: 85px;
    text-align: center;
}
.title{
    font-size: 15px;
    font-weight: bold;
    color: #333333;
}
.search_button{
    border-radius: 100vh;
    border: none;
    margin: 12px;
    font-size: 16px;
    font-weight: bold;
    color: white;
    height: 50px;
    background: #0BB9CD 0% 0% no-repeat padding-box;
}
.search_area{
    width: auto;
    margin: 30px 28px 0px 28px;
    height: 180px;
}

.search-results {
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: block;
    z-index: 999;
}

.search-results li {
    list-style-type: none;
    padding-left: 5px;
    text-align: left;
}

.search-results li:hover {
    background-color: #f2f2f2;
    cursor: pointer;
}
</style>