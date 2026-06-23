<script lang="ts">
  import { page } from '$app/stores';
  import { Bell, LogOut } from 'lucide-svelte';
  import { authStore } from '$lib/stores/auth.svelte';
  import { notificationsStore } from '$lib/stores/notifications.svelte';
  import NotificationList from '$lib/components/shared/NotificationList.svelte';
  const navItems = [
    { name: 'TRIPS', href: '/trips' },
    { name: 'TRACK', href: '/track' },
    { name: 'IMPORT', href: '/import' },
    { name: 'HISTORY', href: '/history' }
  ];

  let isDropdownOpen = $state(false);
  let dropdownEl = $state<HTMLDivElement | null>(null);

  let isNotifOpen = $state(false);
  let notifDropdownEl = $state<HTMLDivElement | null>(null);

  function toggleDropdown() {
    isDropdownOpen = !isDropdownOpen;
    if (isDropdownOpen) isNotifOpen = false;
  }

  function toggleNotifDropdown() {
    isNotifOpen = !isNotifOpen;
    if (isNotifOpen) isDropdownOpen = false;
  }

  function handleOutsideClick(e: MouseEvent) {
    if (isDropdownOpen && dropdownEl && !dropdownEl.contains(e.target as Node)) {
      isDropdownOpen = false;
    }
    if (isNotifOpen && notifDropdownEl && !notifDropdownEl.contains(e.target as Node)) {
      isNotifOpen = false;
    }
  }

  async function handleLogout() {
    isDropdownOpen = false;
    await authStore.logout();
    window.location.href = '/login';
  }
</script>

<svelte:window onclick={handleOutsideClick} />

<header class="hidden md:flex sticky top-0 z-50 h-16 w-full items-center justify-between border-b border-border bg-background/80 px-6 backdrop-blur-md">
  <!-- Logo -->
  <a href="/" class="flex items-center gap-2">
    <div class="flex h-8 w-8 items-center justify-center rounded-sm bg-primary/20 text-primary">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-navigation"><polygon points="3 11 22 2 13 21 11 13 3 11"/></svg>
    </div>
    <span class="font-heading text-xl font-bold tracking-wider text-primary" style="text-shadow: 0 0 8px rgba(255, 42, 122, 0.4);">NEON_TRACKER</span>
  </a>

  <!-- Nav -->
  <nav class="flex items-center gap-8">
    {#each navItems as item}
      <a 
        href={item.href} 
        class="text-sm font-semibold tracking-wider text-muted-foreground transition-colors hover:text-primary"
        class:text-primary={$page.url.pathname.startsWith(item.href)}
      >
        {item.name}
      </a>
    {/each}
  </nav>

  <!-- Profile & Notifs -->
  <div class="flex items-center gap-4 relative">
    <div bind:this={notifDropdownEl}>
      <button 
        class="text-muted-foreground hover:text-primary cursor-pointer relative"
        onclick={toggleNotifDropdown}
      >
        <Bell class="h-5 w-5" />
        {#if notificationsStore.unreadCount > 0}
          <div class="absolute -top-1 -right-1 h-2 w-2 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)]"></div>
        {/if}
      </button>

      {#if isNotifOpen}
        <div class="absolute right-12 top-12 z-50 w-80 h-96 p-0 rounded-lg border border-secondary/40 bg-[#0B0C10] shadow-[0_0_20px_rgba(0,230,184,0.15)] animate-in fade-in slide-in-from-top-2 duration-100 overflow-hidden flex flex-col">
          <NotificationList />
        </div>
      {/if}
    </div>

    <div bind:this={dropdownEl} class="relative">
      <button 
      type="button" 
      onclick={toggleDropdown}
      class="h-8 w-8 overflow-hidden rounded-full border border-border bg-muted cursor-pointer hover:border-primary focus:outline-none transition-colors"
    >
      <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Avatar" class="h-full w-full object-cover" />
    </button>

    {#if isDropdownOpen}
      <div 
        class="absolute right-0 top-12 z-50 w-56 p-2 rounded-lg border border-primary/40 bg-[#0B0C10] shadow-[0_0_15px_rgba(255,42,122,0.25)] animate-in fade-in slide-in-from-top-2 duration-100"
      >
        <div class="px-3 py-2 border-b border-border mb-1">
          <div class="text-xs font-bold text-white truncate">{authStore.user?.name || 'AGENT_GUEST'}</div>
          <div class="text-[9px] text-muted-foreground truncate tracking-wider mt-0.5">{authStore.user?.email || ''}</div>
          {#if authStore.user?.email && authStore.user.email.startsWith('guest_')}
            <span class="inline-block mt-1 text-[8px] font-bold text-secondary tracking-widest bg-secondary/10 px-1 rounded uppercase">Guest_Session</span>
          {/if}
        </div>
        <button
          type="button"
          onclick={handleLogout}
          class="flex items-center gap-2 w-full px-3 py-2 rounded text-left text-xs font-bold text-red-500 hover:bg-red-500/10 transition-colors cursor-pointer"
        >
          <LogOut class="h-3.5 w-3.5" />
          TERMINATE_SESSION
        </button>
      </div>
    {/if}
    </div>
  </div>
</header>
