<?php include_once 'assets/games/lnn/lnn_code.php' ?>
<div id='wrap' class='wrap'>
    <?php echo wrap_top() ?>
    <p id='description'><?php
        echo _('A list of Touhou Lunatic No Miss No Bomb (LNN) runs, updated every so often. ' .
        'For every shottype in a game, tables will tell you which players have done an LNN with it, if any. ' .
        'If a player has multiple LNNs for one particular shottype, those are not factored in.');
	?></p>
    <p id='conditions'><?php
        echo _('Extra conditions are required for PCB, TD, HSiFS, WBaWC and UM; these are No Border Breaks for PCB, ' .
        'No Trance for TD, No Release for HSiFS, No Berserk Roar No Roar Breaks for WBaWC and No Cards for UM. ' .
        'LNN in these games is called LNNN or LNNNN, with extra Ns to denote the extra conditions. ' .
        'The extra condition in UFO, no UFO summons, is optional, as it is not considered to have a significant ' .
        'impact on the difficulty of the run. As for IN, an LNN is assumed to capture all Last Spells and '.
        'is referred to as LNNFS.');
	?></p>
    <p id='tables'><?php echo _('All of the table columns are sortable.') ?></p>
    <p id='lastupdate'><?php echo (isset($lnn['LM']) ? format_lm($lnn['LM'], $lang) : '') ?></p>
    <h2 id='contents_header'><?php echo _('Contents') ?></h2>
    <?php
        // With JavaScript disabled OR wr_old_layout cookie set, show links to all games and player search
        if ($layout == 'New') {
            echo '<div id="contents_new" class="border"><p><a href="#lnns" class="lnns">' . _('LNN Lists') .
            '</a></p><p><a href="#overall">' . _('Overall Count') .
            '</a></p><p><a href="#players">' . _('Player Ranking') .
            '</a></p></div><noscript>';
        }
        echo '<div id="contents" class="border"><p><a href="#lnns">' . _('LNN Lists') . '</a></p>';
        foreach ($lnn as $game => $obj) {
            if ($game == 'LM') {
                continue;
            }
            echo '<p><a href="#' . $game . '">' . full_name($game) . '</a></p>';
        }
        echo '<p id="playersearchlink"><a href="#playersearch">' . _('Player Search') .
        '</a></p><p><a href="#overall">' . _('Overall Count') .
        '</a></p><p><a href="#players">' . _('Player Ranking') .
        '</a></p></div>';
        if ($layout == 'New') {
            echo '</noscript>';
        }
    ?>
    <h2 id='lnns'><?php echo _('LNN Lists') ?></h2>
    <?php
        // With JavaScript disabled OR lnn_old_layout cookie set, show classic all games layout
        if ($layout == 'New') {
            echo '<noscript>';
        }
        foreach ($lnn as $game => $obj) {
            if ($game == 'LM') {
                continue;
            }
            $sum = 0;
            $all = array();
            echo '<div id="' . $game . '"><p><table id="' . $game . 't" class="' . $game . 't">' .
            '<caption><span id="' . $game . '_image_old" class="cover ' . (game_num($game) <= 5 ? 'cover98' : '') . '"></span> ' . full_name($game) . '</caption>' .
            '<thead><tr><th class="general_header">' . shot_route($game) . '</th>' .
            '<th class="general_header nowrap">' . lnn_type($game, $lang) . '<br>' . _('(Different players)') . '</th>' .
            '<th class="general_header">' . _('Players') . '</tr></thead><tbody>';
            foreach ($obj as $shot => $players) {
                if (strpos($shot, 'UFOs')) {
                    continue;
                }
                $count = sizeof($players);
                $sum += $count;
                $all = array_merge($all, $players);
                if ($game == 'UFO') {
                    $count += sizeof($obj[$shot . 'UFOs']);
                }
                sort($players);
                echo '<tr><td class="nowrap">' . format_shot($game, $shot) . '</td><td>' . $count . '</td><td>' . implode(', ', $players);
                if ($game == 'UFO') {
                    $players = $obj[$shot . 'UFOs'];
                    $sum += sizeof($players);
                    $all = array_merge($all, $players);
                    for ($i = 0; $i < sizeof($players); $i++) {
                        $players[$i] .= ' (UFOs)';
                    }
                    sort($players);
                    echo (sizeof($players) > 0 ? ', ' : '') . implode(', ', $players);
                }
                echo '</td></tr>';
            }
            $all = array_unique($all);
            sort($all);
            echo '</tbody><tfoot><tr><td class="foot">' . _('Overall') . '</td><td class="foot">' . $sum . ' (' . sizeof($all) . ')</td>' .
            '<td class="foot">' . implode(', ', $all) . '</td></tr></tfoot></table></div>';
        }
        if ($layout == 'New') {
            echo '</noscript>';
        }
        // With lnn_old_layout cookie NOT set, show game image layout (CSS hides it with JavaScript disabled)
        if ($layout == 'New') {
            echo '<div id="newlayout"><p id="clickgame">' . _('Click a game cover to show its list of LNNs.') . '</p>';
		    foreach ($lnn as $game => $value) {
		        if ($game == 'LM') {
		            continue;
	            }
		        echo '<span class="game_image"><span id="' . $game . '_image" class="game_img"></span>' .
                '<span class="full_name tooltip">' . full_name($game) . '</span></span>';
		    }
            echo '</div>';
        }
    ?>
    <div id='lnn_list'>
        <p id='fullname'></p>
        <table id='lnn_table'>
            <thead id='lnn_thead'>
                <tr>
                    <th id='lnn_shotroute' class='general_header'></th>
                    <th class='general_header nowrap'><span id='lnn_restrictions'></span><br><?php echo _('(Different players)') ?></th>
                    <th class='general_header'><?php echo _('Players') ?></th>
                </tr>
            </thead>
            <tbody id='lnn_tbody'></tbody>
            <tfoot id='lnn_tfoot'>
                <tr>
                    <td id='lnn_overall' class='foot'><?php echo _('Overall') ?></td>
                    <td id='count' class='foot'></td>
                    <td id='total' class='foot'></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div id='playersearch'>
        <h2><?php echo _('Player Search'); ?></h2>
		<p id='playerlnns'><?php echo _('Choose a player name from the menu below to show their LNNs.') ?></p>
		<label for='player'><?php echo _('Player'); ?></label>
		<select id='player'>
            <option value=''>...</option>
		    <?php
		        asort($pl);
		        foreach ($pl as $key => $player) {
		            echo '<option value="' . $player . '">' . $player . '</option>';
		        }
		    ?>
	    </select>
    </div>
	<div id='player_list'>
		<table class='sortable'>
			<thead id='player_thead'><tr>
                <th class='general_header'><?php echo _('Game') ; ?></th>
                <th class='general_header'><?php echo _('Shottype'); ?></th>
                <th class='general_header'><?php echo _('Replay'); ?></th>
            </tr></thead>
			<tbody id='player_tbody'></tbody>
			<tfoot id='player_tfoot'>
                <tr>
                    <td colspan='5'></td>
                </tr>
                <tr class='irregular_tr'>
                    <td><?php echo _('Total') ?></td>
                    <td id='player_sum' colspan='4'></td>
                </tr>
            </tfoot>
		</table>
	</div>
    <div id='overall'>
        <h2><?php echo _('Overall Count'); ?></h2>
        <table class='sortable'>
            <thead>
                <tr>
                    <th class='general_header'>#</th>
                    <th class='general_header'><?php echo _('Game'); ?></th>
                    <th class='general_header'><?php echo _('No. of LNNs'); ?></th>
                    <th class='general_header'><?php echo _('Different players') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($lnn as $game => $data1) {
                        if ($game == 'LM') {
                            continue;
                        }
                        echo '<tr><td' . (game_num($game) == 128 ? ' data-sort="12.8"' : '') . '>' . game_num($game) . '</td><td class="' . $game . '">' . _($game) . '</td>';
                        $sum = 0;
                        $game_pl = array();
                        foreach ($lnn[$game] as $shottype => $data2) {
                            $sum += sizeof($lnn[$game][$shottype]);
                            foreach ($lnn[$game][$shottype] as $player => $data3) {
                                if (!in_array($data3, $game_pl)) {
                                    array_push($game_pl, $data3);
                                }
                            }
                        }
                        echo '<td>' . $sum . '</td><td>' . sizeof($game_pl) . '</td></tr>';
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class='foot' colspan='2'><?php echo _('Overall'); ?></td>
                    <td class='foot'><?php echo $gt ?></td>
                    <td class='foot'><?php echo sizeof($pl_lnn) ?></td>
                </tr>
                <tr>
                    <td colspan='2'><?php echo _('Replays'); ?></td>
                    <td colspan='2'><?php echo sizeof(glob('replays/lnn/*/*')) + sizeof($video_lnns) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div id='players'>
        <h2><?php echo _('Player Ranking'); ?></h2>
        <table id='ranking' class='sortable'>
            <thead>
                <tr>
                    <th class='general_header no-sort'>#</th>
                    <th class='general_header'><?php echo _('Player'); ?></th>
                    <th class='general_header'><?php echo _('No. of LNNs'); ?></th>
                    <th class='general_header'><?php echo _('Games LNN\'d'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                    uasort($pl_lnn, function($a, $b) {
                        $val = $b[1] <=> $a[1];
                        if ($val == 0) {
                            $val = $b[2] <=> $a[2];
                        }
                        return $val;
                    });
                    foreach ($pl_lnn as $key => $value) {
                        $shot_lnns = $pl_lnn[$key][1] == $ALL_LNN ? $pl_lnn[$key][1] . _(' (All Windows)') : $pl_lnn[$key][1];
                        $game_lnns = $pl_lnn[$key][2] == $ALL_GAME_LNN ? $pl_lnn[$key][2] . _(' (All Windows)') : $pl_lnn[$key][2];
                        //$shot_lnns = $pl_lnn[$key][1];
                        //$game_lnns = $pl_lnn[$key][2];
                        echo '<tr><td></td><td>' . $pl_lnn[$key][0] . '</td><td data-sort="' . $pl_lnn[$key][1] . '">' . $shot_lnns . '</td><td data-sort="' . $pl_lnn[$key][2] . '">' . $game_lnns . '</td></tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
    <p id='back'><strong><a id='backtotop' href='#top'><?php echo _('Back to Top'); ?></a></strong></p>
	<?php echo '<input id="missingReplays" type="hidden" value="' . implode('', $missing_replays) . '">' ?>
	<?php echo '<input id="videos" type="hidden" value="' . implode(',', $video_lnns) . '">' ?>
</div>
