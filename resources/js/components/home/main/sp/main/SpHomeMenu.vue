<template>
  <div v-if="selectedUserName" class="user_filter">
    <div class="inner">
      <i class="fa fa-user fa_blank"></i>
      <div class="user_filter_btn">
        {{ selectedUserName }}
        <fa icon="fa-xmark" @click="homeClicked" />
      </div>
    </div>
  </div>
  <div v-if="selectedDivisionName" class="user_filter">
    <div class="inner">
      <i class="fa fa-building fa_blank"></i>
      <div class="user_filter_btn">
        {{ selectedDivisionName }}
        <fa icon="fa-xmark" @click="selectDivisionReset" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { LoginUser } from '/@/js/models/User';
interface Props {
  loginUser: LoginUser;
  selectedUserName: string;
  selectedDivisionName: string;
  homeClicked: () => void;
  updateDivisionCodes: (cds: number[]) => void;
  updateHomeMode: (flg: boolean) => void;
  selectDivisionReset: () => void;
}
const props = defineProps<Props>();
</script>
