<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    categories: Array
});

const form = useForm({
    name: '',
    type: 'expense' // مقدار پیش‌فرض: هزینه
});

const submit = () => {
    form.post(route('categories.store'), {
        onSuccess: () => form.reset('name')
    });
};

const deleteCategory = (id) => {
    if (confirm('آیا از حذف این دسته‌بندی مطمئن هستید؟')) {
        router.delete(route('categories.destroy', id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 px-4 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4">تعریف دسته‌بندی جدید</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block">نام دسته (مثلاً: اجاره، حقوق، خرید):</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded" required />
                        </div>
                        <div>
                            <label class="block">نوع دسته‌بندی:</label>
                            <select v-model="form.type" class="w-full border-gray-300 rounded">
                                <option value="expense">هزینه (خروجی)</option>
                                <option value="income">درآمد (ورودی)</option>
                            </select>
                        </div>
                        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">ذخیره دسته</button>
                    </form>
                </div>

                <div class="bg-white p-6 shadow rounded-lg text-right" dir="rtl">
                    <h3 class="text-lg font-bold mb-4">دسته‌بندی‌های من</h3>
                    <table class="w-full text-right">
                        <thead>
                        <tr class="border-b">
                            <th class="py-2">نام</th>
                            <th>نوع</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="cat in categories" :key="cat.id" class="border-b">
                            <td class="py-2">{{ cat.name }}</td>
                            <td>
                                    <span :class="cat.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                        {{ cat.type === 'income' ? 'درآمد' : 'هزینه' }}
                                    </span>
                            </td>
                            <td>
                                <button @click="deleteCategory(cat.id)" class="text-red-500 text-sm">حذف</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
