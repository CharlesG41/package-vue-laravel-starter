<script setup>

</script>

<template>
    <div class="space-y-8">
        <div class="bg-red-500">{{ $trans('cyvian.static.add') }}</div>
        <button class="mx-2 p-2 border rounded border-blue-400" @click="$setLocale('en')">EN</button>
        <button class="mx-2 p-2 border rounded border-blue-400"  @click="$setLocale('fr')">FR</button>
        <div>
            Current locale is: {{$locale()}}
        </div>
        <div>
            Current route is : {{ route('charlesg.home') }}
        </div>
    </div>
</template>
