<!DOCTYPE html>
<html>
<head>
    <title>Сравнение картинок</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            /* display: none; <- Crashes Chrome on hover */
            -webkit-appearance: none;
            margin: 0; /* <-- Apparently some margin are still there even though it's hidden */
        }
        .count:hover {
            border: none;
        }
        .hidden {
            display: none;
        }

    </style>
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
                <a class="nav-link" href="{{route('welcome')}}">Сравнение картинок с ии</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="{{route('welcome.hash')}}">Сравнение картинок с хешом</a>
            </li>
        </ul>
    </div>
   </div>
</nav>

<div class="container" style="margin-top: 50px">
    <div class="panel panel-primary">
        @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <strong>{{ $message }}</strong>
            </div>
        @endif
        <div class="panel-heading">
            <h2>Добро пожаловать</h2>
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

                    <label for="" class="mb-2">Выберите категорию</label>
                    <select  class="form-control" name="category_id">
                        @foreach($categories as $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                        @endforeach
                    </select>
                    <label  style="margin-top: 30px" class="form-label">Выберите файл для сравнения (ИИ) </label>
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
        @if(isset($image))
        <div class="panell" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px">
            <div >
                <p><strong>Ваше изображение:</strong> {{$name}}</span></p>

                <img  src="{{ asset($image) }}" alt="Image">
            </div>
        </div>
        @endif
        @if(isset($images))
        <h3 style="margin-bottom: 20px">Похожие изображения: {{count($images)}}</h3>
            <div class="d-flex gap-3" style="margin-bottom: 20px">
                <h5>Показать первые: <input style="border: 1px solid gray; " class="count" type="number" name="show_count" value="10" max="20" min="1" > элементов</h5>
            </div>

            <div class="panell" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px">
                @foreach($images as $image)
                    <div  style="border: 2px solid #CCB399FF; border-radius: 8px; padding: 10px">
                        <p><strong>Процент схожости:</strong> {{ number_format((float)$image['percent'], 2, '.', '') }} %<br> <span><strong>Название файла:</strong> {{$image['file_name']}}</span></p>
                        <img style="max-height: 500px; max-width: 100%;" src="{{ asset($image['img']) }}" alt="Image">
                    </div>
                @endforeach
            </div>

        @endif
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const imagesContainer = document.querySelector('.panel-body');
        const showCountInput = document.querySelector('.count');

        // Инициализация при загрузке страницы
        updatePagination(parseInt(showCountInput.value, 10));

        showCountInput.addEventListener('input', function() {
            const showCount = parseInt(this.value, 10);

            updatePagination(showCount);
        });
        function updatePagination(showCount) {
            // Получите все изображения
            const allImages = document.querySelectorAll('.panell > div');
            console.log(allImages)

            // Добавьте или удалите класс для управления стилями
            for (let i = 0; i < allImages.length; i++) {
                if (i < showCount) {
                    allImages[i].classList.remove('hidden');
                } else {
                    allImages[i].classList.add('hidden');
                }
            }
        }
    });
</script>

</body>
</html>
