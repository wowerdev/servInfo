<?php


$host = "127.0.0.1"; // Адрес хоста (обычно localhost )
$login = "trinity"; // Логин от БД
$pass = "trinity"; // Пароль от БД
$bd_char = "characters"; // Имя БД персонажей (обычно characters)
$bd_auth = "auth"; // Имя БД аккаунтов (обычно auth)
$port = "8085"; // Порт (обычно 8085)



?>
<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Информация о сервере</title>
  <link rel="stylesheet" href="css/main.min.css">
  <link rel="shortcut icon" href="img/favicon.png" type="image/x-icon">
</head>

<body>
  <main class="legend">
    <div class="legend__wrap">
      <aside class="legend__aside">
        <h1 class="legend__title">Информация о сервере</h1>
        <nav class="legend__nav">
          <ul class="legend__menu">
            <li class="legend__menu-item">
              <button data-type="status" class="btn legend__menu-btn">Статистика сервера</button>
            </li>
            <li class="legend__menu-item">
              <button data-type="online" class="btn legend__menu-btn">Игроки онлайн</button>
            </li>
            <li class="legend__menu-item">
              <button data-type="guild" class="btn legend__menu-btn">Гильдии сервера</button>
            </li>
            <li class="legend__menu-item">
              <button data-type="ban" class="btn legend__menu-btn">Бан лист</button>
            </li>

          </ul>
        </nav>
        <button class="btn legend__back" onclick="window.history.back();">Вернуться назад</button>
        <div class="legend__aside-img">
          <img src="img/drenei.png" class="legend__aside-img" alt="Информация о сервере" title="Информация о сервере">
        </div>
      </aside>
      <section class="legend__section">
        <h2 class="legend__subtitle"><span></span></h2>
        <?php
        $connectChar = new mysqli($host, $login, $pass, $bd_char);
        $connectAuth = new mysqli($host, $login, $pass, $bd_auth);

        if ($connectChar->connect_error || $connectChar->connect_error) {
          echo "Ошибка подключения к базе данных";
        } else {
          $status = @fsockopen($host, $port, $login, $pass, .5) ? true : false;
          if ($status) {
            $state = "<span class=\"server-status server-status--on\"></span> работает.";
          } else {
            $state = "<span class=\"server-status server-status--off\"></span> не работает.";
          }

          $sql = "SELECT max(`maxplayers`) AS `maxonline` FROM `uptime`";
          $maxOnline = mysqli_fetch_assoc($connectAuth->query($sql));

          $sql = "SELECT COUNT(*) AS `horde` FROM `characters` WHERE `race` IN (2, 5, 6, 8, 10)";
          $all_horde = mysqli_fetch_assoc($connectChar->query($sql));

          $sql = "SELECT COUNT(*) AS `alliance` FROM `characters` WHERE `race` IN (1, 3, 4, 7, 11)";
          $all_alliance = mysqli_fetch_assoc($connectChar->query($sql));

          $sql = "SELECT COUNT(*) AS `acc_count` FROM `account`";
          $acc_count = mysqli_fetch_assoc($connectAuth->query($sql));

          $sql = "SELECT max(`starttime`) AS `start_time`, `uptime` AS `uptime` FROM `uptime` WHERE `realmid` = 1";
          $uptime = mysqli_fetch_assoc($connectAuth->query($sql));


          function secondsToDHMS($seconds)
          {
            $s = (int)$seconds;
            return sprintf('%dд. %02dч. %02dмин. %02dс.', $s / 86400, $s / 3600 % 24, $s / 60 % 60, $s % 60);
          }

          $time = time() - $uptime["start_time"];
          if ($status) {
            $time = secondsToDHMS($time);
          } else {
            $time = "Сервер не работает";
          }  ?>
          <ul class="legend__list active" data-type="status">
            <li class="legend__status-block">
              <ul class="legend__status-list">
                <li class="legend__status-item">
                  <p class="legend__status-p">Состояние сервера: <?php echo  $state; ?></p>
                </li>
                <li class="legend__status-item">
                  <p class="legend__status-p">Аптайм: <span class="orange"><?php echo $time ?></span></p>
                </li>
                <li class="legend__status-item">
                  <p class="legend__status-p">Максимальный онлайн: <span class="orange"><?php echo $maxOnline["maxonline"]; ?></span> чел.</p>
                </li>
                <li class="legend__status-item">
                  <p class="legend__status-p"><img src="img/horde.png" alt="Орда">Персонажей за орду: <span class="orange"><?php echo $all_horde["horde"]; ?></span></p>
                </li>
                <li class="legend__status-item">
                  <p class="legend__status-p"><img src="img/alliance.png" alt="Альянс">Персонажей за альянс: <span class="orange"><?php echo $all_alliance["alliance"]; ?></span></p>
                </li>
                <li class="legend__status-item">
                  <p class="legend__status-p">Всего аккаунтов: <span class="orange"><?php echo $acc_count["acc_count"]; ?></span></p>
                </li>
              </ul>
            </li>
          </ul>
          <?php
          $sql = "SELECT COUNT(*) AS `cur_online` FROM `characters` WHERE `online` = 1  AND NOT `extra_flags` > 16 ";
          $cur_online = mysqli_fetch_assoc($connectChar->query($sql));

          $sql = "SELECT COUNT(*) AS `horde_online` FROM `characters` WHERE `online` = 1 AND `race` IN (2, 5, 6, 8, 10) AND NOT `extra_flags` > 16 ";
          $horde_online = mysqli_fetch_assoc($connectChar->query($sql));

          $sql = "SELECT COUNT(*) AS `alliance_online` FROM `characters` WHERE `online` = 1 AND `race` IN (1, 3, 4, 7, 11) AND NOT `extra_flags` > 16 ";
          $alliance_online = mysqli_fetch_assoc($connectChar->query($sql));

          ?>
          <ul class="legend__list active" data-type="online">
            <li class="legend__online-block">
              <ul class="legend__online-list">
                <li class="legend__online-item">
                  <p class="legend__online-p legend__online-p--all">Общий онлайн: <span class="orange"><?php echo $cur_online["cur_online"]; ?></span> чел.</p>
                </li>
                <li class="legend__online-item">
                  <p class="legend__online-p"><img src="img/alliance.png" alt="Альянс Онлайн">Альянс: <span class="orange"><?php echo $alliance_online["alliance_online"]; ?></span> чел.</p>
                </li>
                <li class="legend__online-item">
                  <p class="legend__online-p"><img src="img/horde.png" alt="Орда Онлайн">Орда: <span class="orange"><?php echo $horde_online["horde_online"]; ?></span> чел.</p>
                </li>
                <li class="legend__online-item legend__online-item--online">
                  <p class="legend__online-p legend__online-p--list"><span class="orange">Список игроков онлайн: </span></p>
                </li>
              </ul>
            </li>
            <li class="legend__item legend__item--table">
              <?php
              $sql = "SELECT `name`, `race`, `level`, `class` FROM `characters` WHERE `online` = 1  AND NOT `extra_flags` > 16  ORDER BY `level` DESC LIMIT 50";
              $res  = $connectChar->query($sql);
              $arrColorsClass = [
                ["Нет класса", "red"],
                ["Воин", "warrior"],
                ["Паладин", "paladin"],
                ["Охотник", "hunter"],
                ["Разбойник", "rogue"],
                ["Жрец", "priest"],
                ["Рыцарь смерти", "dk"],
                ["Шаман", "shaman"],
                ["Маг", "mage"],
                ["Чернокнижник", "warlock"],
                ["Друид", "druid"]
              ]; ?>
              <table class="legend__table">
                <?php
                while ($data =  $res->fetch_assoc()) {
                  if ($data["race"] == "1" || $data["race"] == "3" || $data["race"] == "4" || $data["race"] == "7" || $data["race"] == "11") {
                    $race_src = "img/alliance.png";
                  } else {
                    $race_src = "img/horde.png";
                  }
                  ?>
                  <tr class="legend__table-tr">
                    <td class="legend__table-td legend__table-td--race"><img src="<?php echo  $race_src; ?>" alt="Расса"></td>
                    <td class="legend__table-td legend__table-td--name"><?php echo $data["name"]; ?></td>
                    <td class="legend__table-td legend__table-td--class"><span class="<?php echo $arrColorsClass[$data["class"]][1]; ?>"><?php echo $arrColorsClass[$data["class"]][0]; ?></span></td>
                    <td class="legend__table-td legend__table-td--scope"><b><?php echo $data["level"] ?></b> лвл.</td>
                  </tr>
                <?php
              } ?>
              </table>
            </li>
          </ul>

          <?php
          $sql = "SELECT COUNT(*) AS `all_guild` FROM `guild`";
          $all_guild = mysqli_fetch_assoc($connectChar->query($sql));

          ?>
          <ul class="legend__list active" data-type="guild">
            <li class="legend__online-block">
              <ul class="legend__online-list">
                <li class="legend__online-item">
                  <p class="legend__online-p legend__online-p--all">Всего гильдий: <span class="orange"><?php echo $all_guild["all_guild"]; ?></span> шт.</p>
                </li>
                <li class="legend__online-item legend__online-item--online">
                  <p class="legend__online-p legend__online-p--list"><span class="orange no-margin">Популярные гильдии: </span></p>
                </li>
              </ul>
            </li>
            <li class="legend__item legend__item--table">
              <table class="legend__table">
                <thead>
                  <tr class="legend__table-tr">
                    <th class="legend__table-th">Название</th>
                    <th class="legend__table-th nomob">ГМ</th>
                    <th class="legend__table-th nomob">Дата создания</th>
                    <th class="legend__table-th legend__table-th--right">Кол-во людей</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $sql = "SELECT * FROM `guild`";
                  $res  = $connectChar->query($sql);


                  $arrGuild = [];
                  $i = 0;
                  while ($data = $res->fetch_assoc()) {

                    $guildid = $data["guildid"];
                    $sql = "SELECT COUNT(*) AS `val` FROM `guild_member` WHERE `guildid` = $guildid";
                    $count_member = mysqli_fetch_assoc($connectChar->query($sql));

                    $leader_guid = $data["leaderguid"];
                    $sql = "SELECT `name` FROM `characters` WHERE `guid` = $leader_guid";
                    $name_lider = mysqli_fetch_assoc($connectChar->query($sql));

                    array_push($arrGuild, [
                      "name" => $data["name"],
                      "lider_name" => $name_lider["name"],
                      "create_data" => date("d.m.Y", $data["createdate"]),
                      "count_member" => $count_member["val"]
                    ]);
                  } ?>

                  <?php

                  $sortByMember = array_column($arrGuild, 'count_member');
                  array_multisort($sortByMember, SORT_DESC, $arrGuild);

                  if (count($arrGuild) > 10) {
                    $count = 10;
                  } else {
                    $count = count($arrGuild);
                  }

                  for ($i = 0; $i <  $count; $i++) { ?>
                    <tr class="legend__table-tr">
                      <td class="legend__table-td legend__table-td--name"><span class="orange"><?php echo $arrGuild[$i]["name"] ?></span></td>
                      <td class="legend__table-td legend__table-td--name nomob"><span class="orange"><?php echo $arrGuild[$i]["lider_name"] ?></span></td>
                      <td class="legend__table-td legend__table-td--name nomob"><span class="orange"><?php echo $arrGuild[$i]["create_data"] ?></span></td>
                      <td class="legend__table-td legend__table-td--scope"><b><?php echo $arrGuild[$i]["count_member"] ?></b> чел.</td>
                    </tr>
                  <?php     } ?>

                </tbody>

              </table>

            </li>
          </ul>
          <?php

          $sql = "SELECT * FROM `account_banned` ORDER BY `bandate` DESC LIMIT 25";
          $res  = $connectAuth->query($sql);
          ?>
          <ul class="legend__list active" data-type="ban">
            <li class="legend__item legend__item--table">
              <table class="legend__table">
                <thead>
                  <tr class="legend__table-tr">
                    <th class="legend__table-th">Аккаунт</th>
                    <th class="legend__table-th">Дата бана</th>
                    <th class="legend__table-th">Дата разбана</th>
                    <th class="legend__table-th legend__table-th--right nomob">Забанил</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  while ($data = $res->fetch_assoc()) {
                    $acc_id = $data["id"];
                    $sql = "SELECT `username` FROM `account` WHERE `id` = $acc_id";
                    $name_acc = mysqli_fetch_assoc($connectAuth->query($sql));
                    if ($data["bandate"] == $data["unbandate"]) {
                      $unban = "Никогда";
                    } else {
                      $unban =  date("d.m.Y", $data["unbandate"]);
                    }

                    ?>
                    <tr class="legend__table-tr">
                      <td class="legend__table-td legend__table-td--name"><span class="orange"><?php echo ucfirst(strtolower($name_acc["username"])); ?></span></td>
                      <td class="legend__table-td legend__table-td--name"><span class="orange"><?php echo date("d.m.Y", $data["bandate"]) ?></span></td>
                      <td class="legend__table-td legend__table-td--name"><span class="orange"><?php echo $unban ?></span></td>
                      <td class="legend__table-td legend__table-td--scope nomob"><span class="blue"><?php echo $data["bannedby"] ?></span></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </li>
          </ul>
          <a href="https://it-portfolio.ru/" style="display: none;">Разработка сайтов</a>
        <?php } ?>
      </section>
    </div>
  </main>
  <script src="js/scripts.min.js"></script>
  <?php
  mysqli_close($connectChar);
  mysqli_close($connectAuth);
  ?>
</body>

</html>