<template>
    <div class="col-lg-3 col-xs-6">
        <div :class="`small-box ${color}`">
            <div class="inner">
                <h3 v-if="value !== null">{{ value }}</h3>
                <h3 v-else><i class="fas fa-spinner fa-spin"></i></h3>
                <p>{{ title }}</p>
            </div>
            <div class="icon" style="font-size: 50px; margin-top:10px">
                <i :class="icon" class="icon-hover"></i>
            </div>
            <a :href="`#${sectionId}`" class="small-box-footer">
                Go To 
                <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</template>

<script setup>

const { color } = defineProps({
    color: {
        type: String,
        default: 'bg-primary'
    },
    title: {
        type: String,
        required: true
    },
    value: {
        type: [String, Number, null],
        required: true
    },
    icon: {
        type: String,
        default: ''
    },
    route: {
        type: String,
        default: '#'
    },
    sectionId: {
        type: String,
        default: ''
    }
})

</script>
<style scoped>
.icon-hover {
    transition: transform 0.3s ease; 
}

.inner:hover + .icon .icon-hover, 
.icon-hover:hover { 
    transform: scale(1.2); 
}
</style>
