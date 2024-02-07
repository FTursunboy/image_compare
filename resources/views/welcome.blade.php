<!DOCTYPE html>
<html>
<head>
    <title>Сравнение картинок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                <a class="nav-link" href="{{route('index')}}">Главная </a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{route('welcome')}}">Сравнение картинок</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{route('setting')}}">Настройки</a>
            </li>

        </ul>
    </div>
</nav>

<div class="container" style="margin-top: 50px">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2>Можете отправить картинку</h2>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <form action="{{ route('file.compare') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Выберите Файлы:</label>
                    <input type="file" name="file" class="form-control @error('files') is-invalid @enderror">

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

    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Изображения</h3>
        </div>

        @if(isset($images))
        <h3>Похожие изображения</h3>

        <div class="panel-body">

                @foreach($images as $image)
                    <img  style="border: 1px solid black; max-height: 500px; max-width: 500px" src="{{ asset($image['img']) }}" alt="Image" >
                @endforeach


        </div> @endif
    </div>

</div>
</body>
</html>
