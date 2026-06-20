<script lang="ts">
  import { pushService } from '$lib/services/pushService';
  import { onMount } from 'svelte';
  import { Bell, BellOff, Smartphone, LogOut } from 'lucide-svelte';
  import { Button } from '$lib/components/ui/button';
  import { toast } from 'svelte-sonner';
  import { authStore } from '$lib/stores/auth.svelte';

  async function handleLogout() {
    await authStore.logout();
    window.location.href = '/login';
  }

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

    <!-- Session Control (Logout) -->
    <section class="border-t border-border/50 pt-8 mt-8">
      <h2 class="text-xl font-bold mb-4 text-red-500 uppercase tracking-widest" style="text-shadow: 0 0 10px rgba(239,68,68,0.2);">Session Control</h2>
      
      <div class="bg-card border border-red-500/20 rounded-xl p-6 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <LogOut class="w-5 h-5 text-red-500" />
            <h3 class="font-medium text-lg text-white">Terminate Agent Session</h3>
          </div>
          <p class="text-slate-500 text-sm">Disconnect this terminal and flush active credentials cache.</p>
        </div>
        
        <Button 
          onclick={handleLogout}
          class="w-full md:w-auto bg-[#2A0818] border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold tracking-widest uppercase md:px-6"
        >
          Terminate Session
        </Button>
      </div>
    </section>
  </div>
</div>
