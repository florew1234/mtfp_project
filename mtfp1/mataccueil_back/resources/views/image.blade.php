



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$titre}}</title>
   
    <style>
        .header-img{
            width: 800px;
            height:550px;
        }
        .text-center{
            text-align:center
        }
    </style>
</head>
<body>
    @if($nameimg != "")
        <img src="{{storage_path('app/public/registre/'.$nameimg)}}" class="header-img" alt=""/>
    @else
        <h3 class="text-center" style="text-transform: uppercase;"><strong>{{$infos}}</strong></h3>
    @endif
</body>
</html>