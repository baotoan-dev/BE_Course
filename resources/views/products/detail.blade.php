<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .product {
            border: 1px solid #ccc;
            margin-bottom: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="product">
        <h2>{{ $product->name }}</h2>
        <p>{{ $product->price }}</p>
    </div>
</body>
</html>