<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Team;
use App\Models\Player;
use App\Models\MatchModel;
use App\Models\Statistic;
use App\Models\Inventory;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Teams
        $t1 = Team::create(['name' => 'Red Falcons','game'=>'Valorant','description'=>'Competitive Valorant team']);
        $t2 = Team::create(['name' => 'Blue Wolves','game'=>'Dota 2','description'=>'Casual but skilled']);

        // Players
        $p1 = Player::create(['user_id' => 2, 'team_id' => $t1->id, 'nickname' => 'Falcon1','role_in_game'=>'AWPer','rank'=>'Diamond']);
        $p2 = Player::create(['user_id' => 3, 'team_id' => $t1->id, 'nickname' => 'Falcon2','role_in_game'=>'Support','rank'=>'Platinum']);
        $p3 = Player::create(['user_id' => 4, 'team_id' => $t2->id, 'nickname' => 'Wolf1','role_in_game'=>'Carry','rank'=>'Diamond']);

        // Matches
        MatchModel::create(['team_id'=>$t1->id,'opponent'=>'Green Tigers','match_date'=>'2026-06-01','result'=>'win','score_team'=>16,'score_opponent'=>8]);
        MatchModel::create(['team_id'=>$t2->id,'opponent'=>'Orange Fox','match_date'=>'2026-06-05','result'=>'lose','score_team'=>1,'score_opponent'=>2]);

        // Statistics
        Statistic::create(['player_id'=>$p1->id,'matches_played'=>10,'win'=>6,'lose'=>4,'kill'=>120,'death'=>80,'assist'=>30,'kda'=>2.25]);
        Statistic::create(['player_id'=>$p2->id,'matches_played'=>8,'win'=>5,'lose'=>3,'kill'=>60,'death'=>40,'assist'=>50,'kda'=>2.75]);

        // Inventories
        Inventory::create(['team_id'=>$t1->id,'item_name'=>'Gaming PC','category'=>'Hardware','quantity'=>5,'condition'=>'Good','notes'=>'Used for scrims']);
        Inventory::create(['team_id'=>$t2->id,'item_name'=>'Headset','category'=>'Accessory','quantity'=>5,'condition'=>'New','notes'=>'Sponsor']);
    }
}
