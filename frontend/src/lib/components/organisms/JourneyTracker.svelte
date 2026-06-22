<script lang="ts">
  import type {
    Journey,
    JourneyActivity,
    ActivityStatus,
  } from "$lib/types/journey"
  import type { Activity } from "$lib/types/trip"
  import { Button } from "$lib/components/ui/button"
  import { Card, CardContent } from "$lib/components/ui/card"
  import { Badge } from "$lib/components/ui/badge"
  import {
    Clock,
    MapPin,
    CheckCircle,
    XCircle,
    SkipForward,
  } from "lucide-svelte"

  let {
    journey,
    onUpdateStatus,
  }: {
    journey: Journey
    onUpdateStatus: (activityId: string, status: ActivityStatus) => void
  } = $props()

  let activities = $derived(journey.trip?.activities || [])
  let journeyActivities = $derived(
    (journey.journey_activities || []).reduce(
      (acc: Record<string, JourneyActivity>, ja: JourneyActivity) => {
        acc[ja.activity_id] = ja
        return acc
      },
      {},
    ),
  )

  function getStatus(activityId: string): ActivityStatus {
    return journeyActivities[activityId]?.status || "upcoming"
  }

  function getStatusColor(status: ActivityStatus) {
    switch (status) {
      case "current":
        return "bg-blue-500"
      case "completed":
        return "bg-green-500"
      case "skipped":
        return "bg-gray-400"
      case "missed":
        return "bg-red-500"
      default:
        return "bg-gray-200 text-gray-800"
    }
  }

  function handleAction(activityId: string, status: ActivityStatus) {
    onUpdateStatus(activityId, status)
  }
</script>

<div class="space-y-6 relative border-l-2 border-gray-200 ml-4 pl-6">
  {#each activities as activity}
    {@const status = getStatus(activity.id)}
    {@const isCurrent = status === "current"}
    <div class="relative">
      <!-- Timeline Dot -->
      <div
        class={`absolute -left-8.75 w-6 h-6 rounded-full border-4 border-white ${getStatusColor(status)} flex items-center justify-center`}
      >
        {#if status === "completed"}
          <CheckCircle class="w-4 h-4 text-white" />
        {:else if status === "missed"}
          <XCircle class="w-4 h-4 text-white" />
        {:else if status === "skipped"}
          <SkipForward class="w-4 h-4 text-white" />
        {/if}
      </div>

      <Card
        class={isCurrent
          ? "border-blue-500 shadow-md ring-2 ring-blue-500/20"
          : "opacity-80"}
      >
        <CardContent class="p-4">
          <div class="flex flex-col sm:flex-row justify-between gap-4">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <Badge
                  variant={isCurrent ? "default" : "secondary"}
                  class={isCurrent ? "bg-blue-500 hover:bg-blue-600" : ""}
                >
                  {status.toUpperCase()}
                </Badge>
                <span
                  class="text-sm text-gray-500 font-medium flex items-center gap-1"
                >
                  <Clock class="w-4 h-4" />
                  {activity.time.substring(0, 5)}
                </span>
              </div>
              <h3
                class={`text-lg font-bold ${isCurrent ? "text-blue-900 dark:text-blue-100" : ""}`}
              >
                {activity.title}
              </h3>
              {#if activity.location}
                <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                  <MapPin class="w-4 h-4" />
                  {activity.location}
                </p>
              {/if}
            </div>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-2">
              {#if status === "upcoming" || status === "current"}
                <Button
                  size="sm"
                  variant="outline"
                  class="text-green-600 border-green-200 hover:bg-green-50"
                  onclick={() => handleAction(activity.id, "completed")}
                >
                  Complete
                </Button>
                <Button
                  size="sm"
                  variant="ghost"
                  class="text-gray-500"
                  onclick={() => handleAction(activity.id, "skipped")}
                >
                  Skip
                </Button>
              {/if}
              {#if status === "completed" || status === "skipped"}
                <Button
                  size="sm"
                  variant="ghost"
                  class="text-gray-400"
                  disabled
                >
                  Done
                </Button>
              {/if}
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  {/each}
</div>
