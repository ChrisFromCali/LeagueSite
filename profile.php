<html>
  <head>
    <title>Profile Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"  />
    <script src="scripts/Chart.bundle.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Baloo+Bhaina|Monda" rel="stylesheet">
    <link rel="stylesheet" href="styles/profile.css" />
  </head>

  <body>


    <?php /* FUNCTIONS */
      function getJson($url) {
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE);
        return $data;
      }


    ?>
    <?php

    /* Global */

    $api_key = "RGAPI-53ead5ad-7174-4f7a-bb1d-e463d01c1b19";
    $sumName = $_POST['userName'];
    $sumName = strtolower($sumName);

    $url = "https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $sumName . "?api_key=" . $api_key;
    /* accountID, profileID, name, level, profileIcon */
    $initSummonerData = getJson($url);
    $profileIcon = "datadragon/7.10.1/img/profileicon/" . $initSummonerData["profileIconId"] . ".png";
    $name = $initSummonerData["name"];
    $summonerLevel = $initSummonerData["summonerLevel"]
    ?>

    <!-- Nav bar -->
    <nav>
      <div class="logo">
        League Home
      </div>
      <ul>
        <li><a href="#">Match History</a></li>
        <li><a href="#">Leaderboards</a></li>
        <li><a href="#">Patch Notes</a></li>
      </ul>
    </nav>

    <!-- Profile Header -->
    <div class="container">
      <div class="profile-header">
          <img src=<?php echo $profileIcon; ?> />
          <h1><?php echo $name; ?></h1>
          <p><?php echo "Level: " . $summonerLevel ?> </p>
      </div>

      <!-- Chart(s) -->
      <script src="scripts/Chart.bundle.js"></script>
      <script src="scripts/main.js"></script>

      <canvas id="win-rate-chart"></canvas>

    </div>


  </body>
</html>
