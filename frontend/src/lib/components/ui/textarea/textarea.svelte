<script lang="ts">
	import type { HTMLTextareaAttributes } from "svelte/elements";
	import { cn, type WithElementRef } from "$lib/utils.js";

	type Props = WithElementRef<HTMLTextareaAttributes>;

	let {
		ref = $bindable(null),
		value = $bindable(),
		class: className,
		"data-slot": dataSlot = "textarea",
		...restProps
	}: Props = $props();
</script>

<textarea
	bind:this={ref}
	data-slot={dataSlot}
	class={cn(
		"border-input focus-visible:border-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:aria-invalid:border-destructive/50 disabled:bg-input/50 dark:disabled:bg-input/80 flex min-h-20 w-full rounded-md border bg-input px-3 py-2 text-base transition-all focus-visible:outline-none md:text-sm placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50",
		className
	)}
	bind:value
	{...restProps}
></textarea>
