
<template>
    <div class="box-header-flex">
        <h4> {{ cardTitle }} </h4>

        <slot name="header-action"></slot>
    </div>
<div style="width:100%; border: 1px solid #eee">
    <slot></slot>
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

