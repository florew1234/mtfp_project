<!doctype html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">

</head>
<body class="bg-color">
<header>
    <div class="d-flex justify-content-between">
        <figure class="figure p-3 align-self-center">
            <img src="https://demarchesmtfp.gouv.bj/images/logo-mtfp.svg" class="figure-img img-fluid rounded" alt="..."  >
        </figure>
        <ul class="list-unstyled p-3 text-right" style="font-size: 11px">
            <li> 01 BP 907 COTONOU</li>
            <li class="">BENIN</li>
            <li>Tél: + 229 21 30 25 70</li>
            <li>travail.infos@gouv.bj</li>
            <li>www.travail-gouv.bj</li>
        </ul>
    </div>
</header>

<div class="container-fluid m-0 p-0">
    <h3 class="text-center"><u>Points des doléances des retraités du {{$data["date_start"]}}</u></h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped primary" cellspacing="0" width="100%" style="font-size: 10px" >
            <thead>
            <tr>
                <th width="1%">Numéro d'ordre</th>
                <th width="7%">Matricule</th>
                <th width="15%">Nom et Prénoms</th>
                <th width="10%">Ministère ou Institution de départ</th>
                <th width="10%">Année de départ</th>
                <th width="15%">Contact / Contact Proche</th>
                <th width="10%">Localité</th>
                <th width="30%">Préoccupation</th>
            </tr>

            </thead>
           <tbody>
            @if($data["data"]->count() ==0)
                <tr >
                    <td colspan="8" class="text-center"> Aucune donnée à afficher </td>
                </tr>
            @else
                @foreach($data["data"] as $req)

                    <tr class="">
                        <td>{{$loop->index+1}} </td>
                        <td>
                            {{$req->matricule}}
                        </td>
                        <td>{{$req->identity}} </td>
                        <td>{{$req->entity_name}} </td>
                        <td>{{$req->out_year}} </td>
                        <td>{{$req->contact." ".$req->contact_proche}}</td>
                        <td>{{$req->locality}}</td>
                        <td>{{$req->msgrequest}}</td>
                    </tr>

                @endforeach
            @endif


            </tbody>
        </table>

    </div>

</div>
</body>
</html>
