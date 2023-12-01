<script lang="ts" setup>
import { IReportForm } from '/@/js/models/admin/ReportForm';
import Datepicker from 'vue3-datepicker';
import { REPORT_CONTENT_TYPE } from '/@/js/contarcts/ReportContentType';

interface Props {
  data: IReportForm;
}
defineProps<Props>();
</script>

<template>
  <div class="col2_50 preview_area">
    <label>プレビュー</label>
    <div class="borderbox">
      <div v-if="data.required" class="input_box">
        <label>必読投稿</label>
        <input type="checkbox" />
      </div>
      <div
        v-for="(c, i) in data.diary.contents"
        :key="`preview$${String(i)}`"
        class="input_box flex_2col"
      >
        <div class="preview_main">
          <label>
            {{ c.title }}
            <span v-if="c.require" class="redtxt smalltxt">（必須）</span>
          </label>
          <input
            v-if="c.contentType === REPORT_CONTENT_TYPE.TEXT"
            type="text"
            value=""
          />
          <textarea
            v-else-if="c.contentType === REPORT_CONTENT_TYPE.TEXTAREA"
          ></textarea>
          <form v-else-if="c.contentType === REPORT_CONTENT_TYPE.SELECT">
            <div v-for="ch in c.choices" :key="`previewselect${ch.value}`">
              <input type="radio" :name="`previewselect${String(i)}`" />
              {{ ch.title }}
            </div>
          </form>
          <form v-else-if="c.contentType === REPORT_CONTENT_TYPE.CHOICE">
            <div v-for="ch in c.choices" :key="`previewchoice${ch.value}`">
              <input type="checkbox" />
              {{ ch.title }}
            </div>
          </form>
          <input
            v-else-if="c.contentType === REPORT_CONTENT_TYPE.IMAGE"
            type="file"
            @click="$event.preventDefault()"
          />
          <input
            v-else-if="c.contentType === REPORT_CONTENT_TYPE.MOVIE"
            type="file"
            @click="$event.preventDefault()"
          />
          <Datepicker
            v-else-if="c.contentType === REPORT_CONTENT_TYPE.DATETIME"
            id=""
            type="date"
          />
          <div v-if="c.contentType === REPORT_CONTENT_TYPE.TENKI" class="tenki">
            <input
              :id="`sunny${String(i)}`"
              v-model="c.content"
              type="radio"
              value="晴れ"
              class="hidden"
            />
            <label
              :for="`sunny${String(i)}`"
              class="tenki_label border p-2 w-12 inline-block text-center rounded-md mr-2 last:mr-0 text-gray-400 mx-1 hover:text-gray-700"
            >
              <fa icon="fa-sun" />
            </label>
            <input
              :id="`cloudy${String(i)}`"
              v-model="c.content"
              type="radio"
              value="曇り"
              class="hidden"
            />
            <label
              :for="`cloudy${String(i)}`"
              class="tenki_label border p-2 w-12 inline-block text-center rounded-md mr-2 last:mr-0 text-gray-400 mx-1 hover:text-gray-700"
            >
              <fa icon="fa-cloud" />
            </label>
            <input
              :id="`rainy${String(i)}`"
              v-model="c.content"
              type="radio"
              value="雨"
              class="hidden"
            />
            <label
              :for="`rainy${String(i)}`"
              class="tenki_label border p-2 w-12 inline-block text-center rounded-md mr-2 last:mr-0 text-gray-400 mx-1 hover:text-gray-700"
            >
              <fa icon="fa-cloud-rain" />
            </label>
            <input
              :id="`thunder${String(i)}`"
              v-model="c.content"
              type="radio"
              value="雷"
              class="hidden"
            />
            <label
              :for="`thunder${String(i)}`"
              class="tenki_label border p-2 w-12 inline-block text-center rounded-md mr-2 last:mr-0 text-gray-400 mx-1 hover:text-gray-700"
            >
              <fa icon="fa-thunderstorm" />
            </label>
            <span class="ml-2 text-slate-600 text-sm">
              {{ c.content }}
            </span>
          </div>
        </div>
        <!-- <div>
          <button>
            <i class="fa fa-trash" aria-hidden="true"></i>
          </button>
        </div> -->
      </div>
    </div>
  </div>
</template>
<style scoped>
.tenki {
  display: flex;
  flex-direction: row;
}
.tenki_label {
  margin-right: 4px;
  padding: px;
  border-radius: 4px;
  border: 1px solid #000;
}
.tenki input[type='radio'] {
  display: none;
}
input[type='radio']:checked + .tenki_label {
  background-color: rgb(55 65 81);
}
input[type='radio']:checked + .tenki_label fa,
input[type='radio']:checked + .tenki_label svg {
  color: #fff;
}
</style>
