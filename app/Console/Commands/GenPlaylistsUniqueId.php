<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use App\Playlist;
use Illuminate\Console\Command;

class GenPlaylistsUniqueId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlists:slug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates unique slug for all playlists in database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $playlists = Playlist::all();
        foreach ($playlists as $playlist) {
            $pl_slug = '';
            do {
                $pl_slug = Helper::generateRandomString();
            } while (Playlist::wherePlSlug($pl_slug)->count() != 0);
            $playlist->update(['pl_slug' => $pl_slug]);
        }
        $playlist_count = count($playlists);

        $this->info("Successfully generated unique slugs for $playlist_count playlists");
        return null;
    }
}
