<script lang="ts">
  import CyberCard from "$lib/components/shared/CyberCard.svelte"
  import StatusBadge from "$lib/components/shared/StatusBadge.svelte"
  import { Calendar } from "lucide-svelte"
  import { getDisplayStatus } from "$lib/utils/tripStatus"
  import { formatDate } from "$lib/utils/dateFormatter"

  let { trip, imageSrc }: { trip: any; imageSrc: string } = $props()

  const status = $derived(getDisplayStatus(trip))

  const dateRange = $derived.by(() => {
    if (!trip.start_date) return "TBD"
    const start = formatDate(trip.start_date)
    const end = trip.end_date ? formatDate(trip.end_date) : ""
    return end ? `${start} — ${end}` : start
  })

  const daysLeft = $derived.by(() => {
    if (!trip.start_date) return null
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const start = new Date(trip.start_date)
    start.setHours(0, 0, 0, 0)
    const diffTime = start.getTime() - today.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays
  })
</script>

<CyberCard
  class="overflow-hidden p-0 relative border-border/50 transition-transform duration-300 hover:scale-[1.02] active:scale-[0.97] active:duration-150"
>
  <div class="h-40 w-full relative">
    <div
      class="absolute inset-0 bg-linear-to-t from-[#16171E] to-transparent z-10"
    ></div>
    <img src={imageSrc} alt={trip.title} class="h-full w-full object-cover" />
  </div>

  <div class="p-4 relative z-20 bg-[#16171E]">
    <div class="flex items-center justify-between gap-2 mb-3">
      <h3 class="text-base font-bold tracking-wide truncate text-white">
        {trip.title}
      </h3>
      <StatusBadge {status} class="shrink-0" />
    </div>

    <div
      class="flex items-center justify-between text-xs font-medium text-muted-foreground gap-2 mb-2"
    >
      <div class="flex items-center gap-2 text-secondary">
        <Calendar class="h-4 w-4" />
        {dateRange}
      </div>
    </div>

    {#if status !== "completed" && status !== "cancelled" && status !== "active" && daysLeft !== null && daysLeft >= 0}
    <div class="flex items-center">
      <div class="size-5"></div>
      <span
        class="text-primary font-mono text-[10px] uppercase tracking-widest bg-primary/10 px-2 py-0.5 rounded-sm border border-primary/20 shrink-0"
      >
        {#if daysLeft === 0}
          Starts Today
        {:else if daysLeft === 1}
          1 Day Left
        {:else}
        {daysLeft} Days Left
        {/if}
      </span>
    </div>
      {/if}
  </div>
</CyberCard>
