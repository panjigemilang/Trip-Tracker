<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import { Button } from '$lib/components/ui/button';
  import { Search, Grid, Zap, Activity, Award, Trash2 } from 'lucide-svelte';
</script>

<div class="flex flex-col gap-6 md:gap-8 pb-10 max-w-6xl mx-auto">
  <!-- Mobile Header -->
  <header class="md:hidden pt-4 pb-2 border-b border-border/50">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-widest text-white">History Log</h1>
        <p class="text-sm text-muted-foreground">Past and expired journeys.</p>
      </div>
      <button class="flex items-center gap-1 text-xs text-primary uppercase tracking-widest font-semibold bg-primary/10 px-3 py-1.5 rounded-md border border-primary/30">
        <Trash2 class="h-3 w-3" /> Clear All
      </button>
    </div>
  </header>

  <!-- Desktop Header & Stats -->
  <div class="hidden md:flex flex-col gap-8">
    <div class="flex items-center justify-between border-b border-border/50 pb-6">
      <div class="flex items-center gap-4">
        <div class="w-1 h-6 bg-primary"></div>
        <div>
          <h2 class="text-sm font-bold tracking-widest text-primary mb-1 uppercase">Records Division</h2>
          <h1 class="text-4xl font-bold tracking-widest text-white">Journey History</h1>
        </div>
      </div>
      <div class="flex gap-2">
        <Button class="bg-card border border-primary text-primary hover:bg-primary/20">ALL MISSIONS</Button>
        <Button variant="outline" class="border-border text-muted-foreground hover:text-white">COMPLETED</Button>
        <Button variant="outline" class="border-border text-muted-foreground hover:text-white">FAILED</Button>
      </div>
    </div>
    
    <p class="text-muted-foreground max-w-2xl">Reviewing logged expeditions across the Neo-Kyoto sectors. Archives are synced with the central grid.</p>

    <!-- Desktop Stats -->
    <div class="grid grid-cols-4 gap-4">
      <CyberCard class="p-5 flex items-center justify-between">
        <div>
          <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Total Distance</div>
          <div class="text-3xl font-bold">1,402 <span class="text-sm text-muted-foreground font-normal">KM</span></div>
        </div>
        <Grid class="h-8 w-8 text-muted-foreground/30" />
      </CyberCard>
      
      <CyberCard class="p-5 flex items-center justify-between">
        <div>
          <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Avg Efficiency</div>
          <NeonText color="secondary" class="text-3xl font-bold">94.2%</NeonText>
        </div>
        <Activity class="h-8 w-8 text-secondary/30" />
      </CyberCard>
      
      <CyberCard class="p-5 flex items-center justify-between">
        <div>
          <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Energy Used</div>
          <div class="text-3xl font-bold text-yellow-500">840 <span class="text-sm text-muted-foreground font-normal">KW</span></div>
        </div>
        <Zap class="h-8 w-8 text-yellow-500/30" />
      </CyberCard>
      
      <CyberCard class="p-5 flex items-center justify-between">
        <div>
          <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Rank</div>
          <NeonText class="text-3xl font-bold">ELITE VII</NeonText>
        </div>
        <Award class="h-8 w-8 text-primary/30" />
      </CyberCard>
    </div>
  </div>

  <!-- Mobile List -->
  <div class="md:hidden flex flex-col gap-6">
    <CyberCard class="p-5 border-secondary/30 bg-linear-to-b from-secondary/5 to-transparent relative overflow-hidden">
      <div class="absolute top-0 right-0 p-4">
        <StatusBadge status="completed" />
      </div>
      <h3 class="text-xl font-bold tracking-wider text-white mb-1">Neo-Kyoto Expedition</h3>
      <p class="text-xs text-secondary font-medium tracking-wide mb-6">Oct 12 - Oct 15, 2023</p>
      
      <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-background rounded-lg p-3 flex flex-col items-center justify-center border border-border">
          <div class="h-6 w-6 rounded-full bg-secondary/20 flex items-center justify-center mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="var(--color-neon-cyan)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          </div>
          <div class="text-xl font-bold">12</div>
          <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Completed</div>
        </div>
        <div class="bg-background rounded-lg p-3 flex flex-col items-center justify-center border border-border">
          <div class="h-6 w-6 rounded-full bg-muted flex items-center justify-center mb-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted-foreground"><polyline points="9 18 15 12 9 6"/></svg>
          </div>
          <div class="text-xl font-bold">2</div>
          <div class="text-[8px] text-muted-foreground uppercase tracking-widest">Skipped</div>
        </div>
      </div>
      
      <div class="flex items-center justify-between border-t border-border/50 pt-4">
        <button class="flex items-center gap-1 text-xs text-muted-foreground hover:text-white transition-colors">
          <Trash2 class="h-3 w-3" /> Delete
        </button>
        <button class="text-xs text-secondary font-bold tracking-widest uppercase flex items-center gap-1">
          View Details <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </button>
      </div>
    </CyberCard>

    <div class="text-center mt-4">
      <span class="text-[10px] text-muted-foreground uppercase tracking-widest">End of History Log</span>
    </div>
  </div>

  <!-- Desktop List -->
  <div class="hidden md:flex flex-col gap-4">
    <!-- List Item 1 -->
    <CyberCard class="p-6 flex flex-row items-center gap-6 group hover:border-secondary/50">
      <div class="h-28 w-48 rounded-lg overflow-hidden shrink-0 relative border border-border">
        <div class="absolute inset-0 bg-secondary/10 group-hover:bg-transparent transition-colors z-10"></div>
        <img src="https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=400" alt="Expedition" class="h-full w-full object-cover grayscale opacity-70 group-hover:grayscale-0 transition-all" />
      </div>
      
      <div class="flex-1">
        <div class="flex items-center justify-between mb-2">
          <div class="text-[10px] text-muted-foreground uppercase tracking-widest">ID: NT-0982 / Expedition</div>
          <StatusBadge status="completed" />
        </div>
        
        <h3 class="text-2xl font-bold tracking-wider text-white mb-4">Neo-Kyoto Expedition</h3>
        
        <div class="flex items-center gap-12">
          <div>
            <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Timeline</div>
            <div class="text-sm font-medium">14 OCT // 23:15</div>
          </div>
          <div>
            <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Distance</div>
            <div class="text-sm font-medium">42.5 KM</div>
          </div>
          <div>
            <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Checkpoints</div>
            <div class="text-sm font-medium">12 / 12</div>
          </div>
          <div class="flex-1">
            <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">Status</div>
            <div class="h-1.5 w-full bg-muted rounded-full overflow-hidden">
              <div class="h-full bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.8)]" style="width: 100%"></div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="pl-6 border-l border-border flex flex-col justify-center">
        <Button variant="outline" class="w-full whitespace-nowrap border-primary/30 text-primary hover:bg-primary/10 hover:text-primary tracking-widest uppercase font-semibold text-xs">View Details</Button>
      </div>
    </CyberCard>

    <div class="mt-4 flex justify-center">
      <Button variant="ghost" class="text-muted-foreground hover:text-white tracking-widest uppercase text-xs">
        Access Older Records <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2"><path d="m6 9 6 6 6-6"/></svg>
      </Button>
    </div>
  </div>
</div>
