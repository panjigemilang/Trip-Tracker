<script lang="ts">
  import { notificationsStore } from '$lib/stores/notifications.svelte';
  import { Compass, Send, AlertTriangle, Check, Bell } from 'lucide-svelte';
  import { fade, slide } from 'svelte/transition';

  let notifications = $derived(notificationsStore.notifications);
  let { hideHeader = false } = $props<{ hideHeader?: boolean }>();

  function getIcon(type: string) {
    if (type === 'upcoming') return Compass;
    if (type === 'active') return Send;
    return AlertTriangle;
  }

  function getBorderColor(type: string) {
    if (type === 'upcoming') return 'border-primary/50 shadow-[0_0_15px_rgba(255,42,122,0.15)]';
    if (type === 'active') return 'border-secondary/50 shadow-[0_0_15px_rgba(0,230,184,0.15)]';
    return 'border-yellow-500/50 shadow-[0_0_15px_rgba(234,179,8,0.15)]';
  }

  function getTitleColor(type: string) {
    if (type === 'upcoming') return 'text-primary';
    if (type === 'active') return 'text-primary'; // Mockup shows "TRIP TRACKER" as pink universally
    return 'text-primary'; 
  }
  
  function handleMarkAllRead() {
    notificationsStore.markAllAsRead();
  }
</script>

<div class="flex flex-col h-full bg-[#0B0C10]">
  <!-- Header -->
  {#if !hideHeader}
    <div class="flex items-center justify-between px-4 py-3 border-b border-border/50 sticky top-0 bg-[#0B0C10]/95 backdrop-blur z-10">
      <div class="flex items-center gap-2">
        <Bell class="h-4 w-4 text-white" />
        <h2 class="text-sm font-bold text-white uppercase tracking-widest">Notifications</h2>
      </div>
      {#if notificationsStore.unreadCount > 0}
        <button 
          onclick={handleMarkAllRead}
          class="text-[10px] text-muted-foreground hover:text-white uppercase tracking-widest font-bold flex items-center gap-1 transition-colors"
        >
          <Check class="h-3 w-3" /> Mark All Read
        </button>
      {/if}
    </div>
  {/if}

  <!-- List -->
  <div class="flex-1 overflow-y-auto p-4 space-y-3">
    {#if notifications.length === 0}
      <div class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground">
        <Bell class="h-8 w-8 mb-3 opacity-20" />
        <p class="text-xs uppercase tracking-widest">No notifications</p>
      </div>
    {:else}
      {#each notifications as notif (notif.id)}
        {@const Icon = getIcon(notif.type)}
        {@const titleParts = notif.extra?.periodTitle?.split(' ') || []}
        {#if notif.extra && notif.extra.isStats}
          <!-- STATS PROGRESS CARD (just like the design) -->
          <div 
            in:slide={{ duration: 200 }} 
            out:fade={{ duration: 150 }}
            class="relative p-5 rounded-2xl bg-linear-to-br from-[#12131C] to-[#0A0B10] border border-primary/20 shadow-[0_0_20px_rgba(255,42,122,0.05)] transition-all duration-300"
            class:opacity-50={notif.read}
          >
            <!-- Unread Dot -->
            {#if !notif.read}
              <button 
                onclick={() => notificationsStore.markAsRead(notif.id)}
                class="absolute top-4 right-4 h-2 w-2 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)] cursor-pointer"
                aria-label="Mark read"
              ></button>
            {/if}

            <div class="flex items-baseline gap-1 text-[10px] uppercase tracking-widest font-black text-muted-foreground mb-3">
              <span>{titleParts[0] || ''}</span>
              <span class="text-white text-lg ml-1">{titleParts.slice(1).join(' ')}</span>
            </div>

            <div class="flex items-end justify-between mb-2">
              <span class="text-lg font-bold text-primary tracking-wide text-glow">
                {notif.extra.completedCount}/{notif.extra.targetCount} Trips
              </span>
              <span class="text-xs font-bold text-secondary text-glow">
                ({notif.extra.percentage}%)
              </span>
            </div>

            <!-- Progress Bar -->
            <div class="h-2.5 w-full rounded-full bg-[#1A1D26] overflow-hidden border border-white/5">
              <div 
                class="h-full bg-linear-to-r from-primary to-[#FF6B9D] rounded-full shadow-[0_0_10px_rgba(255,42,122,0.5)] transition-all duration-500" 
                style="width: {notif.extra.percentage}%"
              ></div>
            </div>
            
            <a 
              href={notif.link || '#'} 
              class="block mt-4 text-[10px] font-bold text-muted-foreground hover:text-white uppercase tracking-widest transition-colors"
              onclick={() => notificationsStore.markAsRead(notif.id)}
            >
              View Milestone Details &rarr;
            </a>
          </div>

        {:else if notif.extra && notif.extra.isTrip}
          <!-- TRIP NOTIFICATION CARD (designed just like the mobile screenshot) -->
          <div 
            in:slide={{ duration: 200 }} 
            out:fade={{ duration: 150 }}
            class="relative p-5 rounded-2xl bg-linear-to-br from-[#1E1122] to-[#0D0B12] border border-primary/35 shadow-[0_0_20px_rgba(255,42,122,0.08)] transition-all duration-300"
            class:opacity-50={notif.read}
          >
            <!-- Unread Dot -->
            {#if !notif.read}
              <button 
                onclick={() => notificationsStore.markAsRead(notif.id)}
                class="absolute top-4 right-4 h-2 w-2 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)] cursor-pointer"
                aria-label="Mark read"
              ></button>
            {/if}

            <div class="mb-4">
              <h3 class="text-lg font-bold text-white tracking-wide mb-1 leading-snug">
                {notif.extra.tripName}
              </h3>
              <p class="text-xs text-muted-foreground">
                {notif.extra.segmentName}
              </p>
              {#if notif.extra.dateText}
                <p class="text-[10px] text-muted-foreground/60 uppercase tracking-wider mt-1.5">
                  {notif.extra.dateText}
                </p>
              {/if}
            </div>

            <a 
              href={notif.link || '#'} 
              class="inline-block w-full py-2.5 px-4 rounded-lg bg-transparent border border-primary/60 text-center text-xs font-black text-primary tracking-widest uppercase hover:bg-primary/10 transition-all duration-300 shadow-[0_0_10px_rgba(255,42,122,0.15)]"
              onclick={() => notificationsStore.markAsRead(notif.id)}
            >
              {notif.extra.buttonText}
            </a>
          </div>

        {:else}
          <!-- FALLBACK GENERAL CARD -->
          <div 
            in:slide={{ duration: 200 }} 
            out:fade={{ duration: 150 }}
            class="relative p-4 rounded-xl bg-[#11131A] border transition-all duration-300 {getBorderColor(notif.type)}"
            class:opacity-50={notif.read}
          >
            <!-- Unread Dot -->
            {#if !notif.read}
              <button 
                onclick={() => notificationsStore.markAsRead(notif.id)}
                class="absolute -top-1.5 -right-1.5 h-3 w-3 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)] cursor-pointer"
                aria-label="Mark read"
              ></button>
            {/if}

            <div class="flex items-start justify-between mb-2">
              <div class="flex items-center gap-2">
                <Icon class="h-3.5 w-3.5 {getTitleColor(notif.type)}" />
                <span class="text-[10px] font-black uppercase tracking-widest {getTitleColor(notif.type)}">
                  TRIP TRACKER
                </span>
              </div>
              <span class="text-[10px] text-muted-foreground">{notif.time}</span>
            </div>

            <a 
              href={notif.link || '#'} 
              class="block group cursor-pointer"
              onclick={() => notificationsStore.markAsRead(notif.id)}
            >
              <h3 class="text-[15px] font-bold text-white mb-1 group-hover:text-primary transition-colors">{notif.title}</h3>
              <p class="text-[13px] text-muted-foreground leading-relaxed">{notif.message}</p>
            </a>
          </div>
        {/if}
      {/each}
    {/if}
  </div>
</div>

<style>
  .text-glow {
    text-shadow: 0 0 8px currentColor;
  }
</style>
