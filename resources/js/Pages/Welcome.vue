
<template>
<div class="min-h-screen flex flex-col bg-gradient-to-br from-blue-700 via-blue-800 to-blue-900">

  <!-- Logo Section -->
  <div class="text-center pt-12 px-6">
    <img
      src="/images/logo1.png"
      alt="Logo"
      class="h-20 w-2à mx-auto mb-4 drop-shadow-xl"
    />
  

    <p class="text-blue-200 text-sm max-w-xs mx-auto leading-relaxed">
      منصة موثوقة وسريعة لشحن جميع ألعابك المفضلة بسهولة وأمان
    </p>
  </div>

  <!-- Buttons -->
  <div class="flex flex-col sm:flex-row justify-center items-center gap-4 mt-8 px-6">
    <Link
      href="/login"
      class="w-full sm:w-auto text-center px-8 py-3 bg-white text-blue-700 font-semibold rounded-xl shadow-lg hover:scale-105 hover:bg-gray-100 transition"
    >
      تسجيل الدخول
    </Link>

    <Link
      href="/register"
      class="w-full sm:w-auto text-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-xl shadow-lg hover:scale-105 hover:bg-blue-700 transition"
    >
      إنشاء حساب
    </Link>
  </div>

  <!-- Guide Section -->
  <div class="mt-12 md:mt-16 max-w-md sm:max-w-3xl mx-auto px-4">

    <h2 class="text-white text-xl font-semibold text-center mb-8">
      كيفية استخدام الموقع
    </h2>

    <div class="flex flex-col md:flex-row gap-4">

      <!-- Step 1 -->
      <div class="bg-white rounded-2xl p-5 text-center shadow-xl hover:shadow-2xl hover:-translate-y-1 transition w-full">
        <!-- Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.343 3-3S13.657 2 12 2 9 3.343 9 5s1.343 3 3 3zM12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z" />
        </svg>
        <h3 class="font-semibold text-lg mb-2">1. زود رصيدك</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          قم بتعبئة رصيد محفظتك عبر كروت المحفظة أو التطبيقات البنكية المتوفرة.
        </p>
      </div>

      <!-- Step 2 -->
      <div class="bg-white rounded-2xl p-5 text-center shadow-xl hover:shadow-2xl hover:-translate-y-1 transition w-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3V3z" />
        </svg>
        <h3 class="font-semibold text-lg mb-2">2. اختر العنصر</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          تصفح تشكيلتنا الواسعة من خدمات شحن الألعاب واختر ما يناسبك.
        </p>
      </div>

      <!-- Step 3 -->
      <div class="bg-white rounded-2xl p-5 text-center shadow-xl hover:shadow-2xl hover:-translate-y-1 transition w-full">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <h3 class="font-semibold text-lg mb-2">3. قم بالشراء</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          أتمم عملية الشراء بسهولة واستمتع بوصول الخدمة بسرعة.
        </p>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <footer class="mt-auto pt-12 pb-8 text-center text-blue-200 text-sm px-4">
    © 2025 جميع الحقوق محفوظة - 2Sh7anli
  </footer>

</div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>