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
                Vous vous apprêtez à modifier votre adresse email de connexion à la marketplace QANTIS.<br>
                Un email va vous être adressé sur l’email que vous venez de renseigner afin de valider ce changement<br>
                Etes-vous sûr de vouloir continuer ?
            </div>
            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button data-modal-hide="defaultModal" type="button"
                        class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600"
                        @click="emit('cancel')">
                    Annuler
                </button>
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
