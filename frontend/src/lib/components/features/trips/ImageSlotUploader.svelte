<script lang="ts">
  import { ImagePlus } from 'lucide-svelte';
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
</script>

<div class="grid grid-cols-3 gap-4 mb-8">
  <button class="relative aspect-4/5 md:aspect-video w-full group rounded-xl border border-dashed border-border hover:border-primary/50 transition-colors bg-card hover:bg-card/80 overflow-hidden flex flex-col items-center justify-center gap-2">
    <ImagePlus class="h-6 w-6 text-primary group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(255,42,122,0.3)]" />
    <span class="text-[10px] text-muted-foreground uppercase tracking-widest font-semibold">SLOT_01</span>
  </button>
  
  <button class="relative aspect-4/5 md:aspect-video w-full group rounded-xl border border-dashed border-border hover:border-primary/50 transition-colors bg-card hover:bg-card/80 overflow-hidden flex flex-col items-center justify-center gap-2">
    <ImagePlus class="h-6 w-6 text-primary group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(255,42,122,0.3)]" />
    <span class="text-[10px] text-muted-foreground uppercase tracking-widest font-semibold">SLOT_02</span>
  </button>
  
  <button class="relative aspect-4/5 md:aspect-video w-full group rounded-xl border border-dashed border-border hover:border-primary/50 transition-colors bg-card hover:bg-card/80 overflow-hidden flex flex-col items-center justify-center gap-2">
    <ImagePlus class="h-6 w-6 text-primary group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(255,42,122,0.3)]" />
    <span class="text-[10px] text-muted-foreground uppercase tracking-widest font-semibold">SLOT_03</span>
  </button>
</div>
