
<script setup>
defineProps({
    message: String
})
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-600 via-blue-700 to-blue-900 flex items-center justify-center relative overflow-hidden">

        <!-- Background Glow Circles -->
        <div class="absolute w-96 h-96 bg-blue-400 opacity-20 rounded-full blur-3xl top-10 left-10 animate-pulse"></div>
        <div class="absolute w-96 h-96 bg-indigo-400 opacity-20 rounded-full blur-3xl bottom-10 right-10 animate-pulse"></div>

        <div class="relative z-10 bg-white/10 backdrop-blur-xl border border-white/20 shadow-2xl rounded-3xl p-10 w-full max-w-lg text-center">

            <!-- Logo Area -->
    <div class="mb-8 text-center animate-fade-down">
        <!-- ضع صورة الشعار هنا -->
        <img src="/images/logo1.png" class="h-20 mx-auto mb-5" />
        <p class="text-blue-100 text-sm">
        </p>
    </div>

            <!-- Title -->
            <h1 class="text-3xl font-extrabold text-white mb-4 tracking-wide">
                الموقع تحت الصيانة
            </h1>

            <!-- Message -->
            <p class="text-blue-100 text-lg mb-6 leading-relaxed">
                {{ message }}
            </p>

            <!-- Divider -->
            <div class="w-20 h-1 bg-white/40 mx-auto rounded-full mb-6"></div>

            <!-- Info Text -->
            <p class="text-blue-200 text-sm">
                نعمل حالياً على تحسين تجربة الشحن وتطوير النظام 💙  
                سنعود قريباً بشكل أفضل وأسرع.
            </p>

        </div>

        <!-- Footer -->
        <div class="absolute bottom-5 text-blue-200 text-xs tracking-widest">
            
        </div>

    </div>
</template>