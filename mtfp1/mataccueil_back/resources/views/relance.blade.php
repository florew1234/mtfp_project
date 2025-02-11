<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAPPORT PREOCCUPATION NON TRAITEE</title>
   
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
            <h3 class="text-center" style="text-transform: uppercase;"><strong>POINT DES PREOCCUPATIONS NON TRAITEES </strong><br/> à la date du {{$periode}}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb">STRUCTURES</th>
								<th class="tb cent">PREO.N.T</th>
								<th class="tb cent">PREO.N.T - H.D.</th>
                                <th class="tb cent">TOTAL</th>
							</tr>
						</thead>
                        @if(count($datas) != 0)
						<tbody>
                            @foreach($datas as $dt)
                                @if($dt['total'] != 0 )
                                    <tr>
                                        <td class="ts" style="padding:8px" >{{$dt['strcuture']}}</td>
                                        <td class="tb" >{{$dt['preont']}}</td>
                                        <td class="tb" >{{$dt['preonthd']}}</td>
                                        <td class="tb" >{{$dt['total']}}</td>
                                    </tr>
                                @endif
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="6"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
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
            <th class="legendepl"><strong>PREO. N.T </strong> : PREOCCUPATION NON TRAITEE </th>
        </tr>
        <tr>
            <th class="legendepl"><strong>PREO. N.T - H.D. </strong> : PREOCCUPATION NON TRAITEE et HORS DELAI</th>
        </tr>
    </table>
{{dd('ddddddd')}}
    <footer class="footer">
        <div class="drag-content">
            <div class="green drag"></div>
            <div class="yellow drag"></div>
            <div class="red drag"></div>
        </div>
    </footer>
</body>
</html>