<template>
  <svg
    width="25"
    height="25"
    viewBox="0 0 25 25"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
  >
    <g clip-path="url(#clip0_2_487)">
      <path
        d="M23.2143 15.9809C23.2143 17.4227 22.015 18.5916 20.5357 18.5916H3.04837L6.57515 15.1543L5.31243 13.9237L0.261662 18.8466C-0.0868859 19.1865 -0.0868859 19.7372 0.261662 20.0772L5.31249 25L6.57504 23.7694L3.04832 20.3321H20.5357C23 20.3291 24.997 18.3828 25 15.9809V12.5H23.2143V15.9809ZM1.78577 9.01908C1.78577 7.5771 2.98493 6.40838 4.46433 6.40838H21.9518L18.4251 9.84579L19.6876 11.0763L24.7385 6.15346C25.0871 5.81352 25.0871 5.26283 24.7385 4.9229L19.6876 0L18.425 1.23056L21.9518 4.66797H4.46428C2 4.67069 0.00284598 6.61729 0 9.01913V12.5001H1.78571L1.78577 9.01908Z"
        fill="#9553FF"
      />
    </g>
    <defs>
      <clipPath id="clip0_2_487">
        <rect width="25" height="25" fill="white" />
      </clipPath>
    </defs>
  </svg>
</template>

<script lang="ts" setup>
const props = defineProps({
  fillColor: {
    type: String,
    default: 'none',
  },
  strokeColor: {
    type: String,
    default: '#FFFFFF',
  },
  sizeWidth: {
    type: Number,
    default: 24,
  },
  sizeHeight: {
    type: Number,
    default: 24,
  },
})
</script>
