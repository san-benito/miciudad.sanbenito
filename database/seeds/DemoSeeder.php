<?php

use Carbon\Carbon;
use App\User;
use App\Role;
use App\File;
use App\ImageFile;
use App\Category;
use App\Community;
use App\Organization;
use App\Objective;
use App\Goal;
use App\Milestone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create('es_AR');
        
        $category = new Category();
        $category->title = 'Educacion';
        $category->icon = 'fas fa-book';
        $category->color = '#602282';
        $category->save();
        $category = new Category();
        $category->title = 'Seguridad';
        $category->icon = 'fas fa-shield-alt';
        $category->color = '#30689c';
        $category->save();
        $category = new Category();
        $category->title = 'Ecologia';
        $category->icon = 'fas fa-tree';
        $category->color = '#32a852';
        $category->save();
        $category = new Category();
        $category->title = 'Economia comunitaria';
        $category->icon = 'fas fa-wallet';
        $category->color = '#b52260';
        $category->save();
        $category = new Category();
        $category->title = 'Musica';
        $category->icon = 'fas fa-music';
        $category->color = '#ba8e14';
        $category->save();

        $admin = new User();
        $admin->name = 'Admin';
        $admin->surname = 'Participes';
        $admin->email = 'admin@admin.com';
        $admin->email_verified_at = now();
        $admin->password = Hash::make('participes');
        $admin->remember_token = Str::random(10);
        $admin->save();
        $admin->roles()->attach(Role::where('name', 'user')->first());
        $admin->roles()->attach(Role::where('name', 'admin')->first());
        $admin->save();

        $usrRole = Role::where('name', 'user')->first();
        $users = array();
        for ($i=0; $i < 50; $i++) { 
            $user = new User();
            $user->name = $faker->firstName;
            $user->surname = "Usuario${i}";
            $user->email = "user${i}@user.com";
            $user->email_verified_at = now();
            $user->password = Hash::make('participes');
            $user->remember_token = Str::random(10);
            $user->save();
            $user->roles()->attach($usrRole);
            $user->save();
            $users[] = $user->id;
        }
        $organizations = array();
        for ($i=0; $i < 25; $i++) { 
            $picture = new ImageFile();
            $picture->name = 'default-avatar.png';
            $picture->size = '100';
            $picture->mime = 'image/png';
            $picture->path = 'img/default-avatar.png';
            $organization = new Organization();
            $organization->name = $faker->company;
            $organization->description = $faker->text;
            $organization->save();
            $organization->logo()->save($picture);
            $organizations[] = $organization->id;
        }

        for ($i=0; $i <= 20; $i++) { 
            $objective = new Objective();
            $category = Category::findorfail($faker->randomElement([1,2,3,4]));
            $objective->title = $faker->sentence;
            $objective->content = $faker->text(600);
            $objective->tags = $faker->randomElements(['tag1','tag2','tag3','tag4','tag5','tag6'],3);
            $objective->hidden = false;
            $objective->category()->associate($category);
            $objective->author()->associate($admin);
            $objective->save();
            $objective->organizations()->attach($faker->randomElements($organizations,3));
            
            $community = new Community();
            $community->label = '¡Unite al Telegram!';
            $community->icon = 'fas fa-headphones';
            $community->color = #0088cc;
            $community->url = 'https://google.com';
            $objective->communities()->save($community);
            for ($y=0; $y < 7; $y++) { 
                $goal = new Goal();
                $goal->title = $faker->sentence;
                $goal->status = 'ongoing';
                $goal->indicator = $faker->sentence;
                $goal->indicator_goal = $faker->numberBetween(600,1000);
                $goal->indicator_progress = $faker->numberBetween(0,600);
                $goal->indicator_unit = $faker->word;
                $goal->indicator_frequency = $faker->word;
                $goal->source = $faker->sentence;
                $goal->objective()->associate($objective);
                $goal->save();
                $theMilestones = array();
                for ($z=0; $z < 5; $z++) { 
                    $milestone = new Milestone();
                    $milestone->order = ($z+1);
                    $milestone->title = $faker->sentence;
                    $milestone->goal()->associate($goal);
                    $milestone->save();
                    array_push($theMilestones, $milestone);
                }
                # Create reports
                $howManyReports = rand(1,9);
                for($k = 0; $k < $howManyReports; $k++){
                    // Date?
                    $fromDate = Carbon::now()->subWeeks($howManyReports - $k)->toDateTimeString();
                    $toDate = Carbon::now()->subWeeks($howManyReports - ($k+1))->toDateTimeString();
                    $reportDate = $faker->dateTimeBetween($fromDate, $toDate);
                    // Continue
                    $report = new Report();
                    $reportType = $faker->randomElement(['post','progress','milestone']);
                    $report->type = $reportType;
                    $report->tags = $faker->words(3);
                    $report->title = $faker->sentence();
                    $report->content = $faker->realText(450);
                    $newStatus = $faker->randomElement(['ongoing','delayed','inactive']);
                    $report->status = $newStatus;
                    $goal->status = $newStatus;
                    $goal->save();
                    $report->date = $reportDate;
                    $auxProgressRemaining = $goal->indicator_goal - $goal->indicator_progress;
                    switch($reportType){
                        case 'post':
                            break;
                        case 'progress':
                            $reportProgress = $faker->numberBetween(0,$auxProgressRemaining);
                            $report->progress = $reportProgress;
                            $auxProgressRemaining -= $reportProgress;
                            $goal->indicator_progress += $reportProgress;
                            $goal->save();
                            break;
                        case 'milestone':
                            $theMile = array_shift($theMilestones);
                            if(!is_null($theMile)){
                                $report->milestone()->associate($theMile);
                                $theMile->completed = $reportDate;
                                $theMile->save();
                            }
                            break;
                    }
                    if($faker->boolean(60)){ // Add Localization?
                        $lat = $faker->latitude;
                        $long = $faker->longitude;
                        $report->map_lat = $lat;
                        $report->map_long = $long;
                        $report->map_zoom = 1;
                        $report->map_center = "{\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"type\":\"Point\",\"coordinates\":[{$long},{$lat}]}}";
                        $report->map_geometries = "{\"type\":\"FeatureCollection\",\"features\":[{\"id\":\"2792417c82752ae1060a24bceac6c3bc\",\"type\":\"Feature\",\"properties\":{},\"geometry\":{\"coordinates\":[{$long},{$lat}],\"type\":\"Point\"}}]}";
                    }
                    $report->created_at = $reportDate;
                    $report->updated_at = $reportDate;
                    $report->author()->associate($admin);
                    $report->goal()->associate($goal);
                    $report->save();
                }
                if($faker->boolean(30)){ // Finish Progress?
                    $fromDate = Carbon::now()->subWeeks(1)->toDateTimeString();
                    $toDate = Carbon::now()->toDateTimeString();
                    $reportDate = $faker->dateTimeBetween($fromDate, $toDate);
                    $report = new Report();
                    $report->type = 'progress';
                    $report->tags = $faker->words(3);
                    $report->title = $faker->sentence();
                    $report->content = $faker->realText(450);
                    $report->status = 'reached';
                    $report->progress = $goal->indicator_goal;
                    $report->date = $faker->date();
                    $goal->status = 'reached';
                    $goal->indicator_progress = $goal->indicator_goal;
                    $goal->save();
                    $report->created_at = $reportDate;
                    $report->updated_at = $reportDate;
                    $report->author()->associate($admin);
                    $report->goal()->associate($goal);
                    $report->save();
                }
            }
            for ($y=0; $y < 3; $y++) { 
                $objective->members()->attach($faker->randomElements($users,2), ['role' => $faker->randomElement(['manager','reporter'])]);
            }
            for ($y=0; $y < 2; $y++) { 
                $objective->subscribers()->attach($faker->randomElements($users,2));
            }
        }
    }
}
