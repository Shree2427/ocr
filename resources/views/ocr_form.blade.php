<!DOCTYPE html>
<html>
<head>
    <title>Upload Image</title>
</head>
<body>
    <form action="{{ url('/process-image') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" required>
        <button type="submit">Submit</button>
    </form>
</body>
</html>
