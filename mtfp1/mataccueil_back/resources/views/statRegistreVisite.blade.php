<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LISTE DES REGISTRES</title>
   
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
            padding-left:6px;
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
            <td style="width:40%; ">
            <strong>MTFP</strong>
            </td>
            <td style="width:30%; text-align:center;">
            </td>
            <td style="width:30%;">
                <ul class="address">
                    <li>01 BP 907 Cotonou</li>
                    <li>BENIN</li>
                    <li>TEL: +229 21 30 25 70</li>
                    <li>travail.infos@gouv.bj</li>
                    <li>www.travail.gouv.bj</li>
                </ul>
            </td>
        </tr>
	</table>
    <div class="limiter">
        <div class="container-table100">
            <h3 class="text-center" style="text-transform: uppercase;">{!! $titre !!}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb cent">Date Enreg.</th>
                                <th class="tb cent">Nature visite</th>
                                <th class="tb cent">Matricule / Téléphone</th>
								<th class="tb cent">Nom et prénom(s)</th>
								<th class="tb cent">Sigle Ministère</th>
								<th class="tb cent">Préoccupation</th>
								<th class="tb cent">Satisfait (Oui / Non)</th>
								<th class="tb cent">Motif</th>
								<th class="tb cent">Acteur</th>
							</tr>
						</thead>
                        @if(count($data) != 0)
						<tbody>
                            @foreach($data as $dt)
								<tr>
									<td class="ts" >{{date('d/m/Y H:i:s', strtotime($dt->created_at))}}</td>
                                    <td class="ts" >
                                        @if($dt['plainte'] == 0)
                                            Requête
                                        @elseif($dt['plainte'] == 1)
                                            Plainte
                                        @elseif($dt['plainte'] == 2)
                                            Demande d'information
                                        @endif
                                    </td>
									<td class="ts" >{{$dt['matri_telep']}}</td>
									<td class="tb" >{{$dt['nom_prenom']}}</td>
									<td class="ts">
                                        @if($dt['entite'])
                                            {{$dt['entite']['sigle']}}
                                        @else
                                            --
                                        @endif
                                    </td>
                                    <td class="tb">{{$dt['contenu_visite']}}</td>
                                    <td class="tb">
                                        @if($dt['satisfait'] == "oui")
                                            Oui
                                        @elseif($dt['satisfait'] == "non")
                                            Non
                                        @endif
                                    </td>
                                    <td class="tb" >{{$dt['observ_visite']}}</td>
                                    <td class="tb" >
                                        @if($dt['creator']['agent_user'])
                                            {{$dt['creator']['agent_user']['nomprenoms']}}
                                        @else -- @endif
                                    </td>
                                </tr>
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="9"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
            
              
		</div>
	</div>
    <br>
        
    <footer class="footer">
        <div class="drag-content">
            <div class="green drag"></div>
            <div class="yellow drag"></div>
            <div class="red drag"></div>
        </div>
    </footer>
</body>
</html>