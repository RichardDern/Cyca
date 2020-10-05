@extends('layouts.account')

@section('content')
    <div id="about">
        <h2>Cyca <small>@version('compact')</small></h2>

        <p>{{ __('Created by') }} Richard Dern.</p>

        <ul>
            <li><a href="https://www.getcyca.com" target="_blank">{{ __("Official website") }}</a></li>
            <li><a href="https://github.com/RichardDern/Cyca" target="_blank" rel="noopener noreferrer">{{ __("GitHub repository") }}</a></li>
        </ul>

        <p>
            {{ __("If you like Cyca, maybe you could consider donating") }}:
        </p>

        <ul>
            <li><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GFZ3SAY3Y8NAS">{{ __("Via PayPal") }}</a></li>
            <li><a href="https://www.buymeacoffee.com/richarddern">{{ __("Via Pay me a coffee") }}</a></li>
        </ul>

        <p>{{ __("Thank you for using Cyca !") }}</p>

        <h3>{{ __("Licenses") }}</h3>

        <p>{!! __("Cyca is a Free and Open Source Software released under the :license license in its most recent version.", [
            "license" => '<a href="https://www.gnu.org/licenses/gpl.html" rel="noopener noreferrer" target="_blank">GNU GPL</a>'
        ]) !!}<p>

        <p>{{ __("Software used by Cyca can be released under different licenses. Please see below for more informations.") }}</p>

        <textarea readonly rows="10">{{ file_get_contents(public_path('/js/app.js.LICENSE.txt')) }}</textarea>
    </div>
@endsection
