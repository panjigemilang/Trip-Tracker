<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { Button } from '$lib/components/ui/button';
  import { Calendar } from 'lucide-svelte';

  let {
    title,
    imageSrc,
    description,
    status,
    tripId = '',
    tripSlug = '',
    journeyId = null,
    startDate = null,
    endDate = null
  }: {
    title: string;
    imageSrc: string;
    description: string;
    status: string;
    tripId?: string;
    tripSlug?: string;
    journeyId?: string | null;
    startDate?: string | null;
    endDate?: string | null;
  } = $props();

  const daysLeft = $derived.by(() => {
    if (!startDate) return null;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const start = new Date(startDate);
    start.setHours(0, 0, 0, 0);
    const diffTime = start.getTime() - today.getTime();
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
  });

  function formatDate(dStr: string) {
    if (!dStr) return '';
    return new Date(dStr).toLocaleDateString(undefined, { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' 
    });
  }
</script>

<CyberCard class="overflow-hidden p-0 relative h-100 border border-primary/30" glowState="primary">
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-linear-to-r from-[#0B0C10] via-[#0B0C10]/80 to-transparent z-10"></div>
    <img src={imageSrc} alt={title} class="h-full w-full object-cover opacity-60 mix-blend-screen" />
  </div>
  
  <div class="relative z-20 p-8 h-full flex flex-col justify-center max-w-xl">
    <div class="mb-4 inline-block">
      <StatusBadge {status} class="text-xs px-3 py-1" />
    </div>
    
    <NeonText class="text-5xl tracking-widest uppercase mb-2 leading-tight">
      {@html title.replace(' ', '<br>')}
    </NeonText>

    {#if startDate}
      <div class="flex items-center gap-3 text-xs font-mono text-secondary mb-6 uppercase tracking-widest font-semibold">
        <div class="flex items-center gap-1.5">
          <Calendar class="h-4 w-4 text-secondary" />
          {formatDate(startDate)} {#if endDate} — {formatDate(endDate)}{/if}
        </div>
        {#if status !== 'completed' && status !== 'cancelled' && status !== 'active' && daysLeft !== null && daysLeft >= 0}
          <span class="text-primary font-mono text-[10px] uppercase tracking-widest bg-primary/10 px-2 py-0.5 rounded-sm border border-primary/20 shrink-0">
            {#if daysLeft === 0}
              Starts Today
            {:else if daysLeft === 1}
              1 Day Left
            {:else}
              {daysLeft} Days Left
            {/if}
          </span>
        {/if}
      </div>
    {/if}
    
    <p class="text-muted-foreground mb-8 text-sm leading-relaxed max-w-md">
      {description}
    </p>
    
    <div class="flex gap-4">
      {#if journeyId}
        <Button href="/journey/{journeyId}" class="px-8 py-6 rounded-none bg-primary text-primary-foreground hover:bg-primary/90 font-bold tracking-widest shadow-[0_0_20px_rgba(255,42,122,0.6)]">
          RESUME SESSION
        </Button>
      {:else}
        <Button href="/trips/{tripSlug || 'trip'}/{tripId}" class="px-8 py-6 rounded-none bg-primary text-primary-foreground hover:bg-primary/90 font-bold tracking-widest shadow-[0_0_20px_rgba(255,42,122,0.6)]">
          VIEW DETAILS
        </Button>
      {/if}
    </div>
  </div>
</CyberCard>
