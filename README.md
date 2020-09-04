# WatchDog
- A particularly good tool for administrators to help prevent hacking and controls, upgrading to their anticheats

# features
- Banwave ✔
- Good handling of data ✔
- Like Hypixel? ❌
- Report ✔

# How to setup ?
- This looks like the player's violation through your anticheat
```
   WatchDog::getInstance()->createTableData([
      "killaura" => 0,
      "autoclick" => 0,
   ]);
```

- If you want more violations for players staying at the functions already available and anti-hacking
```   
   WatchDog::getInstance()->addViolations($player, "killaura");
```
# Commands
- /wd help - Show all commands watchdog
- /wd check <player> - Check hacker is hacking 
- /wd return - To return normal mode when use /wd check
- /wd remove <player> - To remove report for player
- /wd report <player> <module> - To report one player is hacking
- /wd banwave <true|false> - To start one banwave
- /wd addbanwave <player> <player>,... - To add players to the list banwave
- /wd info <player> - To show info player 


