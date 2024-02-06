<!DOCTYPE html>
<html>
<head>
    <title>How To Upload Multiple File In Laravel 10 - Techsolutionstuff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h2>How To Upload Multiple File In Laravel 10 - Techsolutionstuff</h2>
        </div>
        <div class="panel-body">
            @if ($message = Session::get('success'))
                <div class="alert alert-success alert-block">
                    <strong>{{ $message }}</strong>
                </div>
            @endif

            <form action="{{ route('file.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Select Files:</label>
                    <input type="file" name="files[]" multiple class="form-control @error('files') is-invalid @enderror">

                    @error('files')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
