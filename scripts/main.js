
var winRateCh = document.getElementById("win-rate-chart").getContext("2d");
var winRateChart = new Chart(winRateCh, {
  type: 'doughnut',
  data:{
    datasets: [{
      data: [131, 118]
    }],

    labels: [
      "wins",
      "losses"
    ],
  }
});
