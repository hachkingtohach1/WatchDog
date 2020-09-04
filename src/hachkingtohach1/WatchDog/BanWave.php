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

use pocketmine\utils\TextFormat;
use pocketmine\scheduler\Task;
use pocketmine\command\ConsoleCommandSender;
use hachkingtohach1\watchshit\WatchShit;

class BanWave extends Task{

    private $timeCount = 0;
	
	public function __construct(){}

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick){
	    if(WatchDog::getInstance()->banwave["ENABLE"] === true){
	        if(WatchDog::getInstance()->banwave["TIME"] === 0){
                foreach(WatchDog::getInstance()->getServer()->getOnlinePlayers() as $player){
                    if(!empty(WatchDog::getInstance()->banwave["PLAYERS"][strtolower($player->getName())])) {
                        WatchDog::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), "ban " . $player->getName() . " Banwave");
                    }
                }
            }
	        else {
                if($this->timeCount === 0){
                    $this->timeCount = microtime(true);
                }
                if(microtime(true) - $this->timeCount > 100){
                    foreach(WatchDog::getInstance()->getServer()->getOnlinePlayers() as $player){
                        $player->sendMessage(TextFormat::RED."Banwave in ".WatchDog::getInstance()->banwave["TIME"]." second(s)!");
                    }
                    $this->timeCount = microtime(true);
                }
                WatchDog::getInstance()->banwave["TIME"]--;
            }
        }
	}
}
