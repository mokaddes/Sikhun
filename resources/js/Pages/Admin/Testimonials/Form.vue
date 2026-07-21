<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Components/Layout/AdminLayout.vue';

const props = defineProps({ testimonial: Object });
const isEdit = !!props.testimonial;

const form = useForm({
    student_name: props.testimonial?.student_name ?? '',
    student_role: props.testimonial?.student_role ?? '',
    quote: props.testimonial?.quote ?? '',
    sort_order: props.testimonial?.sort_order ?? 0,
    is_published: props.testimonial?.is_published ?? true,
});

function submit() {
    isEdit ? form.put(`/admin/testimonials/${props.testimonial.id}`) : form.post('/admin/testimonials');
}
</script>

<template>
    <Head :title="isEdit ? 'Edit Testimonial' : 'Add Testimonial'" />
    <AdminLayout>
        <h1 class="font-heading text-2xl font-extrabold mb-6">{{ isEdit ? 'Edit Testimonial' : 'Add Testimonial' }}</h1>
        <form @submit.prevent="submit" class="max-w-xl space-y-5 rounded-xl border border-[#2a2a38] bg-[#111118] p-6">
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Student Name</label>
                <input v-model="form.student_name" type="text" required class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Role / Context (e.g. "HSC Student, Dhaka")</label>
                <input v-model="form.student_role" type="text" class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Quote</label>
                <textarea v-model="form.quote" rows="4" required class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white"></textarea>
                <p class="text-xs text-[#7a7a9a] mt-1">Use the student's actual words — this appears publicly as a real testimonial.</p>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5 text-[#9a9ab8]">Sort Order</label>
                <input v-model="form.sort_order" type="number" min="0" class="w-full px-4 py-2.5 rounded-lg bg-[#18181f] border border-[#2a2a38] text-white" />
            </div>
            <label class="flex items-center gap-2 text-sm text-[#9a9ab8]"><input v-model="form.is_published" type="checkbox" class="rounded" /> Published</label>
            <button type="submit" :disabled="form.processing" class="px-5 py-2.5 rounded-lg bg-[#e8935c] hover:bg-[#dc8148] text-white text-sm font-semibold disabled:opacity-60">
                {{ form.processing ? 'Saving...' : 'Save' }}
            </button>
        </form>
    </AdminLayout>
</template>
