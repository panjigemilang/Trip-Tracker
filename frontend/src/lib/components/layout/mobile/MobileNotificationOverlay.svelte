<script lang="ts">
  import { X } from 'lucide-svelte';
  import { fade, fly } from 'svelte/transition';
  import { cubicOut } from 'svelte/easing';
  import NotificationList from '$lib/components/shared/NotificationList.svelte';
  import { notificationsStore } from '$lib/stores/notifications.svelte';

  $effect(() => {
    if (notificationsStore.isMobileOverlayOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = '';
    }
    return () => {
      document.body.style.overflow = '';
    };
  });
</script>

{#if notificationsStore.isMobileOverlayOpen}
  <!-- Overlay Backdrop -->
  <!-- svelte-ignore a11y_click_events_have_key_events -->
  <!-- svelte-ignore a11y_no_static_element_interactions -->
  <div 
    class="fixed inset-0 z-[100] bg-background/80 backdrop-blur-sm md:hidden"
    transition:fade={{ duration: 200 }}
    onclick={() => { notificationsStore.isMobileOverlayOpen = false; }}
  ></div>

  <!-- Full Screen Modal Content -->
  <div 
    class="fixed inset-0 z-[101] bg-[#0B0C10] flex flex-col md:hidden"
    transition:fly={{ y: '100%', duration: 300, easing: cubicOut }}
  >
    <!-- Custom Header for Mobile Overlay -->
    <div class="flex items-center justify-between p-4 border-b border-border/50 bg-[#0B0C10]">
      <h2 class="text-lg font-bold text-white tracking-widest uppercase">Notifications</h2>
      <button 
        class="p-2 rounded-full bg-black/20 text-muted-foreground hover:text-white transition-colors"
        onclick={() => { notificationsStore.isMobileOverlayOpen = false; }}
      >
        <X class="h-5 w-5" />
      </button>
    </div>

    <!-- Reusing NotificationList but removing its built-in header via CSS or just letting it render -->
    <!-- Actually, NotificationList has its own header. I'll modify NotificationList to accept a hideHeader prop, or just hide the custom header here and rely on NotificationList's header. Let's just render it! -->
    <div class="flex-1 overflow-hidden relative">
      <NotificationList hideHeader={true} />
    </div>
  </div>
{/if}
