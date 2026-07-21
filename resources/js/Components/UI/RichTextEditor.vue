<script setup>
import { watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';

const props = defineProps({
    modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const editor = useEditor({
    content: props.modelValue,
    extensions: [
        StarterKit,
        Link.configure({ openOnClick: false, autolink: true }),
    ],
    onUpdate: ({ editor }) => emit('update:modelValue', editor.getHTML()),
});

// Keep the editor in sync if modelValue changes from outside (e.g. switching
// the EN/BN tab in CustomPages/Form.vue — each tab holds its own HTML string).
watch(() => props.modelValue, (value) => {
    if (editor.value && value !== editor.value.getHTML()) {
        editor.value.commands.setContent(value, false);
    }
});

onBeforeUnmount(() => editor.value?.destroy());

function setLink() {
    const url = window.prompt('URL');
    if (url === null) return;
    if (url === '') {
        editor.value.chain().focus().unsetLink().run();
        return;
    }
    editor.value.chain().focus().setLink({ href: url }).run();
}
</script>

<template>
    <div class="rounded-lg border border-[var(--border)] bg-[var(--surface2)] overflow-hidden">
        <div v-if="editor" class="flex flex-wrap gap-1 p-2 border-b border-[var(--border)]">
            <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="editor.isActive('bold') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs font-bold">B</button>
            <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="editor.isActive('italic') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs italic">I</button>
            <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="editor.isActive('strike') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs line-through">S</button>
            <span class="w-px bg-[var(--border)] mx-1"></span>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="editor.isActive('heading', { level: 2 }) ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs font-semibold">H2</button>
            <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="editor.isActive('heading', { level: 3 }) ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs font-semibold">H3</button>
            <span class="w-px bg-[var(--border)] mx-1"></span>
            <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="editor.isActive('bulletList') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs">• List</button>
            <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="editor.isActive('orderedList') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs">1. List</button>
            <span class="w-px bg-[var(--border)] mx-1"></span>
            <button type="button" @click="setLink" :class="editor.isActive('link') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs">Link</button>
            <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="editor.isActive('blockquote') ? 'bg-[var(--primary)] text-white' : ''" class="px-2 py-1 rounded text-xs">"</button>
            <span class="w-px bg-[var(--border)] mx-1"></span>
            <button type="button" @click="editor.chain().focus().undo().run()" class="px-2 py-1 rounded text-xs">↶</button>
            <button type="button" @click="editor.chain().focus().redo().run()" class="px-2 py-1 rounded text-xs">↷</button>
        </div>
        <EditorContent :editor="editor" class="prose max-w-none p-4 min-h-[200px] focus:outline-none [&_.ProseMirror]:outline-none [&_.ProseMirror]:min-h-[180px]" />
    </div>
</template>
