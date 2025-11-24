<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-blue-50 to-white">
    <!-- الشعار -->
    <div class="text-center mb-8">
      <img src="/images/logo.png" alt="Logo" class="w-24 h-24 mx-auto rounded-full shadow-md" />
      <h1 class="text-3xl font-bold text-blue-600 mt-4">مرحبا بك في موقع شحن الألعاب</h1>
      <p class="text-gray-600 mt-2 text-sm max-w-md mx-auto">
        منصة موثوقة وسريعة لشحن جميع ألعابك المفضلة — ببساطة وأمان.
      </p>
    </div>

    <!-- الأزرار -->
    <div class="flex space-x-4 rtl:space-x-reverse">
      <Link
        href="/login"
        class="px-6 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
      >
        تسجيل الدخول
      </Link>
      <Link
        href="/register"
        class="px-6 py-2 bg-white text-blue-600 border border-blue-600 rounded-lg shadow hover:bg-blue-50 transition"
      >
        إنشاء حساب
      </Link>
    </div>

    <!-- دليل المستخدم -->
    <div class="mt-16 w-full max-w-3xl px-6">
      <h2 class="text-xl font-semibold text-blue-700 text-center mb-4">كيفية استخدام الموقع</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition text-center">
          <div class="text-blue-600 text-3xl mb-2">🎮</div>
          <h3 class="font-semibold">1. اختر اللعبة</h3>
          <p class="text-gray-500 text-sm mt-1">تصفح الألعاب واختر اللعبة التي تريد شحنها.</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition text-center">
          <div class="text-blue-600 text-3xl mb-2">💰</div>
          <h3 class="font-semibold">2. اشحن رصيدك</h3>
          <p class="text-gray-500 text-sm mt-1">يمكنك تعبئة المحفظة من لوحة التحكم بسهولة.</p>
        </div>
        <div class="bg-white p-4 rounded-xl shadow hover:shadow-md transition text-center">
          <div class="text-blue-600 text-3xl mb-2">⚡</div>
          <h3 class="font-semibold">3. اشحن الآن</h3>
          <p class="text-gray-500 text-sm mt-1">أنشئ طلب شحن وسيتم تنفيذه فورًا بعد الموافقة.</p>
        </div>
      </div>
    </div>

    <!-- الفوتر -->
    <footer class="mt-16 text-gray-400 text-sm text-center pb-6">
      © 2025 جميع الحقوق محفوظة - 2Sh7anli
    </footer>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>