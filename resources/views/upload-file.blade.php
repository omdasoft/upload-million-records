<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Million Records</title>
</head>
<body>
    <form action="/upload" method="post" enctype="multipart/form-data">
        @csrf
        <label for="mycsv">Upload CSV</label>
        <input type="file" name="mycsv" id="mycsv">
        <input type="submit" value="upload">
    </form>
</body>
</html>