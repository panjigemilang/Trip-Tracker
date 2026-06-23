<script lang="ts">
  import { Plus, Check, Loader, Clock } from 'lucide-svelte';
  import { Button } from '$lib/components/ui/button';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import { formatDate, formatTime } from '$lib/utils/dateFormatter';

  let { trip, isFormOpen = $bindable() } = $props<{
    trip: any,
    isFormOpen: boolean
  }>();
</script>

<!-- Mobile Header -->
<div class="md:hidden flex items-center gap-4 mt-2 mb-2">
  <div class="w-8 h-px bg-primary/60"></div>
  <h2 class="text-[11px] font-bold tracking-[0.2em] uppercase text-white">Sequence Timeline</h2>
</div>

{#if !trip.activities || trip.activities.length === 0}
  <div class="md:hidden text-center py-16 bg-slate-950/10 border border-dashed border-border rounded-xl">
    <p class="text-sm text-muted-foreground mb-4">No active checkpoints scheduled for this route.</p>
    <Button onclick={() => { isFormOpen = true; }} variant="outline" class="border-secondary text-secondary hover:bg-secondary/10">
      Initialize First Segment
    </Button>
  </div>
{:else}
  <!-- Mobile Timeline -->
  <div class="md:hidden relative space-y-6">
    <div class="absolute left-5 top-4 bottom-4 w-px bg-[#1F2937] z-0"></div>
    {#each trip.activities as act, i}
      {@const journeyAct = trip.journey?.activities?.find((a: any) => a.id === act.id)}
      {@const status = journeyAct?.status || 'upcoming'}
      {@const isCompleted = status === 'completed'}
      {@const isActive = status === 'current'}
      
      <div class="flex gap-4 relative z-10">
        <div class="flex flex-col items-center shrink-0 w-10">
          {#if isCompleted}
            <div class="h-10 w-10 rounded-xl border-[1.5px] border-primary bg-[#0B0C10] flex items-center justify-center relative mt-1">
              <Check class="h-4 w-4" stroke="var(--color-neon-pink)" strokeWidth={3} />
            </div>
            {#if i !== trip.activities.length - 1}
              <div class="absolute top-11 left-1/2 -translate-x-1/2 -bottom-6 w-px bg-primary shadow-[0_0_8px_rgba(255,42,122,0.6)] z-[-1]"></div>
            {/if}
          {:else if isActive}
            <div class="h-10 w-10 rounded-xl border-[1.5px] border-secondary bg-[#0B0C10] flex items-center justify-center relative mt-1 shadow-[0_0_12px_rgba(0,230,184,0.4)]">
              <Loader class="h-4 w-4" stroke="var(--color-neon-cyan)" strokeWidth={2} />
            </div>
            {#if i !== trip.activities.length - 1}
              <div class="absolute top-11 left-1/2 -translate-x-1/2 -bottom-6 w-px bg-secondary shadow-[0_0_8px_rgba(0,230,184,0.6)] z-[-1]"></div>
            {/if}
          {:else}
            <div class="h-10 w-10 rounded-full border border-muted-foreground/30 bg-[#16171E] flex items-center justify-center text-muted-foreground/40 relative mt-1">
              <Clock class="h-4 w-4" />
            </div>
          {/if}
        </div>

        <div class="flex-1 min-w-0">
          {#if isCompleted}
            <div class="flex items-center justify-between mb-1.5 mt-0.5">
              <span class="text-[12px] font-mono text-muted-foreground/80"><span class="text-white">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
              <span class="text-[8px] border border-primary/30 text-primary px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold bg-primary/10">COMPLETED</span>
            </div>
            <h3 class="text-[16px] font-bold text-white mb-1.5">{act.title}</h3>
            {#if act.notes || act.location}
              <p class="text-[12px] text-muted-foreground leading-relaxed italic">{act.notes || `Activity at ${act.location}`}</p>
            {/if}
          {:else if isActive}
            <CyberCard class="p-4 border-secondary/50 bg-[#11131A] relative shadow-[0_0_15px_rgba(0,230,184,0.1)] rounded-xl">
              <div class="flex items-center justify-between mb-3">
                <span class="text-[12px] font-mono text-secondary font-bold"><span class="text-white">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
                <span class="text-[9px] border border-secondary text-secondary px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold">ACTIVE</span>
              </div>
              <h3 class="text-[17px] font-bold text-white mb-2">{act.title}</h3>
              {#if act.notes || act.location}
                <p class="text-[12px] text-muted-foreground leading-relaxed">{act.notes || `Infiltrating data node at ${act.location}.`}</p>
              {/if}
            </CyberCard>
          {:else}
            <div class="flex items-center justify-between mb-1.5 mt-0.5">
              <span class="text-[12px] font-mono text-muted-foreground/50"><span class="text-white/70">{formatDate(act.date)}</span> • {formatTime(act.time)}</span>
              <span class="text-[8px] border border-muted-foreground/20 text-muted-foreground/60 px-2 py-0.5 rounded-sm uppercase tracking-widest font-bold bg-muted-foreground/5">UPCOMING</span>
            </div>
            <h3 class="text-[16px] font-bold text-white/70 mb-1.5">{act.title}</h3>
            {#if act.notes || act.location}
              <p class="text-[12px] text-muted-foreground/60 leading-relaxed line-clamp-2">{act.notes || `Location: ${act.location}`}</p>
            {/if}
          {/if}
        </div>
      </div>
    {/each}
    
    <!-- Add new segment button at the bottom for mobile -->
    <div class="flex gap-4 mt-6">
      <div class="w-10 shrink-0 flex justify-center">
        <div class="h-10 w-10 rounded-full border border-dashed border-muted-foreground/40 bg-transparent flex items-center justify-center text-muted-foreground/50">
          <Plus class="h-4 w-4" />
        </div>
      </div>
      <div class="flex-1 min-w-0 flex items-center">
        <Button
          variant="outline"
          size="lg"
          onclick={() => { isFormOpen = true; }}
          class="w-full text-left p-4 border border-dashed border-muted-foreground/30 rounded-xl hover:bg-white/5 transition-colors group flex justify-between items-center"
        >
          <span class="text-xs font-bold uppercase tracking-widest text-muted-foreground group-hover:text-white transition-colors">Add New Segment</span>
          <Plus class="h-4 w-4 text-muted-foreground group-hover:text-white transition-colors" />
        </Button>
      </div>
    </div>
  </div>
{/if}
