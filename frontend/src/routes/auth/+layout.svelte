<script lang="ts">
	let { children } = $props();
</script>

<div class="flex min-h-screen items-center justify-center bg-zinc-50 dark:bg-zinc-950 p-4">
	<div class="w-full max-w-md">
		{@render children()}
	</div>
</div>
