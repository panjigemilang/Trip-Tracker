<script lang="ts">
  import { Badge } from '$lib/components/ui/badge';
  
  let { status = 'active', class: className = '' }: { status?: string, class?: string } = $props();

  const statusConfig: Record<string, { label: string, colorClass: string }> = {
    active: { label: 'ACTIVE', colorClass: 'bg-primary/10 text-primary border-primary/30' },
    upcoming: { label: 'UPCOMING', colorClass: 'bg-secondary/10 text-secondary border-secondary/30' },
    planning: { label: 'PLANNING', colorClass: 'bg-[#FFB800]/10 text-[#FFB800] border-[#FFB800]/30' },
    completed: { label: 'COMPLETED', colorClass: 'bg-secondary/10 text-secondary border-secondary/30' },
    failed: { label: 'FAILED', colorClass: 'bg-destructive/10 text-destructive border-destructive/30' },
    cancelled: { label: 'CANCELLED', colorClass: 'bg-destructive/10 text-destructive border-destructive/30' },
    drafting: { label: 'DRAFTING', colorClass: 'bg-[#FFB800]/10 text-[#FFB800] border-[#FFB800]/30' },
    queued: { label: 'QUEUED', colorClass: 'bg-muted/50 text-muted-foreground border-border' }
  };

  const config = statusConfig[status.toLowerCase()] || statusConfig.active;
</script>

<Badge variant="outline" class="rounded-full px-2 py-0.5 text-[10px] uppercase tracking-widest font-semibold {config.colorClass} {className}">
  <span class="mr-1 inline-block h-1.5 w-1.5 rounded-full bg-current"></span>
  {config.label}
</Badge>
