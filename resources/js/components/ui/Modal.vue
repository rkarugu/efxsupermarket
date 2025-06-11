<template>
    <div class="modal fade">
        <div class="modal-dialog" :class="[modalClass]">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">{{ modalTitle }}</h4>
                </div>
                <div class="modal-body">
                    <slot name="modal-body"></slot>
                </div>
                <div class="modal-footer">
                    <button 
                        type="button" 
                        class="btn btn-primary pull-right" 
                        style="margin-left: 10px" 
                        :disabled=processing 
                        @click="$emit('submitClicked')"
                        v-if="!hidePrimaryButton"
                    >
                        {{ buttonText }}
                    </button>

                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

defineProps({
    modalTitle: {
        type: String,
        required: true
    },
    buttonText: {
        type: String,
        default: 'Submit'
    },
    processing: {
        type: Boolean,
        default: false
    },
    hidePrimaryButton: {
        type: Boolean,
        default: false
    },
    modalClass: {
        type: String,
        default: ''
    }
})

defineEmits(['submitClicked'])

</script>
