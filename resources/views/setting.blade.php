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

    <title>Website Menu #1</title>
  </head>
  <body>
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <a class="navbar-brand" href="{{route('index')}}">Главная</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav">
              <li class="nav-item active">
                  <a class="nav-link" href="{{route('index')}}">Главная <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item active">
                  <a class="nav-link" href="{{route('welcome')}}">Сравнение картинок </a>
              </li>
              <li class="nav-item active">
                  <a class="nav-link" href="{{route('setting')}}">Настройки</a>
              </li>

          </ul>
      </div>
  </nav>

    <div class="container">
        <div  class="panel panel-primary">
            <div style="margin-top: 160px"  class="panel-heading">
                <h2>Установите процент схожости при котором выведется картинки </h2>
            </div>
            <div>
                <form action="{{ route('settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Процент:</label>
                        <input type="number" max="99" name="percent" value="{{\App\Models\Setting::first()->percent}}"  class="form-control @error('files') is-invalid @enderror">

                        @error('files')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
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
