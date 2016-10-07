<?php

namespace App\Console\Commands;

use App\Playlist;
use Illuminate\Console\Command;
use Youtube;

class PlaylistVideoKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playlist:video {--keyword}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets keyword to playlist video';

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
        if ($this->option('keyword')) {
            $playlists = Playlist::all();
            $playlist_bar = $this->output->createProgressBar(count($playlists));
            $playlist_bar->setMessage('Start processing playlists');
            $videos_total_count = 0;
            $videos_updated_count = 0;
            foreach ($playlists as $playlist) {
                $keys = $playlist->keys->lists('plk_key');
                $playlist_videos = $playlist->videos;
                $videos_total_count += count($playlist_videos);
                if (!$keys) {
                    continue;
                } elseif (!$playlist_videos) {
                    continue;
                }
                foreach ($keys as $key) {
                    $keyword_videos_set = self::fetchVideosByKeyword($playlist->pl_location, $key);
                    if (!$keyword_videos_set) {
                        continue;
                    }
                    $video_ids = [];
                    foreach ($keyword_videos_set as $value) {
                        array_push($video_ids, $value->id->videoId);
                    }
                    foreach ($playlist_videos as $playlist_video) {
                        if ($playlist_video->keyword == '' && in_array($playlist_video->plv_video_id, $video_ids)) {
                            $playlist_video->update(['keyword' => $key]);
                            $videos_updated_count += 1;
                        }
                    }
                }
                $playlist_bar->advance();
                $playlist_bar->setFormat('%current%/%max% playlists processed [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%' . PHP_EOL);
            }
            $playlist_bar->finish();
            $this->info("$videos_updated_count/$videos_total_count were updated");
        }
    }

    public static function fetchVideosByKeyword($location, $key)
    {
        $params = [
            'q'             => $location . ' ' . $key,
            'type'          => 'video',
            'part'          => 'id, snippet',
            'videoDuration' => 'short',
            'safeSearch'    => 'strict',
            'maxResults'    => 50
        ];

        $key_result = Youtube::searchAdvanced($params);

        return $key_result;
    }
}
