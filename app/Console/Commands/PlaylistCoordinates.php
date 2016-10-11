<?php

namespace App\Console\Commands;

use App\Playlist;
use GoogleMaps;
use Illuminate\Console\Command;

class PlaylistCoordinates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:coordinates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds coordinates to all empty playlists';

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
        $playlists = Playlist::whereNull('coordinates')->get();
        $playlist_count = count($playlists);
        foreach ($playlists as $playlist) {
            $geocoding = GoogleMaps::load('geocoding')
                ->setParamByKey('address', $playlist->pl_location)
                ->get('results.geometry.location');
            if (count($geocoding['results']) > 0) {
                $coordinates = $geocoding['results'][0]['geometry']['location'];
                $playlist->update(['coordinates' => $coordinates]);
            }
        }
        $this->info("Successfully updated coordinates for $playlist_count playlists");
    }
}
