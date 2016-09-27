<?php

namespace App\Console\Commands;

use App\Playlist;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PublishPlaylists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:publish {playlist?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishing playlist';

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
        if ($this->option('all')) {
            $playlists = Playlist::all();
            foreach ($playlists as $playlist) {
                $playlist->update(['pl_status' => true]);
            }
            $playlist_count = count($playlists);
            $this->info("Successfully published $playlist_count playlists");
        } else {
            if ($playlist_slug = $this->argument('playlist')) {
                try {
                    if ($playlist = Playlist::find($playlist_slug)) {
                    } else {
                        $playlist = Playlist::wherePlSlug($playlist_slug)->firstOrFail();
                    }
                    $playlist->update(['pl_status' => true]);
                    $this->info("Successfully published \"$playlist->pl_title\"");
                } catch (ModelNotFoundException $exception) {
                    $this->error('Playlist has not been found. Please, provide correct id or slug');
                }
            } else {
                $this->error('Playlist has not been found. Please, provide correct id or slug');
            }
        }
        return null;
    }
}
