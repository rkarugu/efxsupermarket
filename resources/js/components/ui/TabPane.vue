<template>
    <div class="tab-pane" :class="{'active': active}" :id="tabPaneId">
        <slot></slot>
    </div>
</template>

<script setup>

defineProps({
    active: {
        type: Boolean,
        required: false,
        default: false
    },
    tabPaneId: {
        type: String,
        required: true
    }
})

</script>
