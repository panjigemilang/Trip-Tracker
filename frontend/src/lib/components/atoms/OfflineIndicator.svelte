<script lang="ts">
  import { onMount, onDestroy } from 'svelte';
  import { WifiOff } from 'lucide-svelte';

  let isOffline = $state(false);

  function handleOffline() {
    isOffline = true;
  }

  function handleOnline() {
    isOffline = false;
  }

  onMount(() => {
    isOffline = !navigator.onLine;
    window.addEventListener('offline', handleOffline);
    window.addEventListener('online', handleOnline);
  });

  onDestroy(() => {
    if (typeof window !== 'undefined') {
      window.removeEventListener('offline', handleOffline);
      window.removeEventListener('online', handleOnline);
    }
  });
</script>

{#if isOffline}
  <div class="fixed bottom-4 right-4 z-50 bg-slate-900 text-white dark:bg-slate-100 dark:text-slate-900 px-4 py-3 rounded-lg shadow-lg flex items-center gap-3 animate-in slide-in-from-bottom-5">
    <WifiOff class="w-5 h-5 text-amber-500" />
    <div>
      <p class="font-medium text-sm">You are offline</p>
      <p class="text-xs opacity-80">Changes will be saved locally.</p>
    </div>
  </div>
{/if}
