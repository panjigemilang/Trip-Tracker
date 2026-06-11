<script lang="ts">
  import { page } from '$app/stores';
  import { Bell } from 'lucide-svelte';

  const navItems = [
    { name: 'TRIPS', href: '/trips' },
    { name: 'TRACK', href: '/track' },
    { name: 'IMPORT', href: '/import' },
    { name: 'HISTORY', href: '/history' }
  ];
</script>

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
  <div class="flex items-center gap-4">
    <button class="text-muted-foreground hover:text-primary">
      <Bell class="h-5 w-5" />
    </button>
    <div class="h-8 w-8 overflow-hidden rounded-full border border-border bg-muted">
      <img src="https://i.pravatar.cc/150?u=a042581f4e29026704d" alt="Avatar" class="h-full w-full object-cover" />
    </div>
  </div>
</header>
