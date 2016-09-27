<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::model('playlist', 'App\Playlist');
Route::model('poll', 'App\Poll');
Route::model('playlist_video', 'App\PlaylistVideo');
Route::model('poll_playlist', 'App\PollPlaylist');
Route::model('user', 'App\User');

Route::group(['middleware' => 'web'], function () {
    // Route::get('/', function () {
    //     return view('welcome');
    // });

    Route::auth();
    // Route::get('/', 'HomeController@welcome');
    // Route::get('/', 'HomeController@prelaunch');
    Route::get('/', 'HomeController@index');
    Route::get('/login/{provider}', 'Auth\AuthController@getSocialAuth');
    Route::get('/login/callback/{provider}', 'Auth\AuthController@getSocialAuthCallback');
    Route::get('load_playlist/{playlist}', 'PlaylistController@loadPlaylist');
    Route::get('public_playlist', 'HomeController@allPlaylist');
    Route::get('public_playlist/latest/more', 'HomeController@latestPlaylist');
    Route::get('public_playlist/mostviewed/more', 'HomeController@mostViewedPlaylist');
//    Route::get('public_poll', 'HomeController@allPoll');
//    Route::get('public_poll/latest/more', 'HomeController@latestPoll');
//    Route::get('public_poll/mostvoted/more', 'HomeController@mostVotedPoll');
//    Route::get('public_poll/search', 'HomeController@searchPoll');
//    Route::get('public_poll/{poll}', 'HomeController@poll');
    Route::get('public_playlist/search', 'HomeController@searchPlaylist');
    Route::get('public_playlist/{pl_slug}', 'HomeController@playlistPage')->name('public_playlist.view');
    Route::get('public_playlist/popup/{pl_slug}', 'HomeController@playlistPopUp');
    Route::get('login_ajax', 'Auth\AuthController@ajaxLogin');
    Route::get('/search/preview/{id}', 'SearchController@preview');
    Route::post('/subscribe', 'HomeController@subscribe');
    Route::get('/explainer_popup', 'HomeController@explainerPopUp');
    Route::get('/about', 'PageController@about');
    Route::post('/enter_prelaunch', 'InviteController@enter');
    Route::get('/invite/{code}', 'HomeController@invite');
    Route::get('/search', 'SearchController@index');
    Route::get('/search/get_by_keyword', 'SearchController@getByKeyword');
    Route::get('/search_keywords', 'SearchController@searchKeywords');
    Route::get('/new_search_keywords', 'SearchController@displaySearchKeywords');
    Route::get('/places_of_interests', 'SearchController@getPlacesOfInterests');
    Route::get('/tours', 'SearchController@getTours');
    Route::get('/autogen', 'SearchController@autoGen');
    Route::get('/autogen', 'SearchController@autoGen');
    Route::get('/edit_playlist', 'SearchController@editDemoPlaylist')->name('playlist.demo');
    Route::post('/edit_playlist', 'SearchController@forgetDemoPlaylist');
    Route::get('/suggest_location', 'SearchController@suggestRegion');
    Route::get('/suggest_location/{region}', 'SearchController@suggestLocation');
    Route::post('feedback', 'FeedbackController@create');

    Route::group(['middleware' => 'auth'], function () {

        Route::get('/password/edit', 'Auth\PasswordController@edit');
        Route::post('/password/edit', 'Auth\PasswordController@update');
        Route::get('/profile/edit', 'UserController@editOwn');
        Route::post('/profile/edit', 'UserController@update');

        // Route::get('/search', 'SearchController@index');
        Route::get('/edit_playlist/{playlist}', 'SearchController@editPlaylist');
        Route::get('/edit_playlist/{playlist}/more', 'SearchController@editPlaylistMore');
        Route::get('/edit_playlist/load_selected/{playlist}', 'SearchController@getSelected');
        Route::post('/edit_keywords/{playlist}', 'SearchController@editKeywords');
        Route::get('/results/more', 'SearchController@resultsMore');
        // Route::get('/results', 'SearchController@results');
        // Route::get('/results/more', 'SearchController@resultsMore');
        Route::post('/sort_selected', 'SearchController@sortSelected');

        Route::post('/search/add_video', 'SearchController@add_video');
        Route::post('/search/selected/remove', 'SearchController@remove_video');
        // Route::get('/search/load_selected', 'SearchController@getSelected');

        Route::get('/playlist', 'PlaylistController@index');
        Route::post('/playlist/create', 'PlaylistController@store');
        Route::post('/playlist/publish', 'PlaylistController@publish')->name('playlist.publish');
        Route::get('/playlist/delete/{playlist}', 'PlaylistController@delete');
        Route::post('/playlist/delete', 'PlaylistController@destroy');
        Route::get('/playlist/edit/{playlist}', 'SearchController@editPlaylist');
        Route::post('/playlist/edit', 'PlaylistController@update');
        Route::get('/playlist/successful/{playlist}', 'PlaylistController@successful');
        Route::post('/playlist/sort_item', 'PlaylistController@sortItem');
        Route::get('/playlist/preview/{playlist}', 'PlaylistController@preview');

        Route::get('/playlist/video/{playlist_video}/delete', 'PlaylistVideoController@delete');
        Route::post('/playlist/video/delete', 'PlaylistVideoController@destroy');
        Route::post('/playlist/video/instant_delete', 'PlaylistVideoController@instantDestroy');
        Route::post('/playlist/video/add', 'PlaylistVideoController@store');

        Route::post('/playlist/rating/add', 'PlaylistRatingController@store');

        Route::group(['middleware' => ['role:admin']], function () {
            // dd('ss');
            Route::get('/admin', 'AdminController@index');
            Route::get('/admin/user', 'UserController@index');
            Route::get('/admin/user/popup/{user}', 'UserController@popUp');
            Route::post('/admin/user/search', 'UserController@search');
            Route::get('/admin/user/edit/{user}', 'UserController@edit');
            Route::post('/admin/user/edit', 'UserController@update');

            Route::get('/admin/playlist', 'PlaylistController@adminList');
            Route::post('/admin/playlist/search', 'PlaylistController@search');

            Route::get('/admin/poll', 'PollController@adminList');
            Route::post('/admin/poll/search', 'PollController@search');

            Route::get('/admin/feedbacks', 'AdminController@feedbacks');

            Route::get('/job/update_snippet', 'JobController@updateSnippet');
            Route::get('/job/update_thumb', 'JobController@updatePlThumb');
            Route::get('/job/update_poll_playlist_count', 'JobController@updatePollPlaylistCount');

            Route::get('/poll', 'PollController@index');
            Route::get('/poll/create', 'PollController@create');
            Route::post('/poll/create', 'PollController@store');
            Route::post('/poll/create_add', 'PollController@store_add');
            Route::get('/poll/successful/{poll}', 'PollController@successful');
            Route::get('/poll/delete/{poll}', 'PollController@delete');
            Route::post('/poll/delete', 'PollController@destroy');
            Route::get('/poll/edit/{poll}', 'PollController@edit');
            Route::post('/poll/edit', 'PollController@update');
            Route::get('/poll/add/{playlist}', 'PollController@addPlaylist');
            Route::post('/poll/add', 'PollPlaylistController@store');
            Route::post('/poll/sort_item', 'PollController@sortItem');
            Route::post('/pollplaylist/{poll_playlist}/vote', 'PollPlaylistController@storeVote');
            Route::get('/poll/playlist/{poll_playlist}/delete', 'PollPlaylistController@delete');
            Route::post('/poll/playlist/delete', 'PollPlaylistController@destroy');

            Route::group(['prefix' => 'admin/keywords'], function () {
                Route::get('/', 'KeywordsController@index')->name('admin.keywords.index');
                Route::post('/search', 'KeywordsController@search')->name('admin.keywords.search');
                Route::get('/create', 'KeywordsController@create')->name('admin.keywords.create');
                Route::post('/create', 'KeywordsController@store')->name('admin.keywords.store');
                Route::get('/edit/{keyword}', 'KeywordsController@edit')->name('admin.keywords.edit');
                Route::post('/edit/{keyword}', 'KeywordsController@update')->name('admin.keywords.update');
                Route::get('/delete/{keyword}', 'KeywordsController@delete')->name('admin.keywords.delete');
                Route::post('/delete/{keyword}', 'KeywordsController@destroy')->name('admin.keywords.destroy');
            });

        });

    });


    Route::get('/home', 'HomeController@index');

    Route::get('/embed/{pl_slug}', 'HomeController@embed')->name('playlist.embed');
});
