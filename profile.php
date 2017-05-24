<html>
  <head>
    <title>Profile Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"  />
    <link href="https://fonts.googleapis.com/css?family=Baloo+Bhaina" rel="stylesheet">
    <link rel="stylesheet" href="styles/profile.css" />
  </head>

  <body>

    <?php
    /* Global */

      $api_key = "RGAPI-53ead5ad-7174-4f7a-bb1d-e463d01c1b19";
      $sumName = $_POST['userName'];
      $sumName = strtolower($sumName);
      $url = "https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $sumName . "?api_key=" . $api_key;
      $json = file_get_contents($url);
      $data = json_decode($json, TRUE);
      echo $data;
    ?>


    <header class="header-main">
      <div class="container">
        <div id="branding">
          <h1>Lookup Home</h1>
        </div>
        <form>
          <input type="text" placeholder="summoner name">
          <button type="submit" class="submitBtn">Enter</button>
        </form>
        <nav>
          <ul>
            <li><a href="{{ url_for('leaderboards') }}">Leaderboards</a></li>
            <li><a href="http://na.leagueoflegends.com/en/news/game-updates/patch/patch-79-notes" target="_blank">Patch Notes</a></li>
          </ul>
        </nav>
      </div>
    </header>

    <div class="container">

    </div>



    TEST123 TEST321

  </body>
</html>
