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

      /* Generates a single Json with input of URL */
      function getJson($url) {
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE);
        return $data;
      }

      /* Gets Ranked win rate and adds it to $RankedInfo */
      function getWinRate($a) {
        $winRate = intval(($a['wins'] / ($a['wins'] + $a['losses'])) * 100);
        $a['winRate'] = $winRate;
        return $a;
      }

      /* Gets Match List and returns the last 10 ranked games */
      function getMatchList($accountID) {
        global $api_key;
        $url = "https://na1.api.riotgames.com/lol/match/v3/matchlists/by-account/" . $accountID ."?api_key=" . $api_key;
        $json = file_get_contents($url);
        $data = json_decode($json, TRUE);
        $a = array();

        /* Loops through match list */
        for($i=0; $i < $data["endIndex"]; $i++) {
          /* 420 = Queue ID for Ranked */
          if($data["matches"][$i]["queue"] == 420) {
            array_push($a, $data["matches"][$i]["gameId"]);
          }
          /* Checks size of array, Limits it to 10 matches */
          if(sizeof($a) > 6){
            break;
          }
        }

        /* Checks if any Ranked games were found */
        if(sizeof($a) == 0){
          return "No ranked matches found.";
        }
        else {
          return $a;
        }
      }

      /* Generates Match URLs that need to get prased */
      function getMatchUrl($matchList) {
        global $api_key;
        $a = array();

        for($i=0; $i < sizeof($matchList); $i++){
          $url = "https://na1.api.riotgames.com/lol/match/v3/matches/" . $matchList[$i] . "?api_key=" . $api_key;
          array_push($a, $url);
        }
        return $a;
      }

      function getMatchJson($sMatchUrl) {
        $a = array();

        for($i=0; $i < sizeof($sMatchUrl); $i++) {
          $url = $sMatchUrl[$i];
          $json = file_get_contents($url);
          $data = json_decode($json, TRUE);
          array_push($a, $data);

        }
        return $a;
    }

    function getParticipantID($j, $aID) {
      /* J = Json List, $aID = account ID */
      $a = array();
      /* $i < 6 because that's the length of $j */
      for($i=0; $i < 7; $i++) {
        for($x=0; $x < 10; $x++) {
          if($j[$i]["participantIdentities"][$x]["player"]["accountId"] == $aID) {
            $partID = $j[$i]["participantIdentities"][$x]["participantId"];
            array_push($a, $partID);
          }
        }
      }
      return $a;
    }

    function getGameStats ($j, $p) {
      /* $j= $sMatchJsonList, $p = participant ID list */
      $a = array();

      for($i=0; $i < 7; $i++) {
        $partID = $p[$i] - 1;
        $kills = $j[$i]["participants"][$partID]["stats"]["kills"];
        $deaths = $j[$i]["participants"][$partID]["stats"]["deaths"];
        $assists = $j[$i]["participants"][$partID]["stats"]["assists"];

        /* KDA = (kills + assists) / deaths, but if deaths = 0, you can't divide, making KDA impossible to calculate */
        if($deaths == 0) {
          $kda = "Perfect";
        }
        else {
          $kda = ($kills + $assists) / $deaths;
        }
        $championID = $j[$i]["participants"][$partID]["championId"];
        $summonerSpellID1 = $j[$i]["participants"][$partID]["spell1Id"];
        $summonerSpellID2 = $j[$i]["participants"][$partID]["spell2Id"];

        $a[$i]["kills"] = $kills;
        $a[$i]["deaths"] = $deaths;
        $a[$i]["assists"] = $assists;
        $a[$i]["kda"] = $kda;
        $a[$i]["championID"] = $championID;
        $a[$i]["summonerSpell1"] = $summonerSpellID1;
        $a[$i]["summonerSpell2"] = $summonerSpellID2;

      }
      return $a;
    }

    function getChampionIcons($s, $api) {
      /* $s = Stats Array */
      $a = array ();
      $url = "https://na1.api.riotgames.com/lol/static-data/v3/champions?dataById=true&api_key=" . $api;
      $json = file_get_contents($url);
      $data = json_decode($json, TRUE);

      for($i=0; $i <= sizeof($s); $i++){
        foreach ($data["data"] as $champion) {
          if($s[$i]["championID"] == $champion["id"]) {
            $a[$i] = "datadragon/7.10.1/img/champion/" . $champion["key"] . ".png";
            break;
          }
        }
      }
      return $a;
    }

    ?>

    <?php /*Main Code */

    /* Global */

    $api_key = "RGAPI-53ead5ad-7174-4f7a-bb1d-e463d01c1b19";
    $sumName = $_POST['userName'];
    $sumName = strtolower($sumName);

    /* accountID, profileID, name, level, profileIcon */
    $url = "https://na1.api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $sumName . "?api_key=" . $api_key;
    $initSummonerData = getJson($url);
    $profileIcon = "datadragon/7.10.1/img/profileicon/" . $initSummonerData["profileIconId"] . ".png";
    $name = $initSummonerData["name"];
    $summonerID = $initSummonerData["id"];
    $accountID = $initSummonerData["accountId"];
    $summonerLevel = $initSummonerData["summonerLevel"];

    /* Ranked Info */
    $url ="https://na1.api.riotgames.com/lol/league/v3/positions/by-summoner/" . $summonerID . "?api_key=" . $api_key;
    $rankedJson = getJson($url);
    $rankedInfo = array(
      'tier' => $rankedJson[0]['tier'],
      'rank' => $rankedJson[0]['rank'],
      'leaguePoints' => $rankedJson[0]['leaguePoints'],
      'rankedIcon' => "datadragon/7.10.1/img/tier-icons/tier-icons/" . strtolower($rankedJson[0]['tier']) . "_" . strtolower($rankedJson[0]['rank']) . ".png",
      'wins' => $rankedJson[0]['wins'],
      'losses' => $rankedJson[0]['losses'],
      'winRate' => floor(($rankedJson[0]['wins'] / ($rankedJson[0]['wins'] + $rankedJson[0]['losses'])) * 100)
    );
    echo $rankedInfo['wins'];

    /* Matches List */
    $matchList = getMatchList($accountID);
    /* Specific Match List */
    $sMatchUrl = getMatchUrl($matchList);
    $sMatchJsonList = getMatchJson($sMatchUrl);
    $participantIDList = getParticipantID($sMatchJsonList, $accountID);
    $stats = getGameStats($sMatchJsonList, $participantIDList);
    $championIcons = getChampionIcons($stats, $api_key);
    ?>

    <!-- Nav bar -->
    <nav>
      <div class="container">


      <div class="logo">
        League Home
      </div>
      <div class="nav-search">
        <form>
          <input type="text" placeholder="Summoner Name" />
          <button>Submit</button>
        </form>
      </div>
      <ul>
        <li><a href="#">Match History</a></li>
        <li><a href="#">Leaderboards</a></li>
        <li><a href="#">Patch Notes</a></li>
      </ul>
    </nav>
  </div>
    <!-- Profile Header -->
    <div class="container">
      <div class="profile-block">
          <img class="profile-icon" src=<?php echo $profileIcon; ?> />
          <h1><?php echo $name; ?></h1>
          <div class="level-container">
            <p class="level"><?php echo "Level: " . $summonerLevel; ?></p>
          </div>
      </div>

      <div class="ranked-block">
              <p class="soloq">Solo Queue</p>
              <img src=<?php echo $rankedInfo['rankedIcon']; ?> />
              <div class="ranked-info">
              <p class="rank-tier"><?php echo $rankedInfo['tier'] . " " . $rankedInfo['rank']; ?></p>
              <p class="league-points"><?php echo $rankedInfo['leaguePoints'] . " LP"; ?></p>
              <p class="win-loss"><?php echo $rankedInfo['wins'] . " W / " . $rankedInfo['losses'] . " L";?></p>
              <p class="win-rate"><?php echo $rankedInfo['winRate'] . "%"; ?></p>
            </div>
     </div>

     <div class="match-history-container">
       <div class="match-history-block">
         <img class="champion-icon" src=<?php echo $championIcons[0]; ?> />
         <p>Kills: <?php echo $stats[0]["kills"]; ?></p>
       </div>
     </div>

      </div>

      <!-- Chart(s) -->
  </body>
</html>
