<?php

/**
 * Copyright 2020-2021 hachkingtohach1
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types = 1);

namespace hachkingtohach1\WatchDog;

use pocketmine\Player;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerJoinEvent;

class WatchDog extends PluginBase implements Listener {

    public $timeBanWave = 3600;

    /**
     * @var array
     */
    public $banwave = [];

    /**
     * @var array
     */
	private $tableData = [];

    /**
     * @var array
     */
    private $dataViolations = [];

    /**
     * @var array
     */
    private $reports = [];

    private $prefix = "[WATCHDOG] ";

    private static $instance = null;

    public function onLoad(): void{
        self::$instance = $this;
    }

    /**
     * @return WatchDog
     */
    public static function getInstance() : WatchDog{
        return self::$instance;
    }

    public function createTableData(array $data){
        foreach($data as $check){
            if(!is_numeric($check)){
                $this->getLogger()->warning("Data must belong to the data float as a number!");
            }
        }
        $this->tableData = $data;
    }

    /**
     * @param Player $player
     * @param string $nameData
     */
    public function addViolations(Player $player, string $nameData){
        if(!empty($this->dataViolations[strtolower($player->getName())])){
            $this->dataViolations[strtolower($player->getName())][$nameData] += 1;
        }
    }

    public function onEnable(){
        $this->banwave = [
            "ENABLE" => false,
            "PLAYERS" => [],
            "TIME" => $this->timeBanWave
        ];
        $this->getScheduler()->scheduleRepeatingTask(new BanWave(), 20);
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoinEvent(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        if(empty($this->dataViolations[strtolower($player->getName())])){
            $this->dataViolations[strtolower($player->getName())] = $this->tableData;
        }
        if(!empty($this->reports[strtolower($player->getName())])){
            foreach($this->getServer()->getOnlinePlayers() as $playerOnline){
                if($playerOnline->hasPermission("watchdog.warning")){
                    $player->sendMessage($this->prefix.TextFormat::RED.$player->getName()." has joined, you need check him!");
                }
            }
        }
    }

    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param String $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args) :bool{
		switch($cmd->getName()){
			case "watchdog": 
			case "wd":
			    if(!$sender->hasPermission("watchdog.cmd")){
					$sender->sendMessage(TextFormat::RED."You don't have permission!");
					break;
				}
			    if(!isset($args[0])){
					$sender->sendMessage(TextFormat::GREEN."/wd help");
					break;
				}
			    switch(hash("md5", $args[0])){
					case "0ba4439ee9a46d9d9f14c60f88f45f87":
					    if(!$sender->hasPermission("watchdog.cmd.check")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    if(!isset($args[1])){
							$sender->sendMessage(TextFormat::GREEN."/wd check <player>");
							break;
						}
					    $sender->setGamemode(Player::SPECTATOR);
					    $targetPlayer = $this->getServer()->getPlayer($args[1]);
						$sender->teleport($targetPlayer->getLocation());
						$sender->sendMessage(TextFormat::GREEN."Usage: /wd return - to return normal mode");
					    break;
					case "0f6969d7052da9261e31ddb6e88c136e":
					    if(!$sender->hasPermission("watchdog.cmd.remove")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    if(!isset($args[1])){
							$sender->sendMessage(TextFormat::GREEN."/wd remove <player>");
							break;
						}
						unset($this->reports[strtolower($args[1])]);
						$sender->sendMessage(TextFormat::GREEN."Player has removed in data report!");					
					    break;
					case "e70c4df10ef0983b9c8c31bd06b2a2c3":
					    if(!$sender->hasPermission("watchdog.cmd.return")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    $sender->setGamemode($this->getServer()->getDefaultGamemode());
					    $sender->teleport($this->getServer()->getDefaultLevel()->getSpawnLocation());
					    $sender->sendMessage(TextFormat::GREEN."You has returned normal mode");
					    break;
					case "e98d2f001da5678b39482efbdf5770dc":
					    if(!$sender->hasPermission("watchdog.cmd.report")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    if(!isset($args[1])){
							$sender->sendMessage(TextFormat::GREEN."/wd report <player> <module>");
							break;
						}
						if(!isset($args[2])){
							$sender->sendMessage(TextFormat::GREEN."/wd report <player> <module>");
							break;
						}
						$modules = [];
						for($arg = 3; $arg <= 100; $arg++){
							if(isset($args[$arg])){
								$modules[] = $args[$arg];
							}
						}						
						$this->reports[strtolower($args[1])][] = $modules;
						$sender->sendMessage(TextFormat::GREEN."Thanks for your Cheating report. We understand your concerns and it will be reviewed as soon as possible.");
					    break;
                    case "11ce9a2a46a2c35674ba21262d183876":
					    if(!$sender->hasPermission("watchdog.cmd.banwave")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    if(!isset($args[1])){
							$sender->sendMessage(TextFormat::GREEN."/wd banwave <true|false>");
							break;
						}
						if(!in_array($args[1], ["true", "false"])){
							$sender->sendMessage(TextFormat::GREEN."/wd banwave <true|false>");
							break;
						}
						$result = null;
						switch($args[1]){
							case "true": 
							    $result = true; 
						    break;
							case "false": 
							    $result = false; 
							break;
						}
						if(empty($this->banwave["PLAYERS"])){
							$sender->sendMessage(TextFormat::RED."Data in banwave is empty");							
							break;
						}
					    $this->banwave["ENABLE"] = $result;
                        break;
                    case "a9bc0d18a1b315771f6914b3697656c2":
					    if(!$sender->hasPermission("watchdog.cmd.addbanwave")){
					        $sender->sendMessage(TextFormat::RED."You don't have permission!");
					        break;
						}
					    if(!isset($args[1])){
							$sender->sendMessage(TextFormat::GREEN."/wd addbanwave <player> <player>,..");
							break;
						}
						for($arg = 2; $arg <= 100; $arg++){
							if(isset($args[$arg])){
								if(empty($this->banwave["PLAYERS"][strtolower($args[$arg])])){
								    $this->banwave["PLAYERS"][strtolower($args[$arg])] = strtolower($args[$arg]);
								}
							}
						}
                        break;
                    case "caf9b6b99962bf5c2264824231d7a40c":
                        if(!$sender->hasPermission("watchdog.cmd.info")){
                            $sender->sendMessage(TextFormat::RED."You don't have permission!");
                            break;
                        }
                        if(!isset($args[1])){
                            $sender->sendMessage(TextFormat::GREEN."/wd info <player>");
                            break;
                        }
                        if(!empty($this->banwave["PLAYERS"][strtolower($args[1])])){
                            foreach($this->banwave["PLAYERS"][strtolower($args[1])] as $module){
                                $sender->sendMessage("$module");
                            }
                        }
                        break;
                }
			break;
		}
		return false;
	}
}
