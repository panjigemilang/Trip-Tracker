<script lang="ts">
  import { Button } from "$lib/components/ui/button"
  import { AlertTriangle, CheckCircle, X } from "lucide-svelte"
  import { formatDate, formatTime } from "$lib/utils/dateFormatter"

  let {
    isOpen = $bindable(false),
    rows = [],
    trip = null,
    onConfirm,
  } = $props<{
    isOpen: boolean
    rows: any[]
    trip: any
    onConfirm: () => void
  }>()

  // Validate dates against trip
  let validatedRows = $derived(
    rows.map((row: any, index: number) => {
      let isDateValid = true
      let dateError = ""

      if (!row.date) {
        isDateValid = false
        dateError = "Missing date"
      } else if (trip && (trip.start_date || trip.end_date)) {
        const rowDate = new Date(row.date)
        const startDate = trip.start_date ? new Date(trip.start_date) : null
        const endDate = trip.end_date ? new Date(trip.end_date) : null

        // Reset hours for accurate YYYY-MM-DD comparison
        rowDate.setHours(0, 0, 0, 0)
        if (startDate) startDate.setHours(0, 0, 0, 0)
        if (endDate) endDate.setHours(0, 0, 0, 0)

        if (startDate && rowDate < startDate) {
          isDateValid = false
          dateError = `Before trip start (${formatDate(trip.start_date)})`
        } else if (endDate && rowDate > endDate) {
          isDateValid = false
          dateError = `After trip end (${formatDate(trip.end_date)})`
        }
      }

      return {
        ...row,
        _index: index + 1,
        _isDateValid: isDateValid,
        _dateError: dateError,
      }
    }),
  )

  let hasErrors = $derived(validatedRows.some((r: any) => !r._isDateValid))

  function close() {
    isOpen = false
  }
</script>

{#if isOpen}
  <div
    class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-black/80 backdrop-blur-sm animate-in fade-in duration-200"
  >
    <div
      class="bg-[#0B0C10] border border-primary/30 w-full max-w-5xl max-h-[90vh] flex flex-col rounded-xl shadow-[0_0_40px_rgba(255,42,122,0.15)] animate-in zoom-in-95 duration-200"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between p-6 border-b border-border/50 shrink-0"
      >
        <div class="flex items-center gap-3">
          <div class="w-1 h-6 bg-primary"></div>
          <div>
            <h2
              class="text-xs font-bold tracking-widest text-primary mb-1 uppercase"
            >
              Validation // Preview
            </h2>
            <h1 class="text-xl font-bold tracking-widest text-white uppercase">
              Data_Ingestion_Preview
            </h1>
          </div>
        </div>
        <button
          onclick={close}
          class="p-2 hover:bg-white/5 rounded-full text-muted-foreground hover:text-white transition-colors"
        >
          <X class="w-5 h-5" />
        </button>
      </div>

      <!-- Content -->
      <div class="flex-1 overflow-auto p-6">
        {#if hasErrors}
          <div
            class="mb-6 p-4 border border-red-500/20 bg-red-500/5 text-red-500 text-sm rounded-lg flex items-start gap-3 font-mono"
          >
            <AlertTriangle class="w-5 h-5 shrink-0 mt-0.5" />
            <div>
              <strong class="block uppercase tracking-widest mb-1"
                >Validation Failures Detected</strong
              >
              <p>
                Some records contain dates outside the selected trip's active
                range ({formatDate(trip?.start_date)} to {formatDate(
                  trip?.end_date,
                )}). Please review and correct the highlighted fields in your
                file.
              </p>
            </div>
          </div>
        {:else}
          <div
            class="mb-6 p-4 border border-secondary/20 bg-secondary/5 text-secondary text-sm rounded-lg flex items-center gap-3 font-mono"
          >
            <CheckCircle class="w-5 h-5 shrink-0" />
            <span class="uppercase tracking-widest font-bold"
              >All records passed date validation constraints.</span
            >
          </div>
        {/if}

        <div class="rounded-md border border-border overflow-x-auto">
          <table class="w-full text-left text-sm font-mono">
            <thead
              class="bg-[#16171E] text-muted-foreground text-xs uppercase tracking-widest"
            >
              <tr>
                <th class="px-4 py-3 border-b border-border font-semibold w-16"
                  >Row</th
                >
                <th class="px-4 py-3 border-b border-border font-semibold"
                  >Date</th
                >
                <th class="px-4 py-3 border-b border-border font-semibold"
                  >Time</th
                >
                <th class="px-4 py-3 border-b border-border font-semibold"
                  >Title</th
                >
                <th class="px-4 py-3 border-b border-border font-semibold"
                  >Location</th
                >
              </tr>
            </thead>
            <tbody class="divide-y divide-border/50 bg-[#0B0C10]">
              {#each validatedRows as row}
                <tr
                  class="hover:bg-white/5 transition-colors {!row._isDateValid
                    ? 'bg-red-500/5 hover:bg-red-500/10'
                    : ''}"
                >
                  <td class="px-4 py-3 text-muted-foreground">{row._index}</td>
                  <td class="px-4 py-3">
                    {#if !row._isDateValid}
                      <div class="flex flex-col">
                        <span class="text-red-500 font-bold"
                          >{formatDate(row.date) || "Empty"}</span
                        >
                        <span
                          class="text-[10px] text-red-500/80 uppercase tracking-wider mt-0.5"
                          >{row._dateError}</span
                        >
                      </div>
                    {:else}
                      <span class="text-white">{formatDate(row.date)}</span>
                    {/if}
                  </td>
                  <td class="px-4 py-3 text-gray-300"
                    >{formatTime(row.time) || "-"}</td
                  >
                  <td class="px-4 py-3 text-white font-sans"
                    >{row.title || "-"}</td
                  >
                  <td
                    class="px-4 py-3 text-gray-400 font-sans truncate max-w-[200px]"
                    >{row.location || "-"}</td
                  >
                </tr>
              {/each}
              {#if rows.length === 0}
                <tr>
                  <td
                    colspan="5"
                    class="px-4 py-8 text-center text-muted-foreground italic"
                    >No data found in file.</td
                  >
                </tr>
              {/if}
            </tbody>
          </table>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="p-6 border-t border-border/50 flex items-center justify-end gap-4 shrink-0 bg-[#0B0C10]"
      >
        <Button
          variant="outline"
          onclick={close}
          class="font-mono uppercase tracking-widest border-border text-muted-foreground hover:text-white"
        >
          Cancel
        </Button>
        <Button
          onclick={() => {
            close()
            onConfirm()
          }}
          disabled={hasErrors || rows.length === 0}
          class="bg-primary text-primary-foreground hover:bg-primary/80 font-mono tracking-widest uppercase font-bold shadow-[0_0_15px_rgba(255,42,122,0.4)] disabled:opacity-50 disabled:shadow-none"
        >
          Confirm Ingestion
        </Button>
      </div>
    </div>
  </div>
{/if}
