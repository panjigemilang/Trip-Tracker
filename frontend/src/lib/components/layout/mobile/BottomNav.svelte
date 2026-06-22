<script lang="ts">
  import { page } from '$app/stores';
  import { Map, Navigation, UploadCloud, History, Settings } from 'lucide-svelte';

  const navItems = [
    { name: 'TRIPS', href: '/trips', icon: Map },
    { name: 'TRACK', href: '/track', icon: Navigation },
    { name: 'IMPORT', href: '/import', icon: UploadCloud },
    { name: 'HISTORY', href: '/history', icon: History }
  ];
</script>

<nav class="md:hidden fixed bottom-0 left-0 z-50 flex h-16 w-full items-center justify-around border-t border-border bg-background pb-safe">
  {#each navItems as item}
    {@const isActive = $page.url.pathname.startsWith(item.href)}
    <a href={item.href} class="relative flex flex-col items-center justify-center gap-1 w-full h-full">
      {#if isActive}
        <!-- Active indicator glow -->
        <div class="absolute top-0 w-8 h-0.5 bg-primary shadow-[0_0_10px_rgba(255,42,122,1)]"></div>
      {/if}
      <svelte:component this={item.icon} class="h-5 w-5 {isActive ? 'text-primary' : 'text-muted-foreground'}" />
      <span class="text-[10px] font-semibold uppercase tracking-wider {isActive ? 'text-primary' : 'text-muted-foreground'}">{item.name}</span>
    </a>
  {/each}
</nav>
