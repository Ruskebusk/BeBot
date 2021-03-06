<?php
/*
* Raid.php - Announces a raid.
*
* BeBot - An Anarchy Online & Age of Conan Chat Automaton
* Copyright (C) 2004 Jonas Jax
* Copyright (C) 2005-2012 J-Soft and the BeBot development team.
*
* Developed by:
* - Alreadythere (RK2)
* - Blondengy (RK1)
* - Blueeagl3 (RK1)
* - Glarawyn (RK1)
* - Khalem (RK1)
* - Naturalistic (RK1)
* - Temar (RK1)
*
* See Credits file for all acknowledgements.
*
*  This program is free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; version 2 of the License only.
*
*  This program is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  You should have received a copy of the GNU General Public License
*  along with this program; if not, write to the Free Software
*  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
*  USA
*/
$raid = new Raid($bot);
/*
The Class itself...
*/
class Raid extends BaseActiveModule
{
    var $raid;
    var $user;
	var $user2;
    var $announce;
	var $announcel = 0;
    var $start;
    var $locked;
    var $paused = false;
	var $description;
	var $tank;
	var $move = 0;
	var $showtank = false;
	var $showcallers = false;
	var $type;


    function __construct(&$bot)
    {
        parent::__construct($bot, get_class($this));
        $this->raid = false;
        $this->user = array();
		$this->user2 = array();
        $this->announce = 0;
        $this->locked = false;
        $this->register_command("all", "c", "LEADER");
        $this->register_command("all", "raid", "GUEST");
        if (strtolower($this->bot->game) == 'ao') {
            $this->register_event("pgleave");
            $this->register_event("pgjoin");
            $this->register_event("buddy");
        }
        //$this -> register_event("connect");
        $this->register_event("logon_notify");
        $this->register_module("raid");
        if (strtolower($this->bot->game) == 'ao') {
            $this->bot->core("settings")
                ->create(
                    "Raid",
                    "Remonleave",
                    true,
                    "Automatically remove players from the raid if they leave <botname>'s channel?",
                    "On;Off",
                    false,
                    15
                );
            $this->bot->core("settings")
                ->create(
                    "Raid",
                    "AddOnRejoin",
                    true,
                    "Automatically add players to the raid if they where in the raid but left and rejoin <botname>'s channel?",
                    "On;Off",
                    false,
                    15
                );
        }
        $this->bot->core("settings")
            ->create(
                "Raid",
                "Command",
                "LEADER",
                "Who should be able to access the higher level raid commands (all commands except join/leave)?",
                "ADMIN;LEADER;MEMBER;GUEST;ANONYMOUS"
            );
        $this->bot->core("settings")
            ->create(
                "Raid",
                "Cformat",
                "Raid Command from ##highlight####name####end##: ##msg##",
                "How Should the Raid Command be Output, Use ##name## and ##msg## to place name and message where you want. also ##nl## for new line"
            );
        $this->bot->core("settings")
            ->create("Raid", "Points", 0.1, "How Many points should a User get Every minuite while in Raid");
        $this->bot->core("settings")
            ->create("Raid", "minlevel", 1, "Whats the Default min level to join Raid.");
        $this->bot->core("settings")
            ->create("Raid", "showtank", false, "Whats the Default for Show Tank.");
        $this->bot->core("settings")
            ->create("Raid", "showcallers", false, "Whats the Default for Show Callers.");
        $this->bot->core("settings")
            ->create("Raid", "raidinfo", "", "Raid info.", null, true, 2);
        $this->bot->core("settings")
            ->create("Raid", "showlft", true, "show LFT link next to raid join");
        if (strtolower($this->bot->game) == 'ao') {
            $this->bot->core("settings")
                ->create("Raid", "inPG", true, "Do users have to be in the PG to join a Raid?");
        }
        $this->bot->core("settings")
            ->create(
                "Raid",
                "AnnounceDelay",
                120,
                "Specify the delay between raid announces.",
                '60;120;180;340;400;600;900'
            );

        $this->help['description'] = 'Module to manage and announce raids.';
        $this->help['command']['raid start <description>'] = "Starts a raid with optional description.";
        $this->help['command']['raid end'] = "Ends a raid.";
        $this->help['command']['raid join'] = "Join the active raid.";
        $this->help['command']['raid leave'] = "Leave the active raid.";
        $this->help['command']['raid reward <points>'] = "Reward <points> to all raiders.";
        $this->help['command']['raid punish <points>'] = "Remove <points> to from all raiders.";
        $this->help['command']['raid [lock/unlock]'] = "Locks or Unlocks the active raid.";
        $this->help['command']['raid add <name>'] = "Adds player <name> to the current raid, even if the raid is locked.";
        $this->help['command']['raid kick <name>'] = "Kicks player <name> from the current raid.";
        $this->help['command']['raid check'] = "Generates a list of active raiders with assist links in a window for attendance checking.";
        $this->help['command']['raid check <text>']
            = "put a copy and paste of the results of a raid check in <text> to have bot output a missing notice and to get a short list with kick links.";
        $this->help['command']['raid notin'] = "Sent tells to all user in privgroup saying they arnt in raid if they arnt.";
        $this->help['command']['raid notinkick'] = "Kicks all user in privgroup who arnt in raid.";
        $this->help['command']['raid list'] = "list all user who are or where in the raid and there status.";
        $this->help['command']['c <message>'] = "Raid command. Display <message> in a highly visiable manner.";
        $this->help['notes'] = "All commands except join and leave are restricted to users with " . $this->bot
                ->core("settings")->get('Raid', 'Command') . " or higher access.";
        $this->bot->db->query(
            "CREATE TABLE IF NOT EXISTS " . $this->bot->db->define_tablename("raid_log", "true") . "
				(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				name VARCHAR(20),
				points decimal(11,2) default '0.00',
				time INT default '0',
				end INT default '0',
				UNIQUE (name, time))"
        );
        $this->restart_raid();
    }


    function command_handler($name, $msg, $type)
    {
        $var = explode(" ", $msg, 2);
		if(!isset($var[1])) { $var[1]=""; }
        switch (strtolower($var[0])) {
            case 'c':
                $this->raid_command($name, $var[1]);
                Break;
            case 'raid':
                $var = explode(" ", $msg, 4);
				if(!isset($var[1])) { $var[1]=""; }
				if(!isset($var[2])) { $var[2]=""; }
				if(!isset($var[3])) { $var[3]=""; }
                switch (strtolower($var[1])) {
                    case 'start':
                        Return $this->start_raid($name, $var[2], $var[3]);
                    case 'stop':
                    case 'end':
                        Return $this->end_raid($name);
                    case 'join':
                        return $this->join_raid($name);
                    case 'leave':
                        $return = $this->leave_raid($name);
                        if ($type == "tell") {
                            Return $return;
                        }
                        Break;
                    case 'kick':
                        Return $this->kick_raid($name, $var[2], $var[3], $origin);
                    case 'check':
                        if (!empty($var[3])) {
                            $desc = $var[2] . " " . $var[3];
                        } else {
                            $desc = $var[2];
                        }
                        Return $this->check_raid($name, $desc);
                    case 'lock':
                    case 'unlock':
                        $return = $this->lock_raid($name, $var[1]);
                        if ($type == "tell") {
                            Return $return;
                        }
                        Break;
                    case 'add':
                        return $this->addto_raid($name, $var[2], $type);
                    case 'reward':
                    case 'give':
                        $this->add_point($name, $var[2], $var[3]);
                        Break;
                    case 'punish':
                    case 'take':
                        $this->rem_point($name, $var[2], $var[3]);
                        Break;
                    case 'pause':
                        Return $this->pause(true);
                    case 'unpause':
                        Return $this->pause(false);
                    case 'announce':
                        Return $this->set_announce($name, $var[2]);
                    case 'description':
                        if (!empty($var[3])) {
                            $desc = $var[2] . " " . $var[3];
                        } else {
                            $desc = $var[2];
                        }
                        Return $this->set_description($name, $desc);
                    case 'level':
                    case 'minlevel':
                        Return $this->change_level($name, $var[2]);
                    case 'list':
                        Return $this->list_users($name);
                    case 'tell':
                    case 'notin':
                        Return $this->notin($name);
                    case 'notinkick':
                        Return $this->notinkick($name);
                    case 'move':
                        Return $this->move($name, $var[2]);
                    case 'tank':
                        if (!empty($var[3])) {
                            $tank = $var[2] . " " . $var[3];
                        } else {
                            $tank = $var[2];
                        }
                        Return $this->set_tank($name, $tank);
                    case 'showtank':
                        Return $this->show_tank($name, $var[2]);
                    case 'showcallers':
                        Return $this->show_callers($name, $var[2]);
                    case 'note':
                        if (!empty($var[3])) {
                            $this->note = $var[2] . " " . $var[3];
                        } else {
                            $this->note = $var[2];
                        }
                        Return ("Note for Raid set to ##highlight##" . $this->note . "##end##");
                    Default:
                        if ($this->bot->core("security")
                            ->check_access(
                                $name,
                                $this->bot->core("settings")
                                    ->get('Raid', 'Command')
                            )
                        ) {
                            Return $this->control();
                        } else {
                            if (!$this->raid) {
                                return "No raid in progress";
                            } else {
                                if ($this->move > time()) {
                                    $move = $this->move - time();
                                    $move = ", Move in ##highlight##" . $this->bot
                                            ->core("time")
                                            ->format_seconds($move) . " ##end##";
                                }
                                return ucfirst(
                                    $this->type
                                ) . " Raid is running: ##highlight##" . $this->description . "##end##" . $move . " :: " . $this->clickjoin(
                                );
                            }
                        }
                }
                Break;
            Default:
                Return "##error##Error : Broken plugin, Raid.php received unhandled command: " . $var[0] . "##end##";
        }
    }


    /*
    This gets called on cron
    */
    function connect()
    {
        $this->bot->db->query("UPDATE #___raid_points SET raiding = 0");
    }


    function restart_raid()
    {
        $raiding = $this->bot->db->select("SELECT nickname, raidingas FROM #___raid_points WHERE raiding = 1");
        if (!empty($raiding)) {
            $info = $this->bot->core("settings")->get("Raid", "raidinfo");
            if ($info == "false") {
                return;
            }
            $info = explode(";", $info, 6);
            $this->description = $info[5];
            $this->raid = true;
            $this->name = $info[0];
            $this->minlevel = $info[4];
            $this->announce = (bool)$info[2];
            $this->locked = (bool)$info[3];
            $this->paused = true;
            $this->start = $info[1];
            $this->register_event("cron", "1min");
            echo "Raid Restarted for " . $info[0] . "\n";
            foreach ($raiding as $raider) {
                $this->user2[$raider[1]] = "Bot Restart";
            }
        }
    }


    /*
    This gets called if someone leaves the privgroup
    */
    function pgleave($name)
    {
        if ($this->bot->core("settings")->get("Raid", "Remonleave")) {
            if (isset($this->user[$name])) {
                unset($this->user[$name]);
                $this->user2[$name] = "Left PrivGroup";
                $this->pgleave[$name] = time();
                $this->bot->db->query("UPDATE #___raid_points SET raiding = 0 WHERE id = " . $this->points_to($name));
                $this->bot->send_output("", "##highlight##$name##end## was removed from the raid.", "both");
            }
        }
    }


    function pgjoin($name)
    {
        if ($this->bot->core("settings")
                ->get("Raid", "Remonleave")
            && $this->bot->core("settings")
                ->get("Raid", "AddOnRejoin")
        ) {
            if (isset($this->user2[$name]) && ($this->user2[$name] == "Left PrivGroup" || $this->user2[$name] == "Bot Restart")) {
                if (empty($this->user)) {
                    $this->bot->db->query("UPDATE #___raid_points SET raiding = 0");
                }
                $this->user[$name] = $this->bot->core('player')->id($name);
                $this->bot->db->query(
                    "UPDATE #___raid_points SET raiding = 1, raidingas = '" . $name . "' WHERE id = " . $this->points_to(
                        $name
                    )
                );
                $this->bot->send_output("", "##highlight##$name##end## has Rejoined the raid.", "both");
            }
        }
    }


    function notify($name, $startup = false)
    {
        if (!$startup && $this->raid && !$this->locked) {
            if ($this->move > time()) {
                $move = $this->move - time();
                $move = ", Move in ##highlight##" . $this->bot->core("time")
                        ->format_seconds($move) . " ##end##";
            }
            $who = $this->bot->core("whois")->lookup($name);
            if ($who['level'] < $this->minlevel) {
                Return;
            }
            $this->bot->send_tell(
                $name,
                "Raid is running: ##highlight##" . $this->description . "##end##" . $move . " :: " . $this->clickjoin(
                    true
                )
            );
        }
    }


    function buddy($name, $status)
    {
        if ($this->raid && $status == 1 && isset($this->pgleave[$name]) && $this->pgleave[$name] > (time() - (60 * 5))
        ) {
            $this->bot->send_tell($name, "You have been Invited because you appear to have LD.");
            $this->bot->core("chat")->pgroup_invite($name);
            unset($this->pgleave[$name]);
        }
    }


    /*
    Starts a Raid
    */
    function start_raid($name, $desc)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if (!$this->raid) {
                $this->description = $desc;
                $this->announce = true;
                $this->minlevel = $this->bot->core("settings")
                    ->get("Raid", "minlevel");
                $this->name = $name;
                $this->raid = true;
                $this->locked = false;
                $this->move = false;
                $this->user2 = array();
                $this->points = array();
                $this->note = "";
                $this->tank = false;
                $this->showtank = $this->bot->core("settings")
                    ->get("Raid", "showtank");
                $this->showcallers = $this->bot->core("settings")
                    ->get("Raid", "showcallers");
                $this->pgleave = array();
                $this->start = time();
                $this->bot->send_output(
                    $name,
                    "##highlight##$name##end## has started the raid :: " . $this->clickjoin(),
                    "both"
                );
                $this->pause(true);
                $this->save();
                $this->register_event("cron", "1min");
                return "Raid started. :: " . $this->control();
                if (isset($this->bot->commands["tell"]["raidhistory"])) {
                    $this->bot->db->query(
                        "INSERT INTO #___raid_details (name, description, start) VALUES ('$name', '" . mysqli_real_escape_string($this->bot->db->CONN,
                            $desc
                        ) . "', " . time() . ")"
                    );
                }
            } else {
                return "Raid already running.";
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to start a raid";
        }
    }


    /*
    Ends a Raid
    */
    function end_raid($name)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $this->bot->db->query("UPDATE #___raid_log SET end = " . time() . " WHERE time = " . $this->start);
                $this->raid = false;
                $this->user = array();
                $this->move = false;
                $this->announce = false;
                $this->user2 = array();
                $this->unregister_event("cron", "1min");
                $this->bot->send_output($name, "##highlight##$name##end## has stopped the raid.", "both");
                $this->bot->db->query("UPDATE #___raid_points SET raiding = 0");
                $this->locked = false;
                $this->bot->core("settings")->save("Raid", "raidinfo", "false");
                if (isset($this->bot->commands["tell"]["raidhistory"])) {
                    $this->bot->db->query(
                        "UPDATE #___raid_details SET end = " . time(
                        ) . ", description = '" . $this->description . "', note = '" . $this->note . "' WHERE start = " . $this->start
                    );
                }
                Return "Raid stopped. :: " . $this->control();
            } else {
                return "No raid running.";
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to do this";
        }
    }


    /*
    Issues a raid command
    */
    function raid_command($name, $command)
    {
        $msg = $this->bot->core("settings")->get('Raid', 'Cformat');
        $msg = str_replace("##name##", $name, $msg);
        $msg = str_replace("##msg##", $command, $msg);
        $msg = str_replace("##nl##", "\n", $msg);
        $this->bot->send_output($name, $msg, "both");
    }


    /*
    Adds a point to all raiders
    */
    function add_point($name, $points)
    { //fix me! - fixed addto_raid so that raiders are added correctly
        if (!$this->raid) {
            $this->bot->send_tell($name, "No raid in progress");
        }
        if (!is_numeric($points)) {
            $this->bot->send_tell($name, "Invalid Points Amount");
        } elseif ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            $users = $this->bot->db->select(
                "SELECT raidingas FROM #___raid_points WHERE raiding = 1 ORDER BY raidingas"
            );
            if (!empty($users)) {
                $inside = " :: $points Given to all Raiders ::\n\n";
				$count=0;
                foreach ($users as $user) {
                    $count++;
                    $user = $user[0];
                    if(isset($this->points[$user])) $this->points[$user] += $points;
					else $this->points[$user] = $points;
                    $userp = isset($this->points[$user]) ? $this->points[$user] : 0;
                    $inside .= "##highlight##" . $user . "##end##: ##highlight##" . $userp . "##end## points\n";
                    if (isset($this->bot->commands["tell"]["raidhistory"])) {
                        $this->bot->db->query(
                            "INSERT INTO #___raid_log (name, points, time) VALUES ('" . $user . "', $userp, " . $this->start . ") ON DUPLICATE KEY UPDATE points = points + "
                            . $points
                        );
                    }

                }
                $this->bot->send_output(
                    "",
                    "##highlight##$points##end## points have been added to all raiders (##highlight##$count##end##) :: " . $this->bot
                        ->core("tools")
                        ->make_blob("click to view", $inside),
                    "both"
                );
            }
            $this->bot->db->query("UPDATE #___raid_points SET points = points + " . $points . " WHERE raiding = 1");
        } else {
            $this->bot->send_tell(
                $name,
                "You must be a " . $this->bot
                    ->core("settings")->get('Raid', 'Command') . " to do this"
            );
        }
    }


    function rem_point($name, $points, $type = false)
    { //fix me! - fixed addto_raid so that raiders are added correctly
        if (!$this->raid) {
            $this->bot->send_tell($name, "No raid in progress");
        }
        if (!is_numeric($points) && !is_numeric($type)) {
            $this->bot->send_tell($name, "Invalid Points Amount");
        } elseif ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if (!is_numeric($points)) {
                $temp = $type;
                $type = $points;
                $points = $temp;
            }
            $type = strtolower($type);
            if ($type == "z" || $type == "zod" || $type == "zods") {
                $type = "zods";
            } elseif ($type == "b" || $type == "beast") {
                $type = "beast";
            } else {
                $type = $this->type;
            }
            $users = $this->bot->db->select(
                "SELECT raidingas FROM #___raid_points WHERE raiding = 1 ORDER BY raidingas"
            );
            if (!empty($users)) {
                $inside = " :: $points Taken from all Raiders ::\n\n";
				$count = 0;
                foreach ($users as $user) {
                    $count++;
                    $user = $user[0];
                    if(isset($this->points[$user])) $this->points[$user] -= $points;
					else $this->points[$user] = $points;
                    $userp = isset($this->points[$user]) ? $this->points[$user] : 0;
                    $inside .= "##highlight##" . $user . "##end##: ##highlight##" . $userp . "##end## points\n";
                    if (isset($this->bot->commands["tell"]["raidhistory"])) {
                        $this->bot->db->query(
                            "INSERT INTO #___raid_log (name, points, time) VALUES ('" . $user . "', $userp, " . $this->start . ") ON DUPLICATE KEY UPDATE points = points - "
                            . $points
                        );
                    }

                }
                $this->bot->send_output(
                    "",
                    "##highlight##$points##end## points have been removed from all raiders (##highlight##$count##end##) :: " . $this->bot
                        ->core("tools")
                        ->make_blob("click to view", $inside),
                    "both"
                );
            }
            $this->bot->db->query("UPDATE #___raid_points SET points = points - " . $points . " WHERE raiding = 1");
        } else {
            $this->bot->send_tell($name, "You must be a raidleader to do this");
        }
    }


    /*
    Adds a player to Raid
    */
    function addto_raid($name, $player, $source)
    {
        $player = ucfirst(strtolower($player));
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if (!$this->raid) {
                return "No raid in progress";
            } else {
                if (isset($this->user[$player])) {
                    return $player . " is already in the raid";
                }
            }
            $uid = $this->bot->core('player')->id($player);
            if (!$uid) {
                return "Player ##highlight##$player##end## does not exist.";
            } else {
                if (empty($this->user)) {
                    $this->bot->db->query("UPDATE #___raid_points SET raiding = 0");
                }
                if (!$this->bot->core("online")->in_chat($name)) {
                    $this->bot->send_tell(
                        $name,
                        "##error##Warning: ##highlight##$player##end## is not in the PrivGroup of ##highlight##<botname>##end####end##"
                    );
                }
                $this->bot->db->query(
                    "INSERT INTO #___raid_points (id, nickname, points, raiding, raidingas) VALUES (" . $this->points_to(
                        $player
                    ) . ", '" . $this->points_to_name($player)
                    . "', 0, 1, '" . $player . "') ON DUPLICATE KEY UPDATE raiding = 1, raidingas = '" . $player . "'"
                );
                $this->bot->db->query(
                    "INSERT INTO #___raid_log (name, points, time) VALUES ('" . $player . "', 0, " . $this->start . ") ON DUPLICATE KEY UPDATE name = '" . $player . "'"
                ); //update is just so no error
                //Update last_raid
                $query = "UPDATE #___users SET last_raid = " . time() . " WHERE nickname = '$player'";
                $this->bot->db->query($query);
                $this->user[$player] = $uid;
                $this->user2[$player] = "Joined";
                $this->bot->send_tell($player, "##highlight##$name##end## added you to the raid.");
                if (!$this->locked) {
                    $ctj = " :: " . $this->clickjoin();
                }
                $this->bot->send_output(
                    "",
                    "##highlight##$player##end## was ##highlight##added##end## to the raid by ##highlight##$name##end##" . $ctj,
                    "both"
                );
                if ($source == "tell") {
                    return "##highlight##$player##end## has been ##highlight##added##end## to the raid";
                }
            }
        } elseif ($name == $player) {
            Return $this->join_raid($name);
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to do this";
        }
    }


    /*
    Joins a Raid
    */
    function join_raid($name)
    {
        if (empty($this->user)) {
            $this->bot->db->query("UPDATE #___raid_points SET raiding = 0");
        }
        $minlevel = $this->minlevel;
        $who = $this->bot->core("whois")->lookup($name, true);
        if (isset($this->user[$name])) {
            return "You are already in the raid";
        } elseif ($who["level"] < $minlevel) {
            return "This raid is ##highlight##$minlevel+##end##";
        } elseif ($this->locked) {
            return "The raid status is currently ##highlight##locked##end##.";
        } elseif (strtolower($this->bot->game) == 'ao'
            && $this->bot->core("settings")
                ->get('Raid', 'inpg')
            && !$this->bot->core("online")->in_chat($name)
        ) {
            return "You must be in the PrivGroup of ##highlight##<botname>##end## to join a Raid.";
        } else {
            if ($this->raid) {
                $this->bot->db->query(
                    "INSERT INTO #___raid_points (id, nickname, points, raiding, raidingas) VALUES (" . $this->points_to(
                        $name
                    ) . ", '" . $this->points_to_name($name)
                    . "', 0, 1, '"
                    . $name . "') ON DUPLICATE KEY UPDATE raiding = 1, raidingas = '" . $name . "'"
                );
                $this->bot->db->query(
                    "INSERT INTO #___raid_log (name, points, time) VALUES ('" . $name . "', 0, " . $this->start . ") ON DUPLICATE KEY UPDATE name = '" . $name . "'"
                ); //update is just so no error
                //Update last_raid
                $query = "UPDATE #___users SET last_raid = " . time() . " WHERE nickname = '$name'";
                $this->bot->db->query($query);
                $this->user[$name] = $this->bot->core('player')->id($name);
                $this->user2[$name] = "Joined";
                $this->bot->send_output(
                    "",
                    "##highlight##$name##end## has ##highlight##joined##end## the raid :: " . $this->clickjoin(),
                    "both"
                );
                $this->bot->send_tell($name, "you have joined the Raid");
                return false;
            } else {
                return "No raid in progress";
            }
        }
    }


    /*
    Leaves a Raid
    */
    function leave_raid($name)
    {
		$altinraid = false;
        if (!isset($this->user[$name])) {
            return "You are not in the raid.";
        } else {
            unset($this->user[$name]);
            $this->user2[$name] = "Left";
            if ($this->bot->core("settings")->get("Points", "To_main")) {
                $main = $this->bot->core("alts")->main($name);
                $alts = $this->bot->core("alts")->get_alts($main);
                $alts[] = $main;
                foreach ($alts as $alt) {
                    if (isset($this->user[$alt])) {
                        $altinraid = true;
                    }
                }
            }
            if (!$altinraid) {
                $this->bot->db->query("UPDATE #___raid_points SET raiding = 0 WHERE id = " . $this->points_to($name));
            }

            if (!$this->locked) {
                $ctj = " :: " . $this->clickjoin();
            }
            $this->bot->send_output(
                "",
                "##highlight##$name##end## has ##highlight##left##end## the raid" . $ctj,
                "both"
            );
            return "You have ##highlight##left##end## the raid.";
        }
    }


    /*
    Kicks someone from the raid
    */
    function kick_raid($name, $who, $why, $origin)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            $who = ucfirst(strtolower($who));
            if (!isset($this->user[$who]) && isset($this->user2[$who])) {
                if ($this->user2[$who] == "Left PrivGroup" || $this->user2[$who] == "Bot Restart") {
                    if (!empty($why)) {
                        $why = " (" . $why . ")";
                    }
                    $this->user2[$who] = "Removed from Rejoin by " . $name . $why;
                    return "##highlight##$who##end## has been removed from Rejoin List.";
                } else {
                    return "##highlight##$who##end## is not on the Rejoin List.";
                }
            } elseif (!isset($this->user[$who])) {
                return "##highlight##$who##end## is not in the raid.";
            } else {
                unset($this->user[$who]);
                if (!empty($why)) {
                    $why = " (" . $why . ")";
                }
                $this->user2[$who] = "Kicked by " . $name . $why;
                if ($this->bot->core("settings")->get("Points", "To_main")) {
                    $main = $this->bot->core("alts")->main($who);
                    $alts = $this->bot->core("alts")->get_alts($main);
                    $alts[] = $main;
                    foreach ($alts as $alt) {
                        if (isset($this->user[$alt])) {
                            $altinraid = true;
                        }
                    }
                }
                if (!$altinraid) {
                    $this->bot->db->query(
                        "UPDATE #___raid_points SET raiding = 0 WHERE id = " . $this->points_to($who)
                    );
                }
                $this->bot->send_output(
                    "",
                    "##highlight##$who##end## has been ##highlight##Kicked##end## from the raid by ##highlight##$name##end##$why",
                    "both"
                );
                $this->bot->send_tell($who, "##highlight##$name##end## kicked you from the raid.");
                if ($origin == "tell") {
                    Return "##highlight##$who##end## was kicked from the raid.";
                } else {
                    Return false;
                }
            }
        } elseif ($name == $player) {
            Return $this->leave_raid($name);
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to do this";
        }
    }


    /*
    Checks memebers on a raid
    */
    function check_raid($name, $names)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if (!empty($names)) {
                if (preg_match_all("/Can\'t find target &gt;([A-Za-z0-9]+)&lt;\./", $names, $missing)) {
                    $list = "Users Not with Raid Group: ";
                    foreach ($missing[1] as $player) {
                        $inside .= $player . " [" . $this->bot->core("tools")
                                ->chatcmd("raid kick " . $player . " Not with Group", "Kick") . "]\n";
                        $list .= $player . ", ";
                        $count++;
                    }
                    $list = substr($list, 0, -2);
                    $this->raid_command($name, $list);
                    Return ("##highlight##" . $count . "##end## Players Missing :: " . $this->bot
                            ->core("tools")->make_blob("click to view", $inside));
                } else {
                    Return ("##highlight##0##end## Players Missing");
                }
            } else {
                $players = array_keys($this->user);
                sort($players);
                $inside = "##blob_title##:::: People in the raid ::::##end##\n\n";

                $inside .= "Send not-joined warnings: " . $this->bot
                        ->core("tools")->chatcmd("raid notin", "raid notin") . "\n";
                $inside .= "Kick not-joined from bot: " . $this->bot
                        ->core("tools")
                        ->chatcmd("raid notinkick", "raid notinkick") . "\n\n";

                if (!empty($players)) {
                    if (strtolower($this->bot->game) == 'ao') {
                        foreach ($players as $player) {
                            if (!empty($assist)) {
                                $assist .= " \\n /assist $player";
                            } else {
                                $assist = "/assist $player";
                            }
                        }
                        $inside .= "<a href='chatcmd://$assist'>Check all raid members</a>\n\n";
                    }
                    $inside .= "Example use: <pre>raid check Can't assist yourself. Target is not in a fight. Can't find target &gt;Chris05&lt;.\n\n";
                    foreach ($players as $player) {
                        $who = $this->bot->core("whois")
                            ->lookup(
                                $player,
                                true
                            ); //All info about raiders are expected to be correct as already beeing member and all.

                        if ($who['faction'] == "Omni") {
                            $info = " [##omni##Omni</font>/";
                        } elseif ($who['faction'] == "Clan") {
                            $info = " [##clan##Clan</font>/";
                        } elseif ($who['faction'] == "Neutral") {
                            $info = " [##neut##Neut</font>/";
                        } else //Should never happend but who knows shit happens.
                        {
                            $info = " [<font color=#D7FFBC>" . $who['faction'] . "</font>/";
                        }

                        $info .= "<font color=#A2FF4C>" . $who['level'] . "</font>/";
                        $info .= "<font color=#FFFB9E>" . $who['profession'] . "</font>]";

                        $inside .= $player . " [" . $this->bot->core("tools")
                                ->chatcmd("raid kick " . $player, "Kick") . "]\n";
                    }
                } else {
                    $inside .= "There are no members of this raid.";
                }
                return "Players in raid :: " . $this->bot->core("tools")
                    ->make_blob("click to view", $inside);
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to do this";
        }
    }


    /*
    Locks/unlocks a Raid
    */
    function lock_raid($name, $lock)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if (strtolower($lock) == "lock") {
                if ($this->locked) {
                    $this->bot->send_tell($name, "Raid is Already ##highlight##locked##end##");
                    return false;
                } else {
                    $this->locked = true;
                    $this->bot->send_output(
                        "",
                        "##highlight##$name##end## has ##highlight##locked##end## the raid.",
                        "both"
                    );
                    $this->save();
                    return ("Raid ##highlight##locked##end## :: " . $this->control());
                }
            } else {
                if (!$this->locked) {
                    $this->bot->send_tell($name, "Raid is Already ##highlight##unlocked##end##");
                    return false;
                } else {
                    $this->locked = false;
                    $this->bot->send_output(
                        "",
                        "##highlight##$name##end## has ##highlight##unlocked##end## the raid.",
                        "both"
                    );
                    $this->save();
                    return ("Raid ##highlight##unlocked##end## :: " . $this->control());
                }
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to do this";
        }
    }


    /*
    Make click to join blob
    */
    function clickjoin($join = false)
    {
        if ($this->locked) {
            return "<font color=#FF6D4C>raid is locked</font>.";
        }

        $inside = "##blob_title##:::: Join/Leave Raid ::::##end##\n\n";
        if ($this->description && !empty($this->description)) {
            $inside .= "Description:\n     " . $this->description;
        }
        if ($join) {
            $inside .= "\n\n - " . $this->bot->core("tools")
                    ->chatcmd("join", "Join <botname>");
        }
        $inside .= "\n\n - " . $this->bot->core("tools")
                ->chatcmd("raid join", "Join the raid") . "\n";
        if ($this->bot->core("settings")->get("Raid", "showlft")) {
            $inside .= " - " . $this->bot->core("tools")
                    ->chatcmd("<botname>", "Go LFT", "lft") . "\n";
        }
        $inside .= "\n - " . $this->bot->core("tools")
                ->chatcmd("raid leave", "Leave the raid") . "\n";
        $inside .= " - " . $this->bot->core("tools")
                ->chatcmd("raid leave", "Leave the raid") . "\n";
        return $this->bot->core("tools")->make_blob("click to join", $inside);
    }


    /*
    Get correct char for points
    */
    function points_to($name)
    {
        return $this->bot->core("points")->points_to($name);
    }


    function points_to_name($name)
    {
        return $this->bot->core("points")->points_to_name($name);
    }


    /*
    This gets called on cron
    */
    function cron()
    {
        if (!$this->paused) {
            $points = $this->bot->core("settings")->get('Raid', 'Points');
            if (!is_numeric($points)) {
                $this->bot->send_output(
                    "",
                    "##error##Error: Invalid Amount set for Points in Settings (must be a number)",
                    "both"
                );
                $this->pause(true);
            } else {
                $users = $this->bot->db->select(
                    "SELECT raidingas FROM #___raid_points WHERE raiding = 1 ORDER BY raidingas"
                );
                if (!empty($users)) {
                    //$inside = " :: $points Given to all Raiders ::\n\n";
                    foreach ($users as $user) {
                        $count++;
                        $user = $user[0];
                        $this->points[$user] += $points;
                        $userp = isset($this->points[$user]) ? $this->points[$user] : 0;
                        $this->bot->db->query(
                            "INSERT INTO #___raid_log (name, points, time) VALUES ('" . $user . "', $userp, " . $this->start . ") ON DUPLICATE KEY UPDATE points = points + "
                            . $points
                        );
                    }
                }
                $this->bot->db->query("UPDATE #___raid_points SET points = points + " . $points . " WHERE raiding = 1");
            }
        }

        if ($this->announce
            && $this->announcel <= (time() + $this->bot
                    ->core("settings")->get('Raid', 'AnnounceDelay'))
        ) {
            if ($this->move > time()) {
                $move = $this->move - time();
                $move = ", Move in ##highlight##" . $this->bot->core("time")
                        ->format_seconds($move) . " ##end##";
            } else { $move = ""; }

            if ($this->tank && $this->showtank) {
                $nl = true;
                $tank = "\nTank is ##highlight##" . $this->tank . "##end##";
            } else { $tank = ""; }
            if ($this->showcallers && isset($this->bot->commands['tell']['caller']) && !empty($this->bot->commands['tell']['caller']->callers)) {
                if ($nl) {
                    $callers = ", ";
                } else {
                    $callers = "\n";
                }
                $callers .= $this->bot->commands['tell']['caller']->show_callers();
            } else { $callers = ""; }
            $this->bot->send_output(
                "",
                "Raid is running: ##highlight##" . $this->description . "##end##" . $tank . $callers . $move . " :: " . $this->clickjoin(
                ),
                "both"
            );
            $this->announcel = time();
        }
    }


    function pause($paused)
    {
        if ($paused) {
            $this->bot->send_output("", "Raid Point Ticker Paused", "both");
        } else {
            $this->bot->send_output("", "Raid Point Ticker Unpaused", "both");
        }
        $this->paused = $paused;
        return $this->control();
    }


    function change_level($name, $level)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                if (!is_numeric($level)) {
                    Return ("Raid Level Invalid ##highlight##$level##end## is not a number.");
                } elseif ($level > 220) {
                    Return ("Raid Level Invalid ##highlight##$level##end## is too high.");
                } elseif ($level < 1) {
                    Return ("Raid Level Invalid ##highlight##$level##end## is too low.");
                } else {
                    $this->minlevel = $level;
                    $this->save();
                    Return ("Raid Level Changed to ##highlight##$level##end## :: " . $this->control());
                }
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function notin($name)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $count = 0;
                $online = $this->bot->db->select(
                    "SELECT nickname FROM #___online WHERE status_pg = 1 AND botname = '" . $this->bot->botname . "' ORDER BY nickname"
                );
                if (!empty($online)) {
                    foreach ($online as $notin) {
                        if (!isset($this->user[ucfirst(strtolower($notin[0]))])) {
                            $this->bot->send_tell(
                                $notin[0],
                                "##error##Warning##end##: you are not in the current raid :: " . $this->clickjoin()
                            );
                            $count++;
                        }
                    }
                }
                Return ("Sent not in raid warnings to ##highlight##$count##end## Users");
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to send warnings";
        }
    }


    function notinkick($name)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $count = 0;
                $online = $this->bot->db->select(
                    "SELECT nickname FROM #___online WHERE status_pg = 1 AND botname = '" . $this->bot->botname . "' ORDER BY nickname"
                );
                if (!empty($online)) {
                    foreach ($online as $notin) {
                        if (!isset($this->user[ucfirst(strtolower($notin[0]))])
                            && !$this->bot
                                ->core("security")->check_access($notin[0], 'OWNER')
                        ) {
                            $this->bot->core("chat")->pgroup_kick($notin[0]);
                            $inside[] = $notin[0];
                            $this->bot->send_tell($notin[0], $name . " has Kicked you from privategroup.");
                            $count++;
                        }
                    }
                }
                if ($count > 0) {
                    $this->bot->send_output(
                        $name,
                        $name . " kicked ##highlight##" . implode(", ", $inside) . "##end## from privategroup.",
                        "pgmsg"
                    );
                }
                Return ("##highlight##$count##end## Users Kicked for not in raid");
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to kick";
        }
    }


    function move($name, $time)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $time = $this->bot->core("time")->parse_time($time);
                $this->move = time() + $time;
                Return ("Move Time set for ##highlight##" . $this->bot
                        ->core("time")->format_seconds($time) . " ##end##");
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to send warnings";
        }
    }


    function set_tank($name, $tank)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $this->tank = $tank;
                Return ("Tank set to ##highlight##" . $tank . " ##end##");
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to set tank";
        }
    }


    function set_announce($name, $set)
    {
        $set = strtolower($set);
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                if ($set == "on" || $set == "1") {
                    if ($this->announce) {
                        Return ("Announce is already Set to ##highlight##On##end##");
                    } else {
                        $this->announce = true;
                        $this->save();
                        Return ("Announce Set to ##highlight##On##end## :: " . $this->control());
                    }
                } elseif ($set == "off" || $set == "0") {
                    if (!$this->announce) {
                        Return ("Announce is already Set to ##highlight##Off##end##");
                    } else {
                        $this->announce = false;
                        $this->save();
                        Return ("Announce Set to ##highlight##Off##end## :: " . $this->control());
                    }
                }
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function show_tank($name, $set)
    {
        $set = strtolower($set);
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                if ($set == "on" || $set == "1") {
                    if ($this->showtank) {
                        Return ("Show Tank is already Set to ##highlight##On##end##");
                    } else {
                        $this->showtank = true;
                        //	$this -> save();
                        Return ("Show Tank Set to ##highlight##On##end## :: " . $this->control());
                    }
                } elseif ($set == "off" || $set == "0") {
                    if (!$this->showtank) {
                        Return ("Show Tank is already Set to ##highlight##Off##end##");
                    } else {
                        $this->showtank = false;
                        //	$this -> save();
                        Return ("Show Tank Set to ##highlight##Off##end## :: " . $this->control());
                    }
                }
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function show_callers($name, $set)
    {
        $set = strtolower($set);
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                if ($set == "on" || $set == "1") {
                    if ($this->showcallers) {
                        Return ("Show Callers is already Set to ##highlight##On##end##");
                    } else {
                        $this->showcallers = true;
                        //	$this -> save();
                        Return ("Show Callers Set to ##highlight##On##end## :: " . $this->control());
                    }
                } elseif ($set == "off" || $set == "0") {
                    if (!$this->showcallers) {
                        Return ("Show Callers is already Set to ##highlight##Off##end##");
                    } else {
                        $this->showcallers = false;
                        //	$this -> save();
                        Return ("Show Callers Set to ##highlight##Off##end## :: " . $this->control());
                    }
                }
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function set_description($name, $desc)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            if ($this->raid) {
                $this->description = $desc;
                $this->save();
                Return ("Description Change :: " . $this->control());
            } else {
                Return ("Error There isnt a Raid Running.");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function control()
    {
        $inside = "  ::  Raid Control Interface ::\n";
        if ($this->raid) {
            $info = "Running for ##end##" . (((int)((time() - $this->start) / 60)) + 1) . "##highlight## minutes";
            $link = $this->bot->core("tools")->chatcmd("raid end", "End");
        } else {
            $info = "Not Running";
            $link = "Start: " . $this->bot->core("tools")
                    ->chatcmd("raid start beast", "Beast") . "|" . $this->bot
                    ->core("tools")->chatcmd("raid start zods", "Zods");
        }
        $inside .= "\nRaid Status: ##highlight##$info##end##  [$link]";
        if ($this->raid) {
            $info = $this->minlevel;
            $link = $this->bot->core("tools")->chatcmd("raid level 205", "205");
            $link .= "|" . $this->bot->core("tools")
                    ->chatcmd("raid level 210", "210");
            $link .= "|" . $this->bot->core("tools")
                    ->chatcmd("raid level 215", "215");
            $inside .= "\nMin Level: ##highlight##$info##end##  [$link]";
        }
        if ($this->paused) {
            $info = "Paused";
            $link = $this->bot->core("tools")
                ->chatcmd("raid unpause", "unpause");
        } else {
            $points = $this->bot->core("settings")->get('Raid', 'Points');
            $info = $points . " per min";
            $link = $this->bot->core("tools")->chatcmd("raid pause", "pause");
        }
        $inside .= "\nPoints Status: ##highlight##$info##end##   [$link]";
        if ($this->locked) {
            $info = "Locked";
            $link = $this->bot->core("tools")->chatcmd("raid unlock", "unlock");
        } else {
            $info = "Open";
            $link = $this->bot->core("tools")->chatcmd("raid lock", "lock");
        }
        $inside .= "\nRaid State: ##highlight##$info##end##   [$link]";
        //$inside .= "\nModeration: disabled   [enable]";
        $inside .= "\nDescription: ##highlight##" . $this->description . "##end##";
        if ($this->announce) {
            $info = "Enabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid announce off", "Disable");
        } else {
            $info = "Disabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid announce on", "Enable");
        }
        $inside .= "\nDescription announcements: ##highlight##$info##end##   [$link]";
        if ($this->showtank) {
            $info = "Enabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid showtank off", "Disable");
        } else {
            $info = "Disabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid showtank on", "Enable");
        }
        $inside .= "\nShow Tank: ##highlight##$info##end##   [$link]";
        if ($this->showcallers) {
            $info = "Enabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid showcallers off", "Disable");
        } else {
            $info = "Disabled";
            $link = $this->bot->core("tools")
                ->chatcmd("raid showcallers on", "Enable");
        }
        $inside .= "\nShow Callers: ##highlight##$info##end##   [$link]";
        $active = count($this->user);
        $inactive = count($this->user2) - $active;
        $link = $this->bot->core("tools")->chatcmd("raid check", "Check");
        $link .= "|" . $this->bot->core("tools")->chatcmd("raid list", "List");
        $inside .= "\nThere are ##highlight##$active##end## active, and ##highlight##$inactive##end## inactive participants in raid   [$link]";
        $inside .= "\n\nLinks\n\n";
        $inside .= $this->bot->core("tools")
                ->chatcmd("raid notin", "Send not in Raid Warnings") . "\n";
        $inside .= $this->bot->core("tools")
                ->chatcmd("raid notinkick", "Kick toons not in Raid") . "\n";
        return ($this->bot->core("tools")->make_blob("Raid Control", $inside));
    }


    function list_users($name)
    {
        if ($this->bot->core("security")->check_access(
            $name,
            $this->bot
                ->core("settings")->get('Raid', 'Command')
        )
        ) {
            $inside = " :: Raid User List ::\n";
            if (!empty($this->user2)) {
                ksort($this->user2);
                foreach ($this->user2 as $n => $r) {
                    if (isset($this->user[$n])) {
                        $status = "##green##active##end##";
                    } else {
                        $status = "##red##$r##end##";
                    }
                    $userp = isset($this->points[$n]) ? $this->points[$n] : 0;
                    $inside .= "\n$n [Points:$userp] [$status]";
                    if (isset($this->user[$n])) {
                        $inside .= "   [" . $this->bot->core("tools")
                                ->chatcmd("raid kick " . $n, "Kick") . "]";
                    } elseif ($r == "Left PrivGroup") {
                        $inside .= "   [" . $this->bot->core("tools")
                                ->chatcmd("raid kick " . $n, "Remove from Rejoin") . "]";
                    }
                }
                $active = count($this->user);
                $inactive = count($this->user2) - count($this->user);
                Return ("##highlight##$active##end## Active and ##highlight##$inactive##end## Inactive Users in Raid :: " . $this->bot
                        ->core("tools")->make_blob("click to view", $inside));
            } else {
                Return ("##highlight##0##end## Active and ##highlight##0##end## Inactive Users in Raid");
            }
        } else {
            return "You must be a " . $this->bot->core("settings")
                ->get('Raid', 'Command') . " to change Raid type";
        }
    }


    function save()
    {
        $info[] = $this->name;
        $info[] = (int)$this->start;
        $info[] = (int)$this->announce;
        $info[] = (int)$this->locked;
        $info[] = $this->minlevel;
        $info[] = $this->description;
        $info = implode(";", $info);
        $this->bot->core("settings")->save("Raid", "raidinfo", $info);
    }


    function movein()
    {
        if ($this->move > time()) {
            $move = $this->move - time();
            $minutes = floor($move / 60);
            //$rest = $totalsec % (60*60);
            //$seconds = $rest % 60;
            Return ", Move in ##highlight##" . sprintf("%02dmins", $minutes) . " ##end##";
        } else {
            return false;
        }
    }
}

?>
