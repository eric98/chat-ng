<h1>{{ $chat->name }}</h1>

@foreach ($chat->messages as $missatge)
        <div class="container">
                <b>{{$missatge->user->name}}</b>
                <p>{{$missatge->body}}</p>
                <span class="time-right">{{Carbon\Carbon::parse($missatge->created_at)->formatLocalized('%A %d %B %Y at %H:%m')}}</span>
        </div>
@endforeach

<style>
        .container {
                border: 2px solid #dedede;
                background-color: #f1f1f1;
                border-radius: 5px;
                padding: 10px;
                margin: 10px 0;
        }

        .container::after {
                content: "";
                clear: both;
                display: table;
        }

        .time-right {
                float: right;
                color: #aaa;
        }
</style>
