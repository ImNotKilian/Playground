<?php

define('REMOTE_URL', 'http://origin.clubpenguin.com/');

define('CLIENT', 'play/v2/client/');
define('ENGLISH_FONTS', 'play/v2/client/fonts/en/');

define('GAMES', 'play/v2/games/');

define('WEB_SERVICE', 'play/web_service/');
define('ENGLISH_WEB_SERVICE', 'play/en/web_service/');
define('ENGLISH_GAME_CONFIGS', 'play/en/web_service/game_configs/');

define('START_SWF', 'play/start/swf/');
define('START_XML', 'play/start/xml/');

define('CLOTHING_ICONS', 'play/v2/content/global/clothing/icons/');
define('CLOTHING_PAPER', 'play/v2/content/global/clothing/paper/');
define('CLOTHING_PHOTO', 'play/v2/content/global/clothing/photos/');
define('CLOTHING_SPRITES', 'play/v2/content/global/clothing/sprites/');

define('CONTENT_GLOBAL', 'play/v2/content/global/');
define('GLOBAL_CONTENT', 'play/v2/content/global/content/');
define('GLOBAL_CONTENT_EPF', 'play/v2/content/global/content/epf/');

define('PENGUIN_ACTIONS', 'play/v2/content/global/penguin/actions/');

define('GLOBAL_CRUMBS', 'play/v2/content/global/crumbs/');

define('FURNITURE_ICONS', 'play/v2/content/global/furniture/icons/');
define('FURNITURE_SPRITES', 'play/v2/content/global/furniture/sprites/');

define('IGLOO_BUILDINGS_ICONS', 'play/v2/content/global/igloo/buildings/icons/');
define('IGLOO_BUILDINGS_SPRITES', 'play/v2/content/global/igloo/buildings/sprites/');

define('IGLOO_DATA', 'play/v2/content/global/igloo/data/');

define('FLOORING_ICONS', 'play/v2/content/global/igloo/flooring/icons/');
define('FLOORING_SPRITES', 'play/v2/content/global/igloo/flooring/sprites/');

define('LOCATIONS_ICONS', 'play/v2/content/global/igloo/locations/icons/');
define('LOCATIONS_SPRITES', 'play/v2/content/global/igloo/locations/sprites/');

define('PUFFLE_PAPER', 'play/v2/content/global/puffle/paper/');
define('PUFFLE_SPRITES_DIG', 'play/v2/content/global/puffle/sprites/dig/');
define('PUFFLE_SPRITES_IGLOO', 'play/v2/content/global/puffle/sprites/igloo/');
define('PUFFLE_SPRITES_WALK', 'play/v2/content/global/puffle/sprites/walk/');

define('GLOBAL_BACKYARD', 'play/v2/content/global/backyard/');

define('GLOBAL_INTERFACE', 'play/v2/content/global/interface/');
define('GLOBAL_LOGO', 'play/v2/content/global/logo/');
define('GLOBAL_MUSIC', 'play/v2/content/global/music/');
define('GLOBAL_PENGUIN', 'play/v2/content/global/penguin/');

define('GLOBAL_PROMPTS_PUFFLE', 'play/v2/content/global/prompts/puffle/');

define('GLOBAL_ROOMS', 'play/v2/content/global/rooms/');
define('GLOBAL_ROOMS_EFFECTS', 'play/v2/content/global/rooms/effects/');

define('GLOBAL_STAMPBOOK', 'play/v2/content/global/stampbook/');
define('GLOBAL_TELESCOPE', 'play/v2/content/global/telescope/');

define('LOCAL_ENGLISH', 'play/v2/content/local/en/');
define('LOCAL_ENGLISH_CATALOGUES', 'play/v2/content/local/en/catalogues/');
define('LOCAL_ENGLISH_CLOSEUPS', 'play/v2/content/local/en/close_ups/');
define('LOCAL_ENGLISH_CRUMBS', 'play/v2/content/local/en/crumbs/');
define('LOCAL_ENGLISH_NEWS_PAPERS', 'play/v2/content/local/en/news/papers/');

function fetchAndDecode($remote_url) {
	$data = file_get_contents($remote_url);
	
	return json_decode($data, true);
}

function downloadFile($remote_url, $local_uri) {
	if(file_exists($local_uri) === false) {
		echo "Attempting to download $remote_url..";
		$curl = curl_init($remote_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		
		$data = curl_exec($curl);
		
		$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		if($status_code == 200) {
			echo "success\nLocal: $local_uri\n";
			if(is_dir($directory = str_replace(REMOTE_URL, "", dirname($remote_url))) === false) {
				mkdir($directory, 0777, true);
			}
			
			if(is_dir($directory = dirname($local_uri)) === false) {
				echo "Creating $directory\n";
				mkdir($directory, 0777, true);
			}
			
			file_put_contents($local_uri, $data);
		} else {
			echo "failed\n";
		}
	}
}

$defined_constants = get_defined_constants(true);
$media_directories = $defined_constants["user"];

foreach($media_directories as $media_directory) {
	if(is_dir($media_directory) === false) {
		echo "Creating $media_directory\n";
		mkdir($media_directory, 0777, true);
	}
}

downloadFile(REMOTE_URL . ENGLISH_WEB_SERVICE . "game_configs.bin", ENGLISH_WEB_SERVICE . "game_configs.bin");

$gc_archive = new ZipArchive();
$gc_archive->open(ENGLISH_WEB_SERVICE . "game_configs.bin");
$gc_archive->extractTo(ENGLISH_GAME_CONFIGS);
$gc_archive->close();

rename(ENGLISH_GAME_CONFIGS . "weblogger.php", ENGLISH_WEB_SERVICE . "weblogger.php");

downloadFile(REMOTE_URL . START_XML . "start_module_config.xml", START_XML . "start_module_config.xml");
downloadFile(REMOTE_URL . START_SWF . "start_module.swf", START_SWF . "start_module.swf");
downloadFile("http://www.clubpenguin.com/xml/start-module-xml?response_type=embed", "xml/start-module-xml");

$start_module = file_get_contents("xml/start-module-xml");
$xml = simplexml_load_string($start_module);
$xml_array = json_decode(json_encode($xml), true);

$billboards = $xml_array["billboard_list"]["billboard"];
foreach($billboards as $billboard) {
	downloadFile("http://www.clubpenguin.com/" . $billboard["@attributes"]["src"], str_replace("?response_type=embed", "", $billboard["@attributes"]["src"]));
}

downloadFile(REMOTE_URL . GLOBAL_CONTENT . "map_triggers.json", GLOBAL_CONTENT . "map_triggers.json");

$client_files = array(
	"airtower.swf",
	"backyard.swf",
	"banning.swf",
	"book.swf",
	"club_penguin.swf",
	"dependencies.json",
	"engine.swf",
	"fonts/en/FontLibrary.swf",
	"gridview.swf",
	"igloo.swf",
	"igloo_map.swf",
	"interface.swf",
	"intro_to_cp.swf",
	"intro_to_cp_quest_map.swf",
	"LikeWindow.swf",
	"like_window_module.swf",
	"load.swf",
	"login.swf",
	"mail.swf",
	"newspaper.swf",
	"ninja_progress.swf",
	"notifications.swf",
	"party.swf",
	"phone.swf",
	"phrase_autocomplete.swf",
	"puffle_adoption.swf",
	"puffle_care.swf",
	"puffle/puppet/assetLibraryPufflePuppet.swf",
	"puffle_ui_widget/assets/radial_menu_assets.swf",
	"puffle_care_station_menu.swf",
	"puffle_certificate.swf",
	"puffle_gold_berry_machine.swf",
	"puffle_gold_quest_progress.swf",
	"puffle_manual.swf",
	"puffle_treasure_infographic.swf",
	"rooms_common.swf",
	"sentry.swf",
	"shell.swf",
	"stampbook.swf",
	"stamps.swf",
	"world.swf"
);

foreach($client_files as $index => $client_file) {
	downloadFile(REMOTE_URL . CLIENT . $client_file, CLIENT . $client_file);
	unset($client_files[$index]);
}

$paths_array = fetchAndDecode("http://origin.clubpenguin.com/play/en/web_service/game_configs/paths.json");
foreach($paths_array as $category => $another_path_array) {
	if($category == 'global' || $category == 'local') {
		foreach($another_path_array as $path) {
			$path_info = pathinfo($path);
			
			if(isset($path_info["extension"])) {
				$path = ($category == 'global' ? CONTENT_GLOBAL : LOCAL_ENGLISH) . $path;
				
				if(is_dir($directory = dirname($path)) === false) {
					mkdir($directory, 0777, true);
				}
				
				downloadFile(REMOTE_URL . $path, $path);
			} else {
				$directory = ($category == 'global' ? CONTENT_GLOBAL : LOCAL_ENGLISH) . $path;
				if(is_dir($directory) === false) {
					echo "Creating $directory\n";
					mkdir($directory, 0777, true);
				}
			}
		}
	}
}



$penguin_actions = fetchAndDecode("http://origin.clubpenguin.com/play/en/web_service/game_configs/penguin_action_frames.json");
foreach($penguin_actions as $foo_parent => $foo_parent_array) {
	foreach($foo_parent_array as $bar_child_index => $bar_child_array) {
		downloadFile(REMOTE_URL . PENGUIN_ACTIONS . $bar_child_array["secret_frame"] . ".swf", PENGUIN_ACTIONS . $bar_child_array["secret_frame"] . ".swf");
	}
}

for($car_id = 0; !$car_id > 16; $car_id++) {
	$file = sprintf("penguin_car_%s.swf", $car_id < 10 ? "0$car_id" : $car_id);
	downloadFile(REMOTE_URL . GLOBAL_PENGUIN . $file, GLOBAL_PENGUIN . $file);
}

$puffles = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/puffles.json");
foreach($puffles as $puffle) {
	$lc_description = strtolower($puffle["description"]);
	
	$dig_path = sprintf("puffle_%s_dig.swf", $lc_description);
	$sprite_path = sprintf("puffle_%s_igloo.swf", $lc_description);
	$walk_path = sprintf("puffle_%s_walk.swf", $lc_description);
	$paper_path = sprintf("puffle_%s_paper.swf", $lc_description);
	
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_DIG . $dig_path, PUFFLE_SPRITES_DIG . $dig_path);
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_IGLOO . $sprite_path, PUFFLE_SPRITES_IGLOO . $sprite_path);
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_WALK . $walk_path, PUFFLE_SPRITES_WALK . $walk_path);
	downloadFile(REMOTE_URL  .PUFFLE_PAPER . $paper_path, PUFFLE_PAPER . $paper_path);
}

$wild_puffles = array(
	array("black", 1000),
	array("purple", 1001),
	array("red", 1002),
	array("blue", 1003),
	array("yellow", 1004),
	array("pink", 1005),
	array("blue", 1006),
	array("orange", 1007)
);

foreach($wild_puffles as $wild_puffle) {
	list($type, $sub_type) = $wild_puffle;
	
	$walk_file = sprintf("puffle_%s%d_walk.swf", $type, $sub_type);
	$dig_file = sprintf("puffle_%s%d_dig.swf", $type, $sub_type);
	$igloo_file = sprintf("puffle_%s%d_igloo.swf", $type, $sub_type);
	$paper_file = sprintf("puffle_%s%d_paper.swf", $type, $sub_type);
	
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_WALK . $walk_file, PUFFLE_SPRITES_WALK . $walk_file);
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_DIG . $dig_file, PUFFLE_SPRITES_DIG . $dig_file);
	downloadFile(REMOTE_URL . PUFFLE_SPRITES_IGLOO . $igloo_file, PUFFLE_SPRITES_IGLOO . $igloo_file);
	downloadFile(REMOTE_URL . PUFFLE_PAPER . $paper_file, PUFFLE_PAPER . $paper_file);
}

$items = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/paper_items.json");
$non_sprites = array(1, 8, 9, 10); 
foreach($items as $index => $item) {
	$item_id = $item["paper_item_id"] . ".swf";
	
	downloadFile(REMOTE_URL . CLOTHING_ICONS . $item_id, CLOTHING_ICONS . $item_id);
	downloadFile(REMOTE_URL . CLOTHING_PAPER . $item_id, CLOTHING_PAPER . $item_id);
	
	if(in_array($item["type"], $non_sprites) === false) {
		downloadFile(REMOTE_URL . CLOTHING_SPRITES . $item_id, CLOTHING_SPRITES . $item_id);
	}
	
	if($item["type"] == 9) {
		downloadFile(REMOTE_URL . CLOTHING_PHOTO . $item_id, CLOTHING_PHOTO . $item_id);
	}
	
	unset($items[$index]);
}

$rooms = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/rooms.json");
foreach($rooms as $room_id => $room) {
	downloadFile(REMOTE_URL . GLOBAL_ROOMS . $room["path"], GLOBAL_ROOMS . $room["path"]);
	
	if($room["music_id"] != 0) {
		downloadFile(REMOTE_URL . GLOBAL_MUSIC . $room["music_id"] . ".swf", GLOBAL_MUSIC . $room["music_id"] . ".swf");
	}
	
	unset($rooms[$room_id]);
}

downloadFile(REMOTE_URL . GLOBAL_ROOMS_EFFECTS . "avatar.swf", GLOBAL_ROOMS_EFFECTS. "avatar.swf");
downloadFile(REMOTE_URL . GLOBAL_ROOMS_EFFECTS . "boatback.swf", GLOBAL_ROOMS_EFFECTS . "boatback.swf");
downloadFile(REMOTE_URL . GLOBAL_ROOMS_EFFECTS . "boatfront.swf", GLOBAL_ROOMS_EFFECTS . "boatfront.swf");

$igloo_music = fetchAndDecode("http://media1.clubpenguin.com/play/v2/content/global/igloo/data/music.json");
$igloo_music = $igloo_music["songs"];

foreach($igloo_music as $music) {
	downloadFile(REMOTE_URL . GLOBAL_MUSIC . $music["id"] . ".swf", GLOBAL_MUSIC . $music["id"] . ".swf");
}

downloadFile(REMOTE_URL . IGLOO_DATA . "music.json", IGLOO_DATA . "music.json");

$igloos = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/igloos.json");
foreach($igloos as $igloo_id => $igloo) {
	$igloo_path = $igloo_id . ".swf";
	
	downloadFile(REMOTE_URL . IGLOO_BUILDINGS_ICONS . $igloo_path, IGLOO_BUILDINGS_ICONS . $igloo_path);
	downloadFile(REMOTE_URL . IGLOO_BUILDINGS_SPRITES . $igloo_path, IGLOO_BUILDINGS_SPRITES . $igloo_path);
}

$igloo_floors = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/igloo_floors.json");
foreach($igloo_floors as $igloo_floor) {
	$floor = $igloo_floor["igloo_floor_id"] . ".swf";
	
	if($floor != '0.swf') {
		downloadFile(REMOTE_URL . FLOORING_ICONS . $floor, FLOORING_ICONS . $floor);
		downloadFile(REMOTE_URL . FLOORING_SPRITES . $floor, FLOORING_SPRITES . $floor);
	}
}

$igloo_locations = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/igloo_locations.json");
foreach($igloo_locations as $location) {
	$location_path = $location["igloo_location_id"] . ".swf";
	$background_path = sprintf("%s_backyard.swf", $location["igloo_location_id"]);
	
	downloadFile(REMOTE_URL . LOCATIONS_ICONS . $location_path, LOCATIONS_ICONS . $location_path);
	downloadFile(REMOTE_URL . LOCATIONS_SPRITES . $location_path, LOCATIONS_SPRITES . $location_path);
	downloadFile(REMOTE_URL . GLOBAL_BACKYARD . $background_path, GLOBAL_BACKYARD . $background_path); 
}

$furniture_array = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/furniture_items.json");
foreach($furniture_array as $furniture) {
	$furniture_id = $furniture["furniture_item_id"];
	
	downloadFile(REMOTE_URL . FURNITURE_ICONS . $furniture_id . ".swf", FURNITURE_ICONS . $furniture_id . ".swf");
	downloadFile(REMOTE_URL . FURNITURE_SPRITES . $furniture_id . ".swf", FURNITURE_SPRITES . $furniture_id . ".swf");
}

$games = fetchAndDecode("http://media1.clubpenguin.com/play/en/web_service/game_configs/games.json");
foreach($games as $game => $game_info) {
	$path = str_replace("\\", "/", $game_info["path"]);
	
	downloadFile(REMOTE_URL . GAMES . $path, GAMES . $path);
	
	if($game_info["music_id"] != 0) {
		downloadFile(REMOTE_URL . GLOBAL_MUSIC . $game_info["music_id"] . ".swf", GLOBAL_MUSIC . $game_info["music_id"] . ".swf");
	}
}

?>