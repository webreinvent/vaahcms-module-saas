<script setup>
import {vaah} from '../pinia/vaah.js'

import { useAttrs ,computed} from 'vue'

const attrs = useAttrs()

const props = defineProps({
  label: {
    type: String,
    default: null
  },
  label_width: {
    type: String,
    default: '150px'
  },
  value:{
    default: null,
  },
  type: {
    type: String,
    default: 'text'
  },
  can_copy:{
    type: Boolean,
    default: false
  },
  is_link:{
    type: String,
  },
    btn:{
      type: Boolean,
      default: true
    },
    arrs: {
        type: Array,
        default: null,
    },
})


</script>
<template>
  <tr v-bind="$attrs">
    <td :style="{width: label_width}"><b>{{vaah().toLabel(label)}}</b></td>
    <template v-if="can_copy">
      <td v-html="value"></td>
      <td style="width: 40px;">
        <Button icon="pi pi-copy" @click="vaah().copy(value)" class=" p-button-text"></Button>
      </td>
    </template>
    <template v-else-if="type==='user'">
      <td colspan="2" >

        <template v-if="typeof value === 'object' && value !== null">
            <span v-if="value.name">

                <Button  @click="vaah().copy(value.id)"  class="p-button-outlined p-button-secondary p-button-sm" v-if="btn">
                    {{value.name}}
                </Button>
                <span v-else>
                    <template v-for="(val,index) in value">
                        <tr v-if="props.arrs && props.arrs.includes(index)">
                            <td :style="{width: label_width}"><b>{{vaah().toLabel(index)}}</b>
                            </td>
                            <span v-if="!index.indexOf('is_')">
                                <Tag value="Yes" v-if="val===1 || val" severity="success"></Tag>
                                <Tag v-else value="No" severity="danger"></Tag>
                            </span>
                            <span v-else>
                            {{val}}
                            </span>
                        </tr>
                    </template>
                </span>
            </span>
            <span v-else>
                <template v-for="(val,index) in value"><tr><td :style="{width: label_width}"><b>{{vaah().toLabel(index)}}</b></td> {{val}}</tr></template>
            </span>
        </template>

      </td>
    </template>
    <template v-else-if="type==='yes-no'">
      <td colspan="2">
        <Tag value="Yes" v-if="value===1" severity="success"></Tag>
        <Tag v-else value="No" severity="danger"></Tag>
      </td>
    </template>
    <template v-else-if="props.is_link">
      <td><a :href="props.is_link" target="_blank">{{props.is_link}}</a></td>
      <td style="width: 40px;">
        <Button icon="pi pi-copy" @click="vaah().copy(value)" class=" p-button-text"></Button>
      </td>
    </template>
    <template v-else>
      <td  colspan="2" class="text-editor-code" v-html="value"></td>
    </template>

  </tr>
</template>


<style >
.text-editor-code > pre {
  white-space: pre-wrap;
}
</style>
