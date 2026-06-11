<script lang="ts">
  import { page } from '$app/stores';
  import { Map, Activity, Database, Settings, FileText } from 'lucide-svelte';

  const coreSystems = [
    { name: 'MY TRIPS', href: '/trips', icon: Map },
    { name: 'ACTIVE FEED', href: '/feed', icon: Activity },
    { name: 'RESOURCES', href: '/resources', icon: Database }
  ];

  const operational = [
    { name: 'CONFIG', href: '/settings', icon: Settings },
    { name: 'LOGS', href: '/logs', icon: FileText }
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

  <div class="mt-auto">
    <div class="rounded-lg border border-border bg-card p-4">
      <div class="mb-2 text-[10px] uppercase tracking-widest text-secondary">Network Status</div>
      <div class="flex items-center gap-2 text-xs text-muted-foreground">
        <span class="h-2 w-2 rounded-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.6)]"></span>
        TOKYO_NODE_04 ACTIVE
      </div>
    </div>
  </div>
</aside>
