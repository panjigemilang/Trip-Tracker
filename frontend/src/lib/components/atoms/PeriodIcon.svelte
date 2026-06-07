<script lang="ts">
    import { Sun, CloudSun, Moon, Sunrise } from 'lucide-svelte';
    import type { ActivityPeriod } from '$lib/utils/calculators/periodCalculator';
    import { cn } from '$lib/utils';

    let { period, class: className = '' }: { period: ActivityPeriod, class?: string } = $props();
</script>

{#if period === 'morning'}
    <Sunrise class={cn('text-amber-500', className)} />
{:else if period === 'afternoon'}
    <Sun class={cn('text-orange-500', className)} />
{:else if period === 'evening'}
    <CloudSun class={cn('text-indigo-400', className)} />
{:else if period === 'night'}
    <Moon class={cn('text-slate-700 dark:text-slate-300', className)} />
{/if}
