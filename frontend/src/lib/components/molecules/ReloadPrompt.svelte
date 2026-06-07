<script lang="ts">
  import { useRegisterSW } from 'virtual:pwa-register/svelte';
  import { Button } from '$lib/components/ui/button';

  const { needRefresh, updateServiceWorker, offlineReady } = useRegisterSW({
    onRegistered(r) {
      console.log(`SW Registered: ${r}`);
    },
    onRegisterError(error) {
      console.log('SW registration error', error);
    }
  });

  const close = () => {
    offlineReady.set(false);
    needRefresh.set(false);
  };
</script>

{#if $offlineReady || $needRefresh}
  <div class="fixed bottom-4 right-4 z-50 p-4 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl shadow-lg max-w-sm animate-in slide-in-from-bottom-5">
    <div class="mb-4 text-sm font-medium">
      {#if $offlineReady}
        <p>App ready to work offline</p>
      {:else}
        <p>New content available, click on reload button to update.</p>
      {/if}
    </div>
    <div class="flex items-center gap-2">
      {#if $needRefresh}
        <Button size="sm" onclick={() => updateServiceWorker(true)}>Reload</Button>
      {/if}
      <Button size="sm" variant="outline" onclick={close}>Close</Button>
    </div>
  </div>
{/if}
