var winRateCh = document.getElementById("win-rate-chart");
var winRateChart = new Chart(winRateCh, {
  type: 'pie',
  data = {
    datasets: [{
      data: [84, 68]
    }],
    labels: [
      'wins',
      'Losses'
    ],
}
});
