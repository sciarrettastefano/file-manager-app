<template>
    <q-icon size="1.3rem" :name="fileIcon" />
</template>


<script setup>
// Imports
import { isAudio, isExcel, isImage, isPDF, isText, isVideo, isWord, isZip } from '@/Helper/file-helper';
import { computed } from 'vue';

// Uses


// Refs


// Props & Emit
const props = defineProps({
    file: {
        type: Object,
        default: {}
    }
})

// Computed
const fileIcon = computed(() => {
  if (props.file.is_folder) return 'fa-regular fa-folder'
  if (isImage(props.file)) return 'fa-regular fa-image'
  if (isPDF(props.file)) return 'fa-regular fa-file-pdf'
  if (isAudio(props.file)) return 'fa-regular fa-file-audio'
  if (isVideo(props.file)) return 'fa-regular fa-video'
  if (isWord(props.file)) return 'fa-regular fa-file-word'
  if (isExcel(props.file)) return 'fa-regular fa-file-excel'
  if (isZip(props.file)) return 'fa-regular fa-file-zipper'
  if (isText(props.file)) return 'fa-regular fa-file-lines'
  return 'fa-regular fa-paperclip'
})

// Methods


// Hooks


</script>


<style scoped>

</style>
