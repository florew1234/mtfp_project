<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPPORT</title>
   
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
            <h3 class="text-center" style="text-transform: uppercase;"><strong>{!! $titre !!}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb">Commune</th>
                                <th class="tb cent">Requêtes N.S.</th>
                                <th class="tb cent">Plaintes N.S.</th>
                                <th class="tb cent">Demandes N.S.</th>
								<th class="tb cent">Total</th>
							</tr>
						</thead>

                        @if(count($datas) != 0)
						<tbody>
                            @foreach($datas as $dt)
								<tr>
									<td class="ts" style="padding:8px" >{{$dt['Commune']}}</td>
                                    <td class="tb" >{{$dt['rns']}}</td>
									<td class="tb" >{{$dt['pns']}}</td>
									<td class="tb" >{{$dt['dns']}}</td>
									<td class="tb" >{{$dt['total']}}</td>
								</tr>
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="5"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
            <hr/>
            
            <h3 class="text-center" style="text-transform: uppercase;"><strong> {!! $titreGenReg !!}</strong></h3>
			<div>
				<div>
                
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb cent">Communes </th>
                                <th class="tb cent">TOTAL PREOCCUPATION N.T</th>
                                <th class="tb cent">PRESTATIONS CONCERNEES</th>
							</tr>
						</thead>
                        @if(count($datasPresRegi) != 0)
						<tbody>
                            @foreach($datasPresRegi as $dt)
                                @if($dt['Tplainte'] != 0 )
                                    <tr>
                                        <td class="ts" style="padding:8px" >{{$dt['commune_re']}}</td>
                                        <td class="tb" >{{$dt['Tplainte']}}</td>
                                        <td class="ts" >{!!$dt['serv']!!}</td>
                                    </tr>
                                @endif  
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="3"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
            <hr/> 
            <h3 class="text-center" style="text-transform: uppercase;"><strong> {!! $titreGen !!}</strong></h3>
			<div>
				<div>
                
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb cent">STRUCTURES</th>
                                <th class="tb cent">TOTAL PREOCCUPATION N.T</th>
                                <th class="tb cent">PRESTATIONS CONCERNEES</th>
							</tr>
						</thead>
                        @if(count($datasPresStr) != 0)
						<tbody>
                            @foreach($datasPresStr as $str)
								<tr>
									<td class="ts" style="padding:8px" >{{$str['strcuture']}}</td>
                                    <td class="tb" >{{$str['Tplainte']}}</td>
                                    <td class="ts" >{!!$str['servStr']!!}</td>
								</tr>
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="3"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
            <hr/> 
		</div>
	</div>
    <!-- <i class="text-center"><strong>NB:</strong> Ces statistiques n’intègrent pas les sollicitations par le canal des e-services.</i><br/> -->
    <h2><strong>Légende : </strong></h2>
    <table>
        <tr>
            <th class="legendepl"><strong>N.S. </strong> : Non Satisfait</th>
            <!-- <th class="legendedir"><strong>DIR </strong> : Demandes d'informations et requêtes </th> -->
        </tr>
        
</table>
    <footer class="footer">
        <div class="drag-content">
            <div class="green drag"></div>
            <div class="yellow drag"></div>
            <div class="red drag"></div>
        </div>
    </footer>
</body>
</html>