<script lang="ts">
  import { onMount } from "svelte"
  import { useRegisterSW } from "virtual:pwa-register/svelte"
  import { Button } from "$lib/components/ui/button"

  const { needRefresh, updateServiceWorker, offlineReady } = useRegisterSW({
    onRegistered(r: ServiceWorkerRegistration | undefined) {
      console.log(`SW Registered: ${r}`)
    },
    onRegisterError(error: unknown) {
      console.log("SW registration error", error)
    },
  })

  let isDismissed = $state(true)

  onMount(() => {
    const dismissedTime = localStorage.getItem("pwa_dismissed_time")
    if (dismissedTime) {
      const lastDismissed = parseInt(dismissedTime, 10)
      const oneDay = 24 * 60 * 60 * 1000 // 24 hours in ms
      if (Date.now() - lastDismissed < oneDay) {
        isDismissed = true
        return
      }
    }
    isDismissed = false
  })

  const close = () => {
    localStorage.setItem("pwa_dismissed_time", Date.now().toString())
    isDismissed = true
    offlineReady.set(false)
    needRefresh.set(false)
  }
</script>

{#if ($offlineReady || $needRefresh) && !isDismissed}
  <div
    class="fixed bottom-20 md:bottom-8 right-4 z-50 p-4 border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 rounded-xl shadow-lg max-w-sm animate-in slide-in-from-bottom-5"
  >
    <div class="mb-4 text-sm font-medium">
      {#if $offlineReady}
        <p>App ready to work offline</p>
      {:else}
        <p>New content available, click on reload button to update.</p>
      {/if}
    </div>
    <div class="flex items-center gap-2">
      {#if $needRefresh}
        <Button size="sm" onclick={() => updateServiceWorker(true)}
          >Reload</Button
        >
      {/if}
      <Button size="sm" variant="outline" onclick={close}>Close</Button>
    </div>
  </div>
{/if}
