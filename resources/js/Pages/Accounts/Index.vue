<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    accounts: Array
});

// فرم برای ساخت حساب جدید
const form = useForm({
    name: '',
    balance: 0
});

const submit = () => {
    form.post(route('accounts.store'), {
        onSuccess: () => form.reset()
    });
};

const deleteAccount = (id) => {
    if (confirm('آیا از حذف این حساب مطمئن هستید؟')) {
        router.delete(route('accounts.destroy', id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 px-4 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4">افزودن حساب جدید</h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block">نام حساب (مثلاً بانک پاسارگاد):</label>
                            <input v-model="form.name" type="text" class="w-full border-gray-300 rounded" required />
                        </div>
                        <div>
                            <label class="block">موجودی اولیه:</label>
                            <input v-model="form.balance" type="number" class="w-full border-gray-300 rounded" required />
                        </div>
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">ذخیره</button>
                    </form>
                </div>

                <div class="bg-white p-6 shadow rounded-lg">
                    <h3 class="text-lg font-bold mb-4">لیست حساب‌های من</h3>
                    <table class="w-full text-right">
                        <thead>
                        <tr class="border-b">
                            <th class="py-2">نام حساب</th>
                            <th>موجودی</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="account in accounts" :key="account.id" class="border-b">
                            <td class="py-2">{{ account.name }}</td>
                            <td>{{ new Intl.NumberFormat('fa-IR').format(account.balance) }} ریال</td>
                            <td>
                                <button @click="deleteAccount(account.id)" class="text-red-600">حذف</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
