<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="css/style.css">

    <title>главная</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container">
          <div class="collapse navbar-collapse" id="navbarNav">
              <ul style="color: black; font-size: 20px; font-weight: bold" class="navbar-nav">
                  <li class="nav-item active">
                      <a  class="nav-link" href="{{route('index')}}">Главная </a>
                  </li>
                  <li class="nav-item active">
                      <a class="nav-link" href="{{route('welcome')}}">Сравнение картинок</a>
                  </li>
                  <li class="nav-item active">
                      <a class="nav-link" href="{{route('welcome.hash')}}">Сравнение картинок с хешом</a>
                  </li>
              </ul>
          </div>
      </div>
  </nav>

  <div class="container">
        <div  class="panel panel-primary">
            <div style="margin-top: 160px"  class="panel-heading">
                <h2>Сначала загрузите картинки </h2>
            </div>
            <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                    @if ($message = Session::get('error'))
                        <div class="alert alert-danger alert-block">
                            <strong>{{ $message }}</strong>
                        </div>
                    @endif

                <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Выберите Файлы:</label>
                        <input type="file" name="files[]" multiple class="form-control @error('files') is-invalid @enderror">

                        @error('files')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror

                        <label for="">Выберите категорию</label>
                        <select class="form-control" name="category_id">
                            @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-success">Отправить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



  </body>
</html>
