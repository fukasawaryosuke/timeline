<script setup lang="ts">
import { LoginUser } from '/@/js/models/User';
import { computed, onMounted, ref } from 'vue';
import axios from 'axios';

interface Props {
  loginUser: LoginUser;
  homeClicked: () => void;
  myDiaryClicked: () => void;
  selectedUser: (path: string) => void;
  selectedUserOnlyDiary: (index: number) => void;
}
const props = defineProps<Props>();
</script>

<template>
  <nav class="gnav" id="sns_header">
    <ul>
      <li @click="myDiaryClicked">自分の投稿</li>
      <li @click="homeClicked">全ての投稿</li>
    </ul>
  </nav>
</template>
