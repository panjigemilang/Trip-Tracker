<script lang="ts">
  import { page } from '$app/stores';
  import { Map, Navigation, Database, Settings, FileText } from 'lucide-svelte';

  const coreSystems = [
    { name: 'MY TRIPS', href: '/trips', icon: Map },
    { name: 'TRACKING', href: '/track', icon: Navigation },
    { name: 'HISTORY', href: '/history', icon: FileText },
    { name: 'IMPORT', href: '/import', icon: Database }
  ];

  const operational = [
    { name: 'CONFIG', href: '/settings', icon: Settings }
  ];
</script>

<aside class="hidden md:flex flex-col w-64 h-[calc(100vh-4rem)] fixed left-0 top-16 border-r border-border bg-background p-4 overflow-y-auto">
  
  <div class="mb-8">
    <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted-foreground">Core Systems</h3>
    <ul class="space-y-2">
      {#each coreSystems as item}
        {@const isActive = $page.url.pathname.startsWith(item.href)}
        <li>
          <a href={item.href} class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-all {isActive ? 'bg-primary/10 text-primary border border-primary/30' : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground'}">
            <svelte:component this={item.icon} class="h-4 w-4" />
            {item.name}
          </a>
        </li>
      {/each}
    </ul>
  </div>

  <div class="mb-8">
    <h3 class="mb-4 text-xs font-semibold uppercase tracking-widest text-muted-foreground">Operational</h3>
    <ul class="space-y-2">
      {#each operational as item}
        {@const isActive = $page.url.pathname.startsWith(item.href)}
        <li>
          <a href={item.href} class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-all {isActive ? 'bg-primary/10 text-primary border border-primary/30' : 'text-muted-foreground hover:bg-muted/50 hover:text-foreground'}">
            <svelte:component this={item.icon} class="h-4 w-4" />
            {item.name}
          </a>
        </li>
      {/each}
    </ul>
  </div>
</aside>
