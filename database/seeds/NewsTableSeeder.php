<?php

use App\News;
use App\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class NewsTableSeeder extends Seeder
{
    /**
     * Faker instance.
     *
     * @var
     */
    private $faker;

    /**
     * Possible translations.
     *
     * @var array
     */
    private $translations = [
        'en',
        'rs',
        'de',
        'fr',
    ];

    /**
     * All users.
     *
     * @var
     */
    private $users;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->users = User::all();

        self::generateNews();
    }

    /**
     * Generates random news.
     */
    private function generateNews()
    {
        for ($i = 0; $i < 50; $i++) {
            $news = News::create([
                'user_id' => 1,
            ]);

            foreach ($this->translations as $t) {
                $news->translations()->create([
                    'lang' => $t,
                    'title' => 'Title ' . $t,
                    'body' => 'Body ' . $t,
                ]);
            }
        }
    }
}
