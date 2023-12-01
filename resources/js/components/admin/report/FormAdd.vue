<script setup lang="ts">
import {
  IReportForm,
  IReportContentsItem,
  ReportContentsItem,
} from '/@/js/models/admin/ReportForm';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';

interface Props {
  data: IReportForm;
  edit: boolean;
  newContents: IReportContentsItem;
}
const props = defineProps<Props>();
interface Emits {
  (e: 'updateName', event: any): void;
  (e: 'modeChange', event: any): void;
  (e: 'updateRequired', event: any): void;
  (e: 'addContents'): void;
  (e: 'confirm'): void;
  (e: 'delete'): void;
  (e: 'addChoice', index: number): void;
  (e: 'updateChoice', index: number, choiceIndex: number, value: string): void;
  (e: 'deleteChoice', index: number, choiceIndex: number): void;
  (e: 'update:newContents', model: IReportContentsItem): void;
}
const emits = defineEmits<Emits>();
/** v-model update method */
const updateContents = (key, val) => {
  const copy = new ReportContentsItem(props.newContents);
  copy[key] = val;
  emits('update:newContents', copy);
};
const updateChoice = (i, val) => {
  const copy = new ReportContentsItem(props.newContents);
  copy.updateChoice(i, val);
  emits('update:newContents', copy);
};
const deleteChoice = (i) => {
  const copy = new ReportContentsItem(props.newContents);
  copy.deleteChoice(i);
  emits('update:newContents', copy);
};
const addChoice = () => {
  const copy = new ReportContentsItem(props.newContents);
  copy.addChoice();
  emits('update:newContents', copy);
};
</script>

<template>
  <div class="col2_50">
    <div class="input_box">
      <label>
        投稿フォーム名
        <span class="redtxt smalltxt">（必須）</span>
      </label>
      <input
        type="text"
        :value="data.name"
        @input="$emit('updateName', $event)"
      />
    </div>
    <div class="input_box">
      <label>必読フラグ</label>
      <input
        type="checkbox"
        :value="data.required"
        :checked="data.required"
        @input="$emit('updateRequired', $event)"
      />
    </div>
    <label>フォーム追加</label>
    <div class="borderbox">
      <div class="input_box">
        <label for="contentstitle">
          項目名
          <span class="redtxt smalltxt">（必須）</span>
        </label>
        <input
          id="contentstitle"
          :value="newContents.title"
          type="text"
          @input="updateContents('title', $event.target.value)"
        />
        <input
          type="checkbox"
          :value="newContents.require"
          :checked="newContents.require"
          @change="updateContents('require', $event.target.checked)"
        />
        入力必須
        <input
          type="checkbox"
          :value="newContents.reportVisible"
          :checked="newContents.reportVisible"
          @input="updateContents('reportVisible', $event.target.checked)"
        />
        カードレポートに表示する
      </div>
      <div class="input_box">
        <label>
          フォーム種類
          <span class="redtxt smalltxt">（必須）</span>
        </label>
        <div class="select_wrap">
          <select
            :value="newContents.contentType"
            @change="updateContents('contentType', $event.target.value)"
          >
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
          newContents.contentType === REPORT_CONTENT_TYPE.CHOICE ||
          newContents.contentType === REPORT_CONTENT_TYPE.SELECT
        "
        class="input_box"
      >
        <div
          v-for="(ch, i) in newContents.choices"
          :key="`addChoices${String(i)}}`"
          class="input_box flex_2col"
        >
          <div class="preview_main">
            選択肢タイトル
            <input
              type="text"
              :value="ch.title"
              @input="updateChoice(i, $event.target.value)"
            />
          </div>
          <div>
            <button @click="deleteChoice(i)">
              <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
          </div>
        </div>
        <div class="input_box align_right">
          <button @click="addChoice">
            <i class="fa fa-plus-circle fa_blank" aria-hidden="true"></i>
            選択肢を追加
          </button>
        </div>
      </div>
      <div class="input_box align_right">
        <button class="deepbluebtn btn" @click="$emit('addContents')">
          <i class="fa fa-plus-circle fa_blank" aria-hidden="true"></i>
          追加
        </button>
      </div>
    </div>
    <div class="input_box"></div>
    <div class="input_box">
      <label for="formpreview">フォームプレビュー</label>
      <input
        id="formpreview"
        type="checkbox"
        @change="$emit('modeChange', $event)"
      />
    </div>
    <div v-if="edit" class="main_header flex_end">
      <button class="redbtn btn" @click="$emit('delete')">
        <i class="fa fa-trash fa_blank" aria-hidden="true"></i>
        削除
      </button>
    </div>
  </div>
</template>
