<script lang="ts">
  import { X, ChevronLeft, ChevronRight } from 'lucide-svelte';
  import { fade } from 'svelte/transition';

  let { images = [], currentIndex = $bindable(0), isOpen = $bindable(false) } = $props();

  function close() {
    isOpen = false;
  }

  function next() {
    if (currentIndex < images.length - 1) currentIndex++;
    else currentIndex = 0;
  }

  function prev() {
    if (currentIndex > 0) currentIndex--;
    else currentIndex = images.length - 1;
  }

  function handleKeydown(e: KeyboardEvent) {
    if (!isOpen) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowRight') next();
    if (e.key === 'ArrowLeft') prev();
  }

  // Swipe handling
  let touchStartX = 0;
  let touchEndX = 0;

  function handleTouchStart(e: TouchEvent) {
    touchStartX = e.changedTouches[0].screenX;
  }

  function handleTouchEnd(e: TouchEvent) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  }

  function handleSwipe() {
    if (touchEndX < touchStartX - 50) next();
    if (touchEndX > touchStartX + 50) prev();
  }
</script>

<svelte:window onkeydown={handleKeydown} />

{#if isOpen && images.length > 0}
  <div 
    class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm"
    transition:fade={{ duration: 200 }}
  >
    <button class="absolute top-4 right-4 z-50 text-white/50 hover:text-white transition-colors" onclick={close}>
      <X class="h-8 w-8" />
    </button>

    {#if images.length > 1}
      <button class="absolute left-4 top-1/2 -translate-y-1/2 z-50 text-white/50 hover:text-white transition-colors" onclick={prev}>
        <ChevronLeft class="h-12 w-12" />
      </button>

      <button class="absolute right-4 top-1/2 -translate-y-1/2 z-50 text-white/50 hover:text-white transition-colors" onclick={next}>
        <ChevronRight class="h-12 w-12" />
      </button>

      <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-50 text-white/70 text-sm tracking-widest font-mono">
        {currentIndex + 1} / {images.length}
      </div>
    {/if}

    <div 
      class="relative max-w-5xl max-h-[80vh] w-full h-full p-4 flex items-center justify-center outline-none"
      ontouchstart={handleTouchStart}
      ontouchend={handleTouchEnd}
      role="button"
      tabindex="0"
    >
      <!-- svelte-ignore a11y_missing_attribute -->
      <img 
        src={images[currentIndex]} 
        alt="Gallery preview" 
        class="max-w-full max-h-full object-contain rounded-lg border border-primary/30 shadow-[0_0_30px_rgba(255,42,122,0.2)]"
        transition:fade={{ duration: 150 }}
      />
    </div>
  </div>
{/if}
