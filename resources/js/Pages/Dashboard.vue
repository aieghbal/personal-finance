<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';
// اضافه کردن LinearScale و BarElement - حذف ArcElement
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

// ثبت المان‌های صحیح برای نمودار ستونی
ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

const props = defineProps({
    stats: Object,
    chartData: Array
});

// مابقی کدها (chartConfig و chartOptions) درست هستند
const chartConfig = computed(() => ({
    labels: props.chartData.map(item => item.label),
    datasets: [
        {
            label: 'مبلغ (ریال)',
            backgroundColor: props.chartData.map(item =>
                item.type === 'income' ? '#10B981' : '#FB7185'
            ),
            data: props.chartData.map(item => item.value),
            borderRadius: 5,
        }
    ]
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => new Intl.NumberFormat('fa-IR').format(value)
            }
        }
    }
};

const formatMoney = (value) => {
    return new Intl.NumberFormat('fa-IR').format(value);
};
</script>

<template>
    <Head title="داشبورد" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">خلاصه وضعیت مالی</h2>
        </template>

        <div class="py-12" dir="rtl">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-r-4 border-indigo-500">
                        <div class="text-gray-500 text-sm font-medium">مجموع موجودی حساب‌ها</div>
                        <div class="text-2xl font-bold mt-2 text-indigo-700">
                            {{ formatMoney(stats.total_balance) }} <span class="text-sm font-normal text-gray-400">ریال</span>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-r-4 border-green-500">
                        <div class="text-gray-500 text-sm font-medium">درآمد این ماه</div>
                        <div class="text-2xl font-bold mt-2 text-green-600">
                            {{ formatMoney(stats.monthly_income) }} <span class="text-sm font-normal">+</span>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-r-4 border-red-500">
                        <div class="text-gray-500 text-sm font-medium">هزینه‌های این ماه</div>
                        <div class="text-2xl font-bold mt-2 text-red-600">
                            {{ formatMoney(stats.monthly_expense) }} <span class="text-sm font-normal">-</span>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 shadow-sm rounded-lg border border-gray-100">
                    <h3 class="text-lg font-bold mb-6 text-gray-700">توزیع درآمدها و هزینه‌ها (ماه جاری)</h3>

                    <div v-if="chartData.length > 0" class="flex flex-col md:flex-row items-center justify-around">
                        <div class="w-full max-w-md h-80">
                            <Bar :data="chartConfig" :options="chartOptions" />
                        </div>

                        <div class="mt-6 md:mt-0 space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 bg-green-500 rounded-full"></span>
                                <span class="text-sm text-gray-600">رنگ سبز: دسته‌بندی‌های درآمدی</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-4 h-4 bg-rose-400 rounded-full"></span>
                                <span class="text-sm text-gray-600">رنگ قرمز: دسته‌بندی‌های هزینه‌ای</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="py-20 text-center text-gray-400">
                        داده‌ای برای نمایش نمودار در این ماه وجود ندارد.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
