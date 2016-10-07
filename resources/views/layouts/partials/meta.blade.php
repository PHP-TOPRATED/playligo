<meta property="og:url"                content="{{ request()->url() }}@if(!empty($url_refresh))?fbrefresh={{ $url_refresh }}@endif" />
<meta property="og:type"               content="website" />
<meta property="og:title"              content="@if(isset($og_title)){{$og_title}}@else{{ $page_title or config('playligo.title') }}@endif" />
<meta property="og:description"        content="{{ $page_desc or config('playligo.desc') }}" />
<meta property="og:image"              content="{{ $page_img or asset('img/og-img-logo.jpg') }}" />
<meta property="fb:app_id"             content="{{ env('FACEBOOK_CLIENT_ID') }}" />
