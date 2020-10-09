<!DOCTYPE html>
<html>
<head>
    <title>Import Excel</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<br>

<div class="container">
    <h3 align="center">Import Excel</h3>
    <br>
    <hr>

    @if(count($errors) > 0)
        <div class="alert alert-danger">
            <h4>Upload Validation Error</h4>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" enctype="multipart/form-data" action="{{ url('/excel/import') }}">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="row">
                <div class="col-xs-6"><label>Select File for Upload</label><br><span class="text-muted">.xls, .xslx</span></div>
                <div class="col-xs-3">
                    <input type="file" name="select_file" />
                </div>
                <div class="col-xs-3">
                    <input type="submit" name="upload" class="btn btn-primary" value="Upload">
                </div>
            </div>
        </div>
    </form>

    <br>

    @if($message = Session::get('success'))
        <div class="alert alert-success alert-block">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <strong>{{ $message }}</strong>
        </div>
    @endif

    @isset($import_errors)
        @if(count($import_errors) > 0)
        <div class="row">
            <div class="col-xs-12">
                <div class="alert alert-danger">
                    Upload Validation  ({{ count($import_errors) }} product(s) was not inserted)<br><br>
                    @foreach($import_errors as $import_error)
                        <ul class="list-group">
                            <li class="list-group-item active">insert #{{ $loop->iteration }}</li>
                            @foreach($import_error as $error)
                                @foreach($error as $e)
                                    <li class="list-group-item">{{ $e }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                        <br>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    @endisset

</div>
</body>
</html>
