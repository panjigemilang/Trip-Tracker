<script lang="ts">
	import type { HTMLInputAttributes, HTMLInputTypeAttribute } from "svelte/elements";
	import { cn, type WithElementRef } from "$lib/utils.js";

	type InputType = Exclude<HTMLInputTypeAttribute, "file">;

	type Props = WithElementRef<
		Omit<HTMLInputAttributes, "type"> &
			({ type: "file"; files?: FileList } | { type?: InputType; files?: undefined })
	>;

	let {
		ref = $bindable(null),
		value = $bindable(),
		type,
		files = $bindable(),
		class: className,
		"data-slot": dataSlot = "input",
		...restProps
	}: Props = $props();
</script>

{#if type === "file"}
	<input
		bind:this={ref}
		data-slot={dataSlot}
		class={cn(
			"border-input focus-visible:border-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:aria-invalid:border-destructive/50 disabled:bg-input/50 dark:disabled:bg-input/80 h-10 rounded-md border bg-input px-3 py-2 text-base transition-all file:h-6 file:text-sm file:font-medium focus-visible:outline-none md:text-sm file:text-foreground placeholder:text-muted-foreground w-full min-w-0 file:inline-flex file:border-0 file:bg-transparent disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
			className
		)}
		type="file"
		bind:files
		bind:value
		{...restProps}
	/>
{:else}
	<input
		bind:this={ref}
		data-slot={dataSlot}
		class={cn(
			"border-input focus-visible:border-ring focus-visible:shadow-[0_0_10px_rgba(255,42,122,0.4)] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:aria-invalid:border-destructive/50 disabled:bg-input/50 dark:disabled:bg-input/80 h-10 rounded-md border bg-input px-3 py-2 text-base transition-all file:h-6 file:text-sm file:font-medium focus-visible:outline-none md:text-sm file:text-foreground placeholder:text-muted-foreground w-full min-w-0 file:inline-flex file:border-0 file:bg-transparent disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50",
			className
		)}
		{type}
		bind:value
		{...restProps}
	/>
{/if}
