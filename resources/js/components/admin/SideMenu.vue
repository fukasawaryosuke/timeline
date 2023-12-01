<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import axios from 'axios';

//NOTE:複数のコンポーネントでSideMenuを読み込んでいる為ここで取得
const isHumanAcademy = ref<boolean | null>(null);

axios.get('/api/users/loginuser/isHumanAcademy').then((res) => {
  isHumanAcademy.value = res.data;
});

watch(isHumanAcademy, (newValue) => {
  if (newValue) {
    linkList.unshift(companyLink);
  } else {
    linkList.unshift(organizationLink);
  }
});

const companyLink = {
  title: '法人一覧',
  link: '/admin/company',
};

const organizationLink = {
  title: '部門一覧',
  link: '/admin/organization',
};

const linkList = reactive([
  {
    title: 'アカウント一覧',
    link: '/admin/account',
  },
  {
    title: 'カテゴリ一覧',
    link: '/admin/in_house_system',
  },
  {
    title: '投稿フォーム一覧',
    link: '/admin/report_form',
  },
]);

const subLink = reactive([
  // {
  //   title: 'カテゴリ別投稿数',
  //   link: '/admin/in_house_system/statistics',
  // },
  {
    title: 'アカウント別投稿数',
    link: '/admin/account/statistics',
  },
  // {
  //   title: '事業別集計',
  //   link: '/admin/account/statistics',
  // },
]);
</script>
<template>
  <div class="sidebar">
    <div id="menu_header">
      assistタイムライン
      <br />
      管理画面
    </div>
    <div id="menu_list">
      <ul>
        <li v-for="l in linkList" :key="l.title">
          <router-link :to="l.link">{{ l.title }}</router-link>
        </li>
        <li>
          分析
          <ul>
            <li v-for="l in subLink" :key="l.title">
              <router-link :to="l.link">{{ l.title }}</router-link>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
  color: #000;
}
a:hover {
  color: #06b6d4;
}
</style>
