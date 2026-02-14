<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';

// تعریف پروپس‌هایی که از سمت کنترلر لاراول می‌آیند
const props = defineProps({
    accounts: Array,
    categories: Array
});

// استفاده از useForm برای مدیریت داده‌های فرم و خطاها
const form = useForm({
    account_id: '',
    category_id: '',
    amount: null,
    type: 'expense', // پیش‌فرض: هزینه
    date: new Date().toISOString().split('T')[0], // تاریخ امروز
    description: '',
});

const submit = () => {
    // ارسال داده‌ها به متد store در کنترلر
    form.post(route('transactions.store'), {
        onSuccess: () => form.reset(), // بعد از موفقیت فرم خالی شود
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="max-w-2xl mx-auto p-6 bg-white shadow-md mt-10">
            <h2 class="text-xl font-bold mb-4">ثبت تراکنش جدید</h2>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label>نوع تراکنش:</label>
                    <select v-model="form.type" class="w-full border p-2">
                        <option value="expense">خروجی (هزینه)</option>
                        <option value="income">ورودی (درآمد)</option>
                    </select>
                </div>

                <div>
                    <label>مبلغ:</label>
                    <input v-model="form.amount" type="number" step="0.01" class="w-full border p-2" required>
                </div>

                <div>
                    <label>حساب مربوطه:</label>
                    <select v-model="form.account_id" class="w-full border p-2" required>
                        <option v-for="account in accounts" :key="account.id" :value="account.id">
                            {{ account.name }} (موجودی: {{ account.balance }})
                        </option>
                    </select>
                </div>

                <div>
                    <label>دسته‌بندی:</label>
                    <select v-model="form.category_id" class="w-full border p-2" required>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <button type="submit" :disabled="form.processing" class="bg-blue-600 text-white px-4 py-2">
                    {{ form.processing ? 'در حال ثبت...' : 'ذخیره تراکنش' }}
                </button>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
