<template>
  <div class="pt-24 pb-16 min-h-screen bg-cream-50 flex items-center justify-center">
    <div class="text-center px-6">
      <h1 class="text-8xl font-heading font-black text-jade-500/20 mb-4">{{ $t('notFound.code') }}</h1>
      <h2 class="text-2xl font-heading font-bold text-charcoal-800 mb-3">{{ $t('notFound.title') }}</h2>
      <p class="text-charcoal-500 mb-8 max-w-md mx-auto">
        {{ $t('notFound.desc') }}
      </p>
      <router-link
        to="/"
        class="inline-flex items-center gap-2 px-6 py-3 bg-jade-gradient text-white font-semibold rounded-full hover:shadow-jade-glow transition-all duration-300"
      >
        <span>&larr;</span>
        {{ $t('notFound.backHome') }}
      </router-link>
    </div>
  </div>
</template>
