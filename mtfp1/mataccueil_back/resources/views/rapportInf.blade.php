<!DOCTYPE html>
<html lang="fr">
<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>RAPPORT</title>
    
    <div id="footer">
    <i> Page <span class="pagenum"></span> </i>
    </div>
    <style>
            /* FOOTER */
            #footer {width: 100%;text-align: right;position: fixed;}
            #footer {bottom: 0px;}
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
                        /* margin-left:35%; */
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
    </style>
</head>
<body>
   
    <table style="width:100%; margin-top:-10px; margin-left:-20px; margin-right:-20px; ">
        <tr>
            <td style="width:40%; ">
            <img src="/img/logo-mtfp.svg" class="header-img" alt=""/>
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
<div>
    <div>
            <h3 class="text-center"><strong>Suivi du traitement des demandes d'information </strong><br/> à la date du {{$periode}}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb" >STRUCTURES</th>
								<th class="tb cent" >EN COURS</th>
								<th class="tb cent" >EN COURS MAIS HORS DELAI</th>
								<th class="tb cent" >% EN COURS HORS DELAI</th>
								<th class="tb cent" >TRAITEES</th>
								<th class="tb cent" >TRAITEES MAIS HORS DELAI</th>
								<th class="tb cent" >% TRAITEES HORS DELAI</th>
								<th class="tb cent" >TOTAL</th>
							</tr>
						</thead>
						<tbody>
                            
                            @foreach($datas as $dt)
								<tr>
									<td class="ts" style="padding:8px" >{{$dt['structure']}}</td>
									<td class="tb">{{$dt['en_cours']}}</td>
									<td class="tb">{{$dt['en_cours_hd']}}</td>
                                    @if(round($dt['en_cours_hd']) + round($dt['en_cours']) == 0)
									    <td class="tb">-</td>
                                    @else
                                        <td class="tb" style="background-color : <?php 
                                        if ($dt['pourcent_EnCourshd'] == 0) {
                                            echo '#92d050';
                                        }elseif($dt['pourcent_EnCourshd'] < 26) {
                                            echo '#ffff00';
                                        }elseif($dt['pourcent_EnCourshd'] < 51) {
                                            echo '#ffc000';
                                        }elseif($dt['pourcent_EnCourshd'] <= 100) {
                                            echo '#ff0000';
                                        } ?>;">{{$dt['pourcent_EnCourshd']}}</td>
                                    @endif
									<td class="tb">{{$dt['traites']}}</td>
									<td class="tb">{{$dt['traites_hd']}}</td>
                                    @if(round($dt['traites']) + round($dt['traites_hd']) == 0)
									    <td class="tb">-</td>
                                    @else
                                        <td class="tb" style="background-color : <?php 
                                        if ($dt['pourcent_Traitehd'] == 0) {
                                            echo '#92d050';
                                        }elseif($dt['pourcent_Traitehd'] < 26) {
                                            echo '#ffff00';
                                        }elseif($dt['pourcent_Traitehd'] < 51) {
                                            echo '#ffc000';
                                        }elseif($dt['pourcent_Traitehd'] <= 100) {
                                            echo '#ff0000';
                                        } ?>;">{{$dt['pourcent_Traitehd']}}</td>
                                    @endif
									<td class="tb cent">{{$dt['total']}}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
                    <br/>
                    <br/>
                    <br/>

                    <table style="width : 45%;">
                        <tbody>
                            <tr class="text-align"><th colspan="2"><h2>Légende</h2></th></tr> 
                            <tr>
                                <th style="width:5%"><div id="oa"></div></th>
                                <th style="width:95%">Objectif atteint</th>
                            </tr>
                            <tr>
                                <th style="width:5%"><div id="cp1"></div></th>
                                <th style="width:95%">Contre performance de niveau 1 (Faible)</th>
                            </tr>
                            <tr>
                                <th style="width:5%"><div id="cp2"></div></th>
                                <th style="width:95%">Contre performance de niveau 2 (Moyen)</th>
                            </tr>
                            <tr>
                                <th style="width:5%"><div id="cp3"></div></th>
                                <th style="width:95%">Contre performance de niveau 3 (Critique)</th>
                            </tr>
                        </tbody>
                    </table>
				</div>
			</div>
		</div>
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