<!DOCTYPE html>
<html>
<body>
<?php
//HERE to adjust the max item rarity and max vip rank
$MAX_ITEM_RARITY = 9;
$MAX_VIP_RANK = 7;
?>
<form action = "gacha.php" method="post">
    <input type="submit" name="gachabtn" class="button" value="GACHA!" />
</form>
<br><br>

<?php

function roll_item($vip_rank) {
    global $MAX_ITEM_RARITY,$MAX_VIP_RANK;
    $MIN_RAND = 1;
    $MAX_RAND = 10000;
    $result = 0;
    $DIVISOR_CHANCE = 80-100*($vip_rank/$MAX_VIP_RANK); //use to decrease the chance for getting item rarity that greater than vip rank+1
    if ($vip_rank+1 > $MAX_ITEM_RARITY) {
        $vip_rank = $MAX_VIP_RANK;
    }
    //get the base chance for "HIGHER CHANCE" item
    $base_probability = (($vip_rank+1)/$MAX_ITEM_RARITY);

    //if the base vip_rank is greater than max item rarity, then just set the base probability to 1
    if ($base_probability > 1.0) {
        $base_probability = 1.0;
    }
    //remain probability use for "LOWER CHANCE" item
    $remain_probability = 1.0 - $base_probability;
    //Make the remain probability lower to avoid low vip rank have high chance to get high rarity item
    $base_probability += $remain_probability-$remain_probability/$DIVISOR_CHANCE;
    $remain_probability = $remain_probability/$DIVISOR_CHANCE;
    //echo $base_probability," ";

    //Generate random number
    $randNum = mt_rand($MIN_RAND,$MAX_RAND*10000) / 10000;  
    //echo "<br>",$randNum," ";
    //First check is the result is the "HIGHER CHANCE" item
    //For the cases that max vip rank > max item rarity, need to set the max vip range to max item rarity
    $max_vip_range = ($vip_rank+1 <= $MAX_ITEM_RARITY) ? $vip_rank+1 : $MAX_ITEM_RARITY;
    for ($i=1; $i<=$max_vip_range; $i++) {
        //result is the item rarity that the player get
        $result += 1;
        
        if ($randNum <= $MAX_RAND*$base_probability*($i/($max_vip_range))) {
            return $result;
        }
    }

    //Now check the result if it is the "LOWER CHANCE" item
    //For the cases that max vip rank < max item rarity, need to set the max vip range to max item rarity
    $max_remain_range = ($MAX_VIP_RANK <= $MAX_ITEM_RARITY) ? $MAX_VIP_RANK : $MAX_ITEM_RARITY;
    //echo "remain: ",$max_remain_range," ";
    for ($i=$vip_rank+1; $i<=$max_remain_range; $i++) {
        $result += 1;
        //echo $MAX_RAND*$base_probability + $remain_probability*($i/$MAX_VIP_RANK), " ";
        //echo "random Num: ", $randNum, "<br>";
        if ($randNum <= $MAX_RAND*( $base_probability + $remain_probability*($i/$max_remain_range) )) {
            return $result;
        }
    }
    /*
    echo "Range of higest rarity item: ", $MAX_RAND*( $base_probability + $remain_probability), "<br>";
    echo "Range of second higest rarity item: ", $MAX_RAND*( $base_probability + $remain_probability * (($MAX_VIP_RANK-1)/$MAX_VIP_RANK)), "<br>";
    echo "Undefine random Num: ", $randNum, "<br>";*/
}

function gacha100() {
    global $MAX_VIP_RANK,$MAX_ITEM_RARITY;
    //Get the result for each vip rank
    for ($vip=1; $vip<=$MAX_VIP_RANK; $vip++) {
        $distribution = [];
        //initialize distribution value
        for($i=0; $i<=$MAX_ITEM_RARITY; $i++) {
            $distribution[$i] = 0;
        }

        //loop roll_item function for 100 times and save the result into $distribution
        for($j=1; $j<=100; $j++) {
            $item = roll_item($vip);
            $distribution[$item-1] += 1 ;
        }

        //Print the result for current vip rank
        echo "<br> [vip", $vip, "] => Array:<br>";
        for($k=0; $k<$MAX_ITEM_RARITY; $k++) {
            echo "[", $k+1, "] => ", $distribution[$k], "<br>";
        }
    }
}

if (isset($_POST['gachabtn'])) {
    gacha100();
    unset($_POST['gachabtn']);
}
?>

</body>
</html>