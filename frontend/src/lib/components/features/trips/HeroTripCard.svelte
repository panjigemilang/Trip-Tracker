<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import StatusBadge from '$lib/components/shared/StatusBadge.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { Button } from '$lib/components/ui/button';

  export let title: string;
  export let imageSrc: string;
  export let description: string;
  export let status: string;
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
    
    <NeonText class="text-5xl tracking-widest uppercase mb-4 leading-tight">
      {@html title.replace(' ', '<br>')}
    </NeonText>
    
    <p class="text-muted-foreground mb-8 text-sm leading-relaxed max-w-md">
      {description}
    </p>
    
    <div class="flex gap-4">
      <Button class="px-8 py-6 rounded-none bg-primary text-primary-foreground hover:bg-primary/90 font-bold tracking-widest shadow-[0_0_20px_rgba(255,42,122,0.6)]">
        RESUME SESSION
      </Button>
      <Button variant="outline" class="px-8 py-6 rounded-none border-secondary text-secondary hover:bg-secondary/10 hover:text-secondary font-bold tracking-widest shadow-[0_0_20px_rgba(0,230,184,0.1)]">
        VIEW LOGISTICS
      </Button>
    </div>
  </div>
</CyberCard>
