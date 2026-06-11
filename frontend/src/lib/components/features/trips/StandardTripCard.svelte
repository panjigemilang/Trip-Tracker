<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import { Calendar, MapPin } from 'lucide-svelte';

  export let title: string;
  export let imageSrc: string;
  export let dateRange: string;
  export let location: string;
  export let status: string;
</script>

<CyberCard class="overflow-hidden p-0 relative border-border/50 transition-transform hover:scale-[1.02]">
  <div class="h-40 w-full relative">
    <div class="absolute inset-0 bg-linear-to-t from-[#16171E] to-transparent z-10"></div>
    <img src={imageSrc} alt={title} class="h-full w-full object-cover" />
    <div class="absolute top-3 right-3 z-20">
      <StatusBadge {status} />
    </div>
  </div>
  
  <div class="p-4 relative z-20 bg-[#16171E]">
    <h3 class="text-lg font-bold mb-2 tracking-wide">{title}</h3>
    <div class="flex items-center gap-4 text-xs font-medium text-muted-foreground">
      <div class="flex items-center gap-1 text-secondary">
        <Calendar class="h-4 w-4" />
        {dateRange}
      </div>
      <div class="flex items-center gap-1">
        <MapPin class="h-4 w-4" />
        {location}
      </div>
    </div>
  </div>
</CyberCard>
