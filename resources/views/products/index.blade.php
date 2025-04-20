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
        li {
            list-style: none;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <ul>
        @foreach ($products as $product)
            <div class="product">
                <li>{{ $product->name }}</li>
                <l1>{{ $product->price }}</l1>
            </div>
        @endforeach
    </ul>
</body>
</html>