<script lang="ts">
  import { pushService } from '$lib/services/pushService';
  import { onMount } from 'svelte';
  import { Bell, BellOff, Smartphone } from 'lucide-svelte';
  import { Button } from '$lib/components/ui/button';
  import { toast } from 'svelte-sonner';

  let isSubscribed = $state(false);
  let isLoading = $state(true);

  // You would fetch this from your backend normally
  const VAPID_PUBLIC_KEY = import.meta.env.VITE_VAPID_PUBLIC_KEY || 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuB-5MEKKeEXcndiqWXqq4wFz0';

  onMount(async () => {
    try {
      const sub = await pushService.getSubscription();
      isSubscribed = !!sub;
    } catch (e) {
      console.error(e);
    } finally {
      isLoading = false;
    }
  });

  async function toggleSubscription() {
    isLoading = true;
    try {
      if (isSubscribed) {
        await pushService.unsubscribeUser();
        isSubscribed = false;
        toast.success('Unsubscribed from notifications');
      } else {
        await pushService.subscribeUser(VAPID_PUBLIC_KEY);
        isSubscribed = true;
        toast.success('Subscribed to notifications!');
      }
    } catch (e: any) {
      toast.error('Failed to update subscription. Please ensure permissions are granted.');
    } finally {
      isLoading = false;
    }
  }
</script>

<svelte:head>
  <title>Settings | Plan Trip Tracker</title>
</svelte:head>

<div class="container max-w-2xl py-8">
  <h1 class="text-3xl font-bold mb-8">Settings</h1>

  <div class="space-y-8">
    <section>
      <h2 class="text-xl font-semibold mb-4 border-b pb-2">Notifications</h2>
      
      <div class="bg-white dark:bg-slate-900 border rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <Smartphone class="w-5 h-5 text-slate-500" />
            <h3 class="font-medium text-lg">Push Notifications</h3>
          </div>
          <p class="text-slate-500 text-sm">Receive alerts when activities are starting, or when a journey is completed.</p>
        </div>
        
        <Button 
          variant={isSubscribed ? 'destructive' : 'default'} 
          disabled={isLoading}
          onclick={toggleSubscription}
          class="w-full md:w-auto"
        >
          {#if isSubscribed}
            <BellOff class="w-4 h-4 mr-2" /> Disable
          {:else}
            <Bell class="w-4 h-4 mr-2" /> Enable
          {/if}
        </Button>
      </div>

      <!-- iOS fallback banner -->
      <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 text-blue-800 dark:text-blue-300 rounded-lg text-sm flex items-start gap-3">
        <div class="mt-0.5">ℹ️</div>
        <div>
          <strong>iOS Users:</strong> To enable push notifications on iOS, you must first add this web app to your Home Screen (Share &gt; Add to Home Screen), and launch it from there.
        </div>
      </div>
    </section>
  </div>
</div>
