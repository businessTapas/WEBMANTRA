<html>
    <head>
    <title>Admin Login</title>

    </head>
    <body>
        @if($errors->any())
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(Session::has('error_message'))
            <div>
                  
                        {{ Session::get('error_message') }}
                  
            </div>
        @endif

        @if(session('status'))
            <div>
                  
                        {{ session('status') }}
                  
            </div>
        @endif
        <form action="{{ route('admin.postlogin') }}" method="post">
        @csrf
            <input type="email" name="email">
            <input type="password" name="password" >
            <input type="submit" value="Submit">
        </form>
    </body>
</html>