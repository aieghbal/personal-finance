<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import DatePicker from 'vue3-persian-datetime-picker';

const props = defineProps({
    transactions: Array,
    accounts: Array,
    categories: Array
});

const editMode = ref(false);
const editId = ref(null);

const form = useForm({
    account_id: '',
    category_id: '',
    amount: '',
    date: new Date().toISOString().split('T')[0],
    description: ''
});

const submit = () => {
    if (editMode.value) {
        form.put(route('transactions.update', editId.value), {
            onSuccess: () => {
                editMode.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('transactions.store'), {
            onSuccess: () => form.reset('amount', 'description')
        });
    }
};

const editTransaction = (tr) => {
    editMode.value = true;
    editId.value = tr.id;
    form.account_id = tr.account_id;
    form.category_id = tr.category_id;
    form.amount = tr.amount;

    // حل مشکل نمایش تاریخ در Input:
    // تاریخ دیتابیس معمولاً Y-m-d H:i:s است، ما فقط Y-m-d را برای اینپوت نیاز داریم
    if (tr.date) {
        form.date = tr.date.split(' ')[0].split('T')[0];
    }

    form.description = tr.description;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteTransaction = (id) => {
    if (confirm('با حذف این تراکنش، مبلغ آن از موجودی حساب کسر/اضافه خواهد شد. مطمئن هستید؟')) {
        router.delete(route('transactions.destroy', id));
    }
};

const formatMoney = (value) => {
    return new Intl.NumberFormat('fa-IR').format(value);
};
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-12 px-4 max-w-7xl mx-auto" dir="rtl">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="bg-white p-6 shadow-sm border rounded-lg h-fit">
                    <h3 class="text-lg font-bold mb-4">
                        {{ editMode ? 'ویرایش تراکنش' : 'ثبت تراکنش جدید' }}
                    </h3>
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label class="block text-sm mb-1">حساب:</label>
                            <select v-model="form.account_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="" disabled>انتخاب حساب...</option>
                                <option v-for="acc in accounts" :key="acc.id" :value="acc.id">
                                    {{ acc.name }} (موجودی: {{ formatMoney(acc.balance) }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">دسته‌بندی:</label>
                            <select v-model="form.category_id" class="w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="" disabled>انتخاب دسته...</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }} ({{ cat.type === 'income' ? 'درآمد' : 'هزینه' }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">مبلغ:</label>
                            <input v-model="form.amount" type="number" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm" required />
                        </div>

                        <div>
                            <label class="block text-sm mb-1">تاریخ:</label>
                            <div>
                                <label class="block text-sm mb-1">تاریخ:</label>
                                <date-picker
                                    v-model="form.date"
                                    format="YYYY-MM-DD"
                                    display-format="jYYYY/jMM/jDD"
                                    color="#4f46e5"
                                    editable
                                    class="w-full"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm mb-1">توضیحات:</label>
                            <textarea v-model="form.description" class="w-full border-gray-300 rounded-md shadow-sm" rows="2"></textarea>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button :disabled="form.processing"
                                    :class="editMode ? 'bg-orange-600 hover:bg-orange-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                                    class="w-full text-white py-2 rounded-md transition">
                                {{ editMode ? 'بروزرسانی تغییرات' : 'ذخیره تراکنش' }}
                            </button>

                            <button v-if="editMode" @click="editMode = false; form.reset()" type="button"
                                    class="w-full bg-gray-100 text-gray-700 py-2 rounded-md hover:bg-gray-200">
                                انصراف از ویرایش
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white p-6 shadow-sm border rounded-lg">
                    <h3 class="text-lg font-bold mb-4">تاریخچه تراکنش‌ها</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                            <tr class="bg-gray-50 text-sm text-gray-600 border-b">
                                <th class="p-3">تاریخ</th>
                                <th class="p-3">دسته</th>
                                <th class="p-3">حساب</th>
                                <th class="p-3">مبلغ</th>
                                <th class="p-3">عملیات</th>
                            </tr>
                            </thead>
                            <tbody class="text-sm">
                            <tr v-for="tr in transactions" :key="tr.id" class="border-b hover:bg-gray-50 transition">
                                <td class="p-3 text-gray-700">{{ tr.date_jalali }}</td>
                                <td class="p-3 font-medium">{{ tr.category.name }}</td>
                                <td class="p-3 text-gray-600">{{ tr.account.name }}</td>
                                <td class="p-3 font-bold" :class="tr.category.type === 'income' ? 'text-green-600' : 'text-red-600'">
                                    {{ tr.category.type === 'income' ? '+' : '-' }} {{ formatMoney(tr.amount) }}
                                </td>
                                <td class="p-3 flex gap-3">
                                    <button @click="editTransaction(tr)" class="text-blue-600 hover:text-blue-800">ویرایش</button>
                                    <button @click="deleteTransaction(tr.id)" class="text-red-500 hover:text-red-700">حذف</button>
                                </td>
                            </tr>
                            <tr v-if="transactions.length === 0">
                                <td colspan="5" class="p-10 text-center text-gray-400">هیچ تراکنشی یافت نشد.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
