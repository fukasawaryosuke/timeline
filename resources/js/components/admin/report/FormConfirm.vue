<script setup lang="ts">
import { ReportForm, IReportForm } from '/@/js/models/admin/ReportForm';

interface Props {
  data: IReportForm;
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
  <section>
    <div>
      <span>名前: {{ data.name }}</span>
    </div>
    <div>
      <span>必須: {{ data.required }}</span>
    </div>
    <div
      v-for="(c, i) in data.diary.contents"
      :key="`confirm${i}`"
      class="content"
    >
      <div>No.{{ i + 1 }}</div>
      <div>
        <label>
          タイトル
          <span>{{ c.title }}</span>
        </label>
      </div>
      <div>
        <label>
          形式
          <span>{{ c.convertContentType() }}</span>
        </label>
      </div>
      <div
        v-if="
          c.contentType === ReportForm.TYPE_CHOICE ||
          c.contentType === ReportForm.TYPE_SELECT
        "
      >
        <ul>
          <li v-for="(ch, chi) in c.choices" :key="`confirmChoice${i}${chi}`">
            {{ ch.title }}
          </li>
        </ul>
      </div>
    </div>
    <button @click="$emit('return')">戻る</button>
    <button @click="$emit('update')">{{ btnString }}</button>
  </section>
</template>
