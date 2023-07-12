<template>
    <div
            class="modal-overlay"
    >
        <div
                class="fixed bg-secondary px-2 py-3 rounded-xl text-white flex flex-col justify-center items-center
      text-center top-[45%] left-[45%] w-[500px] ml-[-150px] !opacity-100 z-9999">
            <div class="flex justify-end relative w-full mb-3">
                <button class="button-secondary text-lg right-2 text-white" @click="emit('cancel')">
                    X
                </button>
            </div>
            <h3 class="text-base md:text-lg lg:text-2xl">Attention</h3>
            <div class="pt-3 pb-6 text-sm md:text-base lg:text-lg">
                Votre demande a bien été prise en compte, merci de consulter vos emails
            </div>
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="defaultModal" type="button"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                        @click="emit('validate')">
                    Valider
                </button>
            </div>
        </div>

    </div>
</template>

<script lang="ts" setup>
const emit = defineEmits(['cancel', 'validate'])
</script>

<style scoped>
.modal {
    z-index: 999;
}

.modal-overlay {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    background-color: #000000da;
}
</style>
