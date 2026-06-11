<script lang="ts">
  import CyberCard from '$lib/components/shared/CyberCard.svelte';
  import NeonText from '$lib/components/shared/NeonText.svelte';
  import { Button } from '$lib/components/ui/button';
  import { MapPin, Navigation, SkipForward, XCircle } from 'lucide-svelte';
</script>

<div class="flex flex-col h-[calc(100vh-8rem)] md:h-[calc(100vh-4rem)] max-w-7xl mx-auto md:p-6 w-full">
  
  <div class="hidden md:flex items-center justify-between mb-6">
    <div class="flex items-center gap-4">
      <div class="w-1 h-6 bg-secondary"></div>
      <h1 class="text-2xl font-bold tracking-widest text-white uppercase">Journey_Map</h1>
      <span class="border border-secondary text-secondary text-[10px] px-2 py-0.5 tracking-widest uppercase">Live_Status</span>
    </div>
  </div>

  <div class="flex flex-col md:flex-row gap-6 h-full">
    <!-- Left Sidebar (Timeline) -->
    <div class="w-full md:w-80 flex flex-col gap-6 order-2 md:order-1">
      <div class="md:hidden flex items-center justify-between border-b border-border/50 pb-4 mb-2">
        <h1 class="text-2xl font-bold tracking-widest text-white uppercase">Journey_Map</h1>
        <span class="border border-secondary text-secondary text-[10px] px-2 py-0.5 tracking-widest uppercase">Live_Status</span>
      </div>

      <div class="flex-1 relative">
        <!-- Timeline Line -->
        <div class="absolute left-2.75 top-4 bottom-4 w-px bg-border z-0"></div>

        <div class="space-y-8 relative z-10">
          <!-- Item 1 (Past) -->
          <div class="flex gap-4">
            <div class="h-6 w-6 rounded-full border-2 border-muted bg-background flex items-center justify-center mt-1 shrink-0">
              <div class="h-2 w-2 rounded-full bg-muted"></div>
            </div>
            <div>
              <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">08:30 AM</div>
              <h4 class="text-sm font-bold text-muted-foreground">Akihabara Station</h4>
              <p class="text-xs text-muted-foreground/70 mt-1">Arrival via Yamanote Line</p>
            </div>
          </div>

          <!-- Item 2 (Current) -->
          <div class="flex gap-4">
            <div class="h-6 w-6 rounded-full border-2 border-primary bg-background flex items-center justify-center mt-1 shrink-0 shadow-[0_0_10px_rgba(255,42,122,0.5)]">
              <div class="h-2 w-2 rounded-full bg-primary"></div>
            </div>
            <div>
              <div class="text-[10px] text-primary uppercase tracking-widest mb-1 flex items-center gap-2">
                10:15 AM — CURRENT
              </div>
              <NeonText class="text-lg font-bold block mb-1">Tsukiji Outer Market</NeonText>
              <p class="text-xs text-muted-foreground mt-1 max-w-50 leading-relaxed">Exploring local street food and culinary artifacts.</p>
              <div class="flex gap-2 mt-3">
                <span class="text-[8px] uppercase tracking-widest px-2 py-0.5 border border-primary/30 text-primary bg-primary/5 rounded">Station_B4</span>
                <span class="text-[8px] uppercase tracking-widest px-2 py-0.5 border border-secondary/30 text-secondary bg-secondary/5 rounded">Food_Hub</span>
              </div>
            </div>
          </div>

          <!-- Item 3 (Future) -->
          <div class="flex gap-4">
            <div class="h-6 w-6 rounded-full border-2 border-border bg-background flex items-center justify-center mt-1 shrink-0"></div>
            <div>
              <div class="text-[10px] text-muted-foreground uppercase tracking-widest mb-1">12:30 PM — PLANNED</div>
              <h4 class="text-sm font-bold text-white">Ginza District</h4>
              <p class="text-xs text-muted-foreground/70 mt-1 italic">Scanning commercial sector...</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side (Map & Controls) -->
    <div class="flex-1 flex flex-col gap-6 order-1 md:order-2">
      <!-- Map View placeholder -->
      <CyberCard class="flex-1 p-0 relative overflow-hidden min-h-75 md:min-h-0 border-primary/30">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1542931287-023b922fa89b?q=80&w=1200')] bg-cover bg-center opacity-40"></div>
        <div class="absolute inset-0 bg-linear-to-t from-[#0B0C10] via-transparent to-[#0B0C10]/50"></div>
        
        <div class="absolute bottom-6 left-6 z-20">
          <div class="flex items-center gap-2 text-secondary text-[10px] font-bold uppercase tracking-widest mb-1">
            <MapPin class="h-3 w-3" /> District_04 // Chuo City
          </div>
          <h2 class="text-4xl font-bold tracking-wider text-white">Tsukiji Outer Market</h2>
        </div>

        <!-- Metrics overlay on desktop -->
        <div class="hidden md:block absolute top-6 right-6 z-20 w-64 p-4 bg-background/80 backdrop-blur-md border border-border rounded-lg">
          <div class="text-[10px] text-primary uppercase tracking-widest mb-4 font-bold">Active_Metrics</div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <div class="text-[8px] text-muted-foreground uppercase tracking-widest mb-1">Time Spent</div>
              <div class="text-2xl font-bold">42 <span class="text-xs font-normal">min</span></div>
            </div>
            <div>
              <div class="text-[8px] text-muted-foreground uppercase tracking-widest mb-1">Calories</div>
              <div class="text-2xl font-bold text-yellow-500">315 <span class="text-xs font-normal">kcal</span></div>
            </div>
            <div>
              <div class="text-[8px] text-muted-foreground uppercase tracking-widest mb-1">Distance</div>
              <NeonText color="secondary" class="text-2xl font-bold block">1.2 <span class="text-xs font-normal text-white">km</span></NeonText>
            </div>
          </div>
        </div>
      </CyberCard>

      <!-- Logging & Controls -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-auto md:h-48">
        <div class="flex flex-col">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-4 h-px bg-primary"></div>
            <h3 class="text-sm font-bold tracking-widest uppercase">Digital_Log</h3>
          </div>
          <textarea 
            placeholder="Input segment observations..." 
            class="flex-1 w-full rounded-md border border-border bg-card px-3 py-2 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] transition-all resize-none mb-2"
          ></textarea>
          <div class="flex gap-2">
            <span class="text-[10px] px-2 py-1 bg-muted rounded text-muted-foreground uppercase tracking-widest">#SUSHI</span>
            <span class="text-[10px] px-2 py-1 bg-muted rounded text-muted-foreground uppercase tracking-widest">#MORNING_WALK</span>
            <button class="text-[10px] px-2 py-1 border border-border rounded text-muted-foreground uppercase tracking-widest hover:bg-muted">+ ADD TAG</button>
          </div>
        </div>

        <CyberCard class="flex flex-col justify-center gap-4 border-dashed border-border p-6 bg-transparent">
          <p class="text-xs text-muted-foreground text-center mb-2">System is currently tracking this segment in high-fidelity mode. Automated check-outs will occur if GPS drift exceeds 500m.</p>
          <div class="flex gap-4">
            <Button variant="outline" class="flex-1 h-12 border-primary/50 text-primary hover:bg-primary/10 tracking-widest uppercase font-bold shadow-none">
              <SkipForward class="mr-2 h-4 w-4" /> Skip
            </Button>
            <Button variant="outline" class="flex-1 h-12 bg-transparent border-border hover:bg-muted tracking-widest uppercase font-bold shadow-none text-muted-foreground">
              <XCircle class="mr-2 h-4 w-4" /> Cancel
            </Button>
          </div>
        </CyberCard>
      </div>
    </div>
  </div>
</div>
