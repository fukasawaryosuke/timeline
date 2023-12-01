<script lang="ts" setup>
import { IIhsForm } from '/@/js/models/admin/IhsForm';
interface Props {
  ihs: IIhsForm;
  formList: any[];
  fileName?: string;
  btnString: string;
}
defineProps<Props>();
interface Emits {
  (e: 'return'): void;
  (e: 'update'): void;
}
defineEmits<Emits>();
</script>

<template>
  <div class="main_area">
    <div>
      <span>カテゴリ: {{ ihs.system_name }}</span>
    </div>
    <div>
      <span>説明: {{ ihs.description }}</span>
    </div>
    <div>
      <span>ファイル名: {{ fileName ?? '' }}</span>
      <img v-if="ihs.icon_path" :src="ihs.icon_path" alt="icon" />
    </div>
    <div>
      <span>投稿フォーム: {{ ihs.getFormTitle(formList) }}</span>
    </div>
    <div>
      <span>公開設定: {{ ihs.getStatusStr() }}</span>
    </div>
    <button @click="$emit('return')">戻る</button>
    <button @click="$emit('update')">{{ btnString }}</button>
  </div>
</template>
