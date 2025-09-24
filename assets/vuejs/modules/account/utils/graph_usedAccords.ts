import { VueUiDonutConfig } from 'vue-data-ui'

export const usedAccordsGraphConfig = (): VueUiDonutConfig => {
  return {
    autoSize: false,
    responsive: false,
    useBlurOnHover: false,
    userOptions: {
      show: false,
    },
    style: {
      chart: {
        useGradient: false,
        width: 500,
        height: 500,
        layout: {
          labels: {
            dataLabels: {
              show: false,
            },
            hollow: {
              total: {
                show: false,
              },
              average: {
                show: false,
              },
            },
          },
        },
        legend: {
          show: false,
        },
        tooltip: {
          show: true,
          backgroundOpacity: 90,
        },
      },
    },
  }
}
