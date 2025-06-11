<template>
    <li :class="{'active': active}">
        <a :href="`#${tabPaneId}`" data-toggle="tab">
            {{ tabTitle }}
        </a>
    </li>
</template>

<script setup>

defineProps({
    active: {
        type: Boolean,
        required: false,
        default: false
    },
    tabTitle: {
        type: String,
        required: true
    },
    tabPaneId: {
        type: String,
        required: true
    }
})

</script>
