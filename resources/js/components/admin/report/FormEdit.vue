<script setup lang="ts">
import {
  IReportForm,
  ReportForm,
  ReportContentsItem,
} from '/@/js/models/admin/ReportForm';

interface Props {
  data: IReportForm;
  edit: boolean;
}
defineProps<Props>();
interface Emits {
  (e: 'selected', event: any, index: number): void;
  (e: 'updateName', event: any): void;
  (e: 'updateRequired', event: any): void;
  (e: 'updateTitle', event: any, index: number): void;
  (e: 'addContents'): void;
  (e: 'updateContentsRequired', event: any, index: number): void;
  (e: 'updateContentsReportVisible', event: any, index: number): void;
  (e: 'deleteContents', index: number): void;
  (e: 'addChoice', index: number): void;
  (e: 'updateChoice', index: number, choiceIndex: number, value: string): void;
  (e: 'deleteChoice', index: number, choiceIndex: number): void;
}
defineEmits<Emits>();
</script>

<template>
  <label>追加されたフォーム</label>
  <div
    v-for="(c, i) in data.diary.contents"
    :key="`contents${String(i)}`"
    class="borderbox"
  >
    <div class="input_box">
      <div class="input_box flex_2col">
        <!-- <label>No. {{ i + 1 }}</label>
          <label>Key. {{ c.key }}</label> -->
        <div>
          <label :for="`contentstitle${String(i)}`">
            項目名
            <span class="redtxt smalltxt">（必須）</span>
          </label>
          <input
            :id="`contentstitle${String(i)}`"
            type="text"
            :value="c.title"
            @input="$emit('updateTitle', $event, i)"
          />
          <input
            type="checkbox"
            :value="c.require"
            :checked="c.require"
            @change="$emit('updateContentsRequired', $event, i)"
          />
          入力必須
          <input
            type="checkbox"
            :value="c.reportVisible"
            :checked="c.reportVisible"
            @change="$emit('updateContentsReportVisible', $event, i)"
          />
          カードレポートに表示する
        </div>
        <div>
          <button @click="$emit('deleteContents', i)">
            <i class="fa fa-trash" aria-hidden="true"></i>
          </button>
        </div>
      </div>
      <div class="input_box">
        <label>
          フォーム種類
          <span class="redtxt smalltxt">（必須）</span>
        </label>
        <div class="select_wrap">
          <select :value="c.contentType" @change="$emit('selected', $event, i)">
            <option value="">フォーム種類</option>
            <option
              v-for="s in ReportContentsItem.CONTENTS_TYPE_MASTER"
              :key="`confirmformtype${s.title}`"
              :value="s.contentType"
            >
              {{ s.title }}
            </option>
          </select>
        </div>
      </div>
      <div
        v-if="
          c.contentType === ReportForm.TYPE_CHOICE ||
          c.contentType === ReportForm.TYPE_SELECT
        "
        class="input_box"
      >
        <div
          v-for="(ch, chi) in c.choices"
          :key="`choices${String(i)}${String(chi)}`"
          class="input_box flex_2col"
        >
          <div class="preview_main">
            選択肢タイトル
            <input
              type="text"
              :value="ch.title"
              @input="$emit('updateChoice', i, chi, $event.target.value)"
            />
            <!-- <label>値 {{ ch.value }} TODO: 削除予定</label> -->
          </div>
          <div>
            <button @click="$emit('deleteChoice', i, chi)">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="input_box align_right">
          <button @click="$emit('addChoice', i)">
            <i class="fa fa-plus-circle fa_blank" aria-hidden="true"></i>
            選択肢を追加
          </button>
        </div>
      </div>
      <!-- <div class="input_box align_right">
        <button class="redbtn btn" @click="$emit('deleteContents', i)">
          <i class="fa fa-trash fa_blank" aria-hidden="true"></i>
          項目を削除
        </button>
      </div> -->
    </div>
  </div>
</template>
