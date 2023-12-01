<script setup lang="ts">
import { NoticeListModel } from '/@/js/models/Notice';
import { Organization } from '/@/js/models/User';
import { MustReadListModel } from '/@/js/models/MustRead';
// import { OrganizationTreeListModel } from '/@/js/models/Organization';

interface Props {
  noticeList: NoticeListModel;
  mustReadList: MustReadListModel;
  snsChange: (val: number) => void;
  tabNumber: number;
  selectedIhsId: number;
}
const props = defineProps<Props>();
const organizationsClicked = () => {
  props.snsChange(2);
};
</script>

<template>
  <!-- <div class="inner"> -->
  <div class="inner bg-assist-grad">
    <nav class="footer-link">
      <ul>
        <li
          id="active"
          :class="tabNumber === 3 ? 'select' : ''"
          @click="snsChange(3)"
        >
          <i class="fa fa-book" />
          <span>カテゴリ</span>
        </li>
        <li v-if="selectedIhsId != 1" class="hidden">
          <i class="fa fa-users" />
          <span>組織</span>
        </li>
        <li
          v-else
          @click="organizationsClicked"
          :class="tabNumber === 2 ? 'select' : ''"
          id="orzTab"
        >
          <i class="fa fa-users" />
          <span>組織</span>
        </li>
        <li @click="snsChange(5)" :class="tabNumber === 5 ? 'select' : ''">
          <div class="organization">
            <i class="fa fa-exclamation-triangle" />
            <span>必読投稿</span>
            <span v-if="mustReadList.latestCount !== 0" class="notice_badge">
              {{ String(mustReadList.latestCount) }}
            </span>
          </div>
        </li>
        <li @click="snsChange(4)" :class="tabNumber === 4 ? 'select' : ''">
          <div class="organization">
            <i class="fa fa-bell color" aria-hidden="true" />
            <span>お知らせ</span>
            <div v-if="noticeList.newNoticeCount() !== 0" class="notice_badge">
              {{ String(noticeList.newNoticeCount()) }}
            </div>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</template>
