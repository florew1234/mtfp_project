<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTFP</title>
   
    <div id="footer">
    <i> Page <span class="pagenum"></span> </i>
    </div>
    <style>
        hr{page-break-after: always;}
            /* FOOTER */
            #footer {width: 100%;text-align: right;position: fixed;}
            #footer {bottom: -15px;}
            .pagenum:before {content: counter(page);}
        /* ENTETE */
        
        .container{
            margin-left:5rem;
            margin-right:5rem;
            margin-bottom:2rem;
        }
        .table{
            width:100%;
            margin-top:-10px;
            margin-left:-20px;
            margin-right:-20px; 
            margin-bottom:50px;
            border-collapse:collapse;

        }
        .td,.th{
            border: 1 solid black;
        }
        .right {
            float: right;
            }
            .left {
            float: left;
            }
            .address{
                list-style: none;
            }
            .address li{
                text-align:right
            }

            .header-img{
                height:50px;
            }
            .green{
                background-color:green;
            }
            .yellow{
                background-color:yellow;
            }
            .red{
                background-color:red;
            }
            .drag{
                width: 100px;
                height:10px;
                box-sizing: border-box;
            }
            .drag-content{
                text-align:center
            }
            .drag-content .drag{
                display:inline-block;
            }
            .header {
                height:150px;
                font-size:12px;
                }
            .footer {
                /* margin-left:30%; */
                margin-top:5rem;
                margin-bottom:5rem;
                height:50px;
            }
            .page-break {
                page-break-after: always;
            }
            .text-center{
                text-align:center
            }
            @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
        
        .tb {
            border: 1px solid black;
            text-align:center;
            border-collapse: collapse;
        } 
        .ts {
            border: 1px solid black;
            text-align:left;
            border-collapse: collapse;
        } 
        .cent{
            background-color : #cccccb;
        }
                
        #oa {
            height: 30px;
            width: 30px;
            background: #92d050;
            -ms-transform: rotate(45deg); /* Internet Explorer */
            -moz-transform: rotate(45deg); /* Firefox */
            -webkit-transform: rotate(45deg); /* Safari et Chrome */
            -o-transform: rotate(45deg); /* Opera */
        }
        #cp1 {
            height: 30px;
            width: 30px;
            background: #ffff00;
            -ms-transform: rotate(45deg); /* Internet Explorer */
            -moz-transform: rotate(45deg); /* Firefox */
            -webkit-transform: rotate(45deg); /* Safari et Chrome */
            -o-transform: rotate(45deg); /* Opera */
        }
        #cp2 {
            height: 30px;
            width: 30px;
            background: #ffc000;
            -ms-transform: rotate(45deg); /* Internet Explorer */
            -moz-transform: rotate(45deg); /* Firefox */
            -webkit-transform: rotate(45deg); /* Safari et Chrome */
            -o-transform: rotate(45deg); /* Opera */
        }
        #cp3 {
            height: 30px;
            width: 30px;
            background: #ff0000;
            -ms-transform: rotate(45deg); /* Internet Explorer */
            -moz-transform: rotate(45deg); /* Firefox */
            -webkit-transform: rotate(45deg); /* Safari et Chrome */
            -o-transform: rotate(45deg); /* Opera */
        }
        .legendepl{
            text-align:left;
        }
        .legendedir{
            text-align:left; 
            padding-left:30;
        }
        .cache_balise{
            display:none;
        }
    </style>
</head>
<body>
    <table style="width:100%; margin-top:-10px; margin-left:-20px; margin-right:-20px; ">
        <tr>
            <td style="width:40%;">
                <img src="https://api.mataccueil.gouv.bj/img/logo-mtfp.png" class="header-img" alt=""/>
            </td>
            <td style="width:30%; text-align:center;">
            </td>
            <td style="width:30%;">
                <!-- <ul class="address">
                    <li>01 BP 907 Cotonou</li>
                    <li>BENIN</li>
                    <li>TEL: +229 21 30 25 70</li>
                    <li>travail.infos@gouv.bj</li>
                    <li>www.travail.gouv.bj</li>
                </ul> -->
            </td>
        </tr>
	</table>

    <h2 class="text-dark fw-bold">Vue d'ensemble</h2>
    <table class="table" style="">
                    <thead>
                        <th class="th">Libellé</th>
                        <th class="th">Valeur</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="td">Total des préoccupations reçues</td>
                            <td class="td"> {{$data['all'] ?? ""}}</td>
                        </tr>
                        <!-- <tr>
                            <td class="td">Total des préoccupations traitées</td>
                            <td class="td">{{$data['treated'] ?? ""}}</td>
                        </tr> -->
                        <tr>
                            <td class="td">Total des préoccupations en attente</td>
                            <td class="td">{{$data['pending'] ?? ""}} soit {{ $data['pending_pourcent'] ?? ""}}</td>
                        </tr>
                        <tr>
                            <td class="td">Nombre de préoccupation ayant fait l'objet de notation</td>
                            <td class="td">{{$data['note_all'] ?? ""}}</td>
                        </tr>
                        <tr>
                            <td class="td">Moyenne des notes enregistrées </td>
                            <td class="td">{{$data['moy'] ?? ""}}</td>
                        </tr>
                    </tbody>
                </table>
                <table class="table"  style="">
                    <thead>
                        <th class="th">5 plus anciens logs sectoriels au système</th>
                    </thead>
                    <tbody>
                    @foreach($data1['last_sectoriels'] as $d)

                        <tr >
                           <td class="td">
                            <dl>
                                <dt>{{$d['user']['email']}}</dt>
                                <dt>{{$d['user']['agent_user']['nomprenoms'] ?? ""}}</dt>
                                <dt>{{$d['user']['entity']['sigle']}}</dt>
                                <dd>Date dernière connexion {{$d['last_login']}}</dd>
                            </dl>
                           </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <table class="table"  style="">
                    <thead>
                        <th class="th">5 plus anciens logs PF au système</th>
                    </thead>
                    <tbody>
                        @foreach($data1['last_pfc'] as $d)
                        <tr >
                           <td class="td">
                            <dl>
                                <dt>{{$d['user']['email']}}</dt>
                                <dt>{{$d['user']['agent_user']['nomprenoms']}}</dt>
                                <dt>{{$d['user']['entity']['sigle']}}</dt>
                                <dd>Date dernière connexion {{$d['last_login']}}</dd>
                            </dl>
                           </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
    <h2 class="text-dark fw-bold">Performance des structures </h2>
    <div class="bg-white mb-3 p-3">
        <table class="table mb-3">
            <thead>
                <th class="th">Libéllé</th>
                <th class="th">Valeur</th>
            </thead>
            <tbody>
                <tr>
                    <td class="td">Structures ayant le plus fort taux de préoccupations non traitées </td>
                    <td class="td">
                        <dl>
                            <dt>Entité: {{$data2['high_pending']['entite'] ?? ""}}</dt>
                            <dt>Direction: {{$data2['high_pending']['direction'] ?? ""}}</dt>
                            <dt>Non traitées: {{$data2['high_pending']['nontraitee'] ?? ""}}</dt>
                        </dl>
                    </td>
                </tr>
                <tr>
                    <td class="td">Structures et prestations ayant obtenu les plus faibles notes.</td>
                    <td class="td">
                        @forelse($elements as $el)
                        <dl >
                            <dt>Structure: {{$el["structure"]}}</dt>
                            <dt>Prestation: {{$el["prestation"]}}</dt>
                            <dt>Note: {{$el["note"]}}</dt>
                        </dl>
                           @empty

                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                       
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr>
                    <th class="th" rowspan="2"></th>
                    <th class="text-center th" colspan="2">Semaine écoulé</th>
                    <th class="text-center th" colspan="2">Total</th>
                </tr>
                <tr>
                    <th class="th">Plus élevé</th>
                    <th class="th">Plus Faible</th>
                    <th class="th">Plus élevé</th>
                    <th class="th">Plus Faible</th>
                </tr>
            </thead>

            <tbody>
                @foreach(  $data3 as $d)
                <tr >
                    <td class="td">{{$d['name']}}</td>
                    <td class="td">
                        <ol>
                        @forelse( $d['max_last_week'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @empty

                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                        </ol>
                    </td>
                    <td class="td">
                    <ol>
                        @forelse( $d['min_last_week'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @empty
                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                        </ol>
                           
                      
                    </td>
                    <td class="td">
                    <ol>
                        @forelse( $d['max_total'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @empty
                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                        </ol>
                           
                      
                    </td>
                    <td class="td">
                    <ol>
                        @forelse( $d['min_total'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @empty
                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                        </ol>
                           
                       
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <footer class="footer">
        <div class="drag-content">
            <div class="green drag"></div>
            <div class="yellow drag"></div>
            <div class="red drag"></div>
        </div>
    </footer>
</body>
</html>