<template>
  <div class="mt-10 flex h-20 w-full items-center justify-center">
    <div id="loading">
      <div class="flex justify-center">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
      </div>

      <div class="mt-3 text-primary">Chargement en cours...</div>
    </div>
  </div>
</template>
<script lang="ts" setup></script>
<style scoped>
@keyframes pop {
  0% {
    transform: scale(0);
    box-shadow: none;
  }
  50% {
    transform: scale(1.5);
    box-shadow: 0 0 7px #9553ff;
  }
  100% {
    transform: scale(1);
    box-shadow: 0 0 5px #9553ff;
  }
}

@keyframes ellipsis {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

#loading {
  @apply flex w-[200px] flex-col justify-center text-center;
  margin: 100px auto;
}

.circle {
  float: left;
  margin-left: 9px;
  border-radius: 100%;
  width: 10px;
  height: 10px;
  background: #fff;
  animation: pop 1s infinite alternate;
  transform: scale(0);
}

.circle:nth-child(1) {
  margin-left: 5px;
}

.circle:nth-child(2) {
  animation-delay: 0.1s;
}

.circle:nth-child(3) {
  animation-delay: 0.2s;
}

.circle:nth-child(4) {
  animation-delay: 0.3s;
}

.circle:nth-child(5) {
  animation-delay: 0.4s;
}

.circle:nth-child(6) {
  animation-delay: 0.49s;
}
</style>
