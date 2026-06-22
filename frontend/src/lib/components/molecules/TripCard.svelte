<script lang="ts">
    import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '$lib/components/ui/card';
    import { MapPin, CalendarDays, ArrowRight } from 'lucide-svelte';
    import StatusBadge from '$lib/components/atoms/StatusBadge.svelte';
    import { Button } from '$lib/components/ui/button';

    let { trip, class: className = '' } = $props();
</script>

<Card class={className}>
    <CardHeader class="pb-3">
        <div class="flex justify-between items-start mb-2">
            <CardTitle class="text-xl">{trip.title}</CardTitle>
            <StatusBadge status={trip.status} />
        </div>
        {#if trip.description}
            <CardDescription class="line-clamp-2">{trip.description}</CardDescription>
        {/if}
    </CardHeader>
    <CardContent class="pb-4">
        <div class="flex flex-col gap-2 text-sm text-slate-500 dark:text-slate-400">
            {#if trip.start_date && trip.end_date}
                <div class="flex items-center gap-2">
                    <CalendarDays class="w-4 h-4" />
                    <span>{trip.start_date} to {trip.end_date}</span>
                </div>
            {:else}
                <div class="flex items-center gap-2 italic">
                    <CalendarDays class="w-4 h-4" />
                    <span>No dates set</span>
                </div>
            {/if}
            <div class="flex items-center gap-2">
                <MapPin class="w-4 h-4" />
                <span>{trip.activity_count || 0} activities planned</span>
            </div>
        </div>
    </CardContent>
    <CardFooter>
        <Button variant="outline" class="w-full justify-between" href={`/trips/${trip.slug || 'trip'}/${trip.id}`}>
            View Trip Details
            <ArrowRight class="w-4 h-4" />
        </Button>
    </CardFooter>
</Card>
