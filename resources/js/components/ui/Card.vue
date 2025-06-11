<template>
    <div class="box box-primary">
        <div class="box-header with-border" v-if="cardTitle">
            <div class="box-header-flex">
                <h3 class="box-title"> {{ cardTitle }} </h3>

                <slot name="header-action"></slot>
            </div>
        </div>
        <div class="box-body">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>

defineProps({
    cardTitle: {
        type: String,
        default: ''
    }
});

</script>