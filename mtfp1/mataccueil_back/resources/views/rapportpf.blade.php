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
            padding-left: 5px;
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
    </style>
</head>
<body>
    <table style="width:100%; margin-top:-10px; margin-left:-20px; margin-right:-20px; ">
        <tr>
            <td style="width:40%; ">
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
            <h3 class="text-center " style="text-transform: uppercase;"><strong>Statistiques des points focaux communaux (P.F.C.) </strong><br/> du {{$periode}}</h3>
            <div >
                <div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
                        <thead>
                            <tr>
                                <th class="tb">Point focal communal</th>
                                <th class="tb cent">TOTAL DIR</th>
                                <th class="tb cent">DIR. T</th>
                                <th class="tb cent">DIR. N.T</th>
                                <th class="tb cent">% DIR. N.T</th>
                                <th class="tb cent">OBSERVATIONS</th>
                                <th class="tb cent">TOTAL PL</th>
                                <th class="tb cent">PL. T</th>
                                <th class="tb cent">PL. N.T</th>
                                <th class="tb cent">% PL. N.T</th>
                                <th class="tb cent">OBSERVATIONS</th>
                            </tr>
                        </thead>
                        @if(count($dataspfday) != 0)
                            <tbody>
                                @foreach($dataspfday as $dtp)
                                    <tr>
                                        <td class="ts" style="padding:8px" >{{$dtp['nomcom']}}</td>
                                        <td class="tb" >{{$dtp['TotalDir_']}}</td>
                                        @if(round($dtp['TotalDir_']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['Totaldir_Tr']}}</td>
                                            <td class="tb" >{{$dtp['Totaldir_NTr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentDIR_NTr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentDIR_NTr'] > 25) {
                                                    $obspd = "Moyennement critique";
                                                }elseif($dtp['pourcentDIR_NTr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentDIR_NTr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentDIR_NTr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                        <td class="tb" >{{$dtp['TotalPL_']}}</td>
                                        @if(round($dtp['TotalPL_']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['TotalPL_Tr']}}</td>
                                            <td class="tb" >{{$dtp['TotalPL_NTr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentPL_NTr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentPL_NTr'] > 25) {
                                                    $obspd = "Moyennement critique";
                                                }elseif($dtp['pourcentPL_NTr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentPL_NTr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentPL_NTr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tr><th colspan="9"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
                    </table>
                    <br/>
                    <br/>
                </div>
            </div>
            <hr/>
            <h3 class="text-center " style="text-transform: uppercase;"><strong>Statistiques des points focaux communaux (P.F.C.) </strong><br/> à la date du {{$periode}}</h3>
            <div >
                <div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
                        <thead>
                            <tr>
                                <th class="tb">Point focal communal</th>
                                <th class="tb cent">TOTAL DIR</th>
                                <th class="tb cent">DIR. T</th>
                                <th class="tb cent">DIR. N.T</th>
                                <th class="tb cent">% DIR. N.T</th>
                                <th class="tb cent">OBSERVATIONS</th>
                                <th class="tb cent">TOTAL PL</th>
                                <th class="tb cent">PL. T</th>
                                <th class="tb cent">PL. N.T</th>
                                <th class="tb cent">% PL. N.T</th>
                                <th class="tb cent">OBSERVATIONS</th>
                            </tr>
                        </thead>
                        @if(count($dataspf) != 0)
                            <tbody>
                                @foreach($dataspf as $dtp)
                                    <tr>
                                        <td class="ts" style="padding:8px" >{{$dtp['nomcom']}}</td>
                                        <td class="tb" >{{$dtp['TotalDir_']}}</td>
                                        @if(round($dtp['TotalDir_']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['Totaldir_Tr']}}</td>
                                            <td class="tb" >{{$dtp['Totaldir_NTr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentDIR_NTr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentDIR_NTr'] > 25) {
                                                    $obspd = "Moyennement critique";
                                                }elseif($dtp['pourcentDIR_NTr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentDIR_NTr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentDIR_NTr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                        <td class="tb" >{{$dtp['TotalPL_']}}</td>
                                        @if(round($dtp['TotalPL_']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['TotalPL_Tr']}}</td>
                                            <td class="tb" >{{$dtp['TotalPL_NTr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentPL_NTr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentPL_NTr'] > 25) {
                                                    $obspd = "Moyennement critique";
                                                }elseif($dtp['pourcentPL_NTr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentPL_NTr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentPL_NTr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tr><th colspan="9"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
                    </table>
                    <br/>
                    <br/>
                </div>
            </div>
            <hr/>
            <h3 class="text-center " style="text-transform: uppercase;"><strong>STATISTIQUES DES VISITES DES CCSP ET DES GSRU </strong><br/> PERIODE DU 01/06/2022 AU {{$periode}}</h3>
            <div>
                <div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
                        <thead>
                            <tr>
                                <th class="tb">Communes</th>
                                <th class="tb cent">N.V.</th>
                                <th class="tb cent">TOTAL DIR</th>
                                <th class="tb cent">DIR. S</th>
                                <th class="tb cent">DIR. N.S</th>
                                <th class="tb cent">% DIR. N.S</th>
                                <th class="tb cent">OBSER.</th>
                                <th class="tb cent">TOTAL PL</th>
                                <th class="tb cent">PL. S</th>
                                <th class="tb cent">PL. N.S</th>
                                <th class="tb cent">% PL. N.S</th>
                                <th class="tb cent">OBSER.</th>
                            </tr>
                        </thead>
                        @if(count($dataspfrevi) != 0)
                            <tbody>
                                @foreach($dataspfrevi as $dtp)
                                    <tr>
                                        <td class="ts" style="padding:8px" >{{$dtp['nomcomr']}}</td>
                                        <td class="tb" >{{$dtp['TotalDir_r'] + $dtp['TotalPL_r']}}</td>
                                        <td class="tb" >{{$dtp['TotalDir_r']}}</td>
                                        @if(round($dtp['TotalDir_r']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['Totaldir_Trr']}}</td>
                                            <td class="tb" >{{$dtp['Totaldir_NTrr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentDIR_NTrr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentDIR_NTrr'] > 25) {
                                                    $obspd = "Moy. critique";
                                                }elseif($dtp['pourcentDIR_NTrr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentDIR_NTrr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentDIR_NTrr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                        <td class="tb" >{{$dtp['TotalPL_r']}}</td>
                                        @if(round($dtp['TotalPL_r']) == 0)
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                            <td class="tb">-</td>
                                        @else
                                            <td class="tb" >{{$dtp['TotalPL_Trr']}}</td>
                                            <td class="tb" >{{$dtp['TotalPL_NTrr']}}</td>
                                            <td class="tb" style="background-color : <?php 
                                                $obspd = "";
                                                if ($dtp['pourcentPL_NTrr'] > 50) {
                                                    $obspd = "Critique";
                                                }elseif($dtp['pourcentPL_NTrr'] > 25) {
                                                    $obspd = "Moy. critique";
                                                }elseif($dtp['pourcentPL_NTrr'] > 0) {
                                                    $obspd = "Acceptable";
                                                }elseif($dtp['pourcentPL_NTrr'] == 0) {
                                                    $obspd = "Objectif atteint";
                                                } ?>;">{{round($dtp['pourcentPL_NTrr'],2)}}
                                            </td>
                                            <td class="tb" >{{$obspd}}</td>
                                        @endif
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <tr><th colspan="9"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
                    </table>
                    <br/>
                    <br/>
                </div>
            </div>
            <hr/>
            <h3 class="text-center" style="text-transform: uppercase;"><strong>Détails des requêtes traitées par points focaux communaux (P.F.C.)  </strong><br/> du {{$peri}}</h3>
			<div>
				<div>
                        @if(count($dataspfday) != 0)
                            @foreach($dataspfday as $dt)
                                <h3>{{$dt['nomcom']}} - {{$dt['commune']}}</h3>
                                <!-- Ajouter les détails des infos  -->
                                <?php 
                                $detail = \App\Http\Controllers\ServiceController::Detail_Requete($dt['idcom']);?>
                                @if(count($detail) != 0)
                                <table class="tb" style="border : 1px solid black; width : 100%;">
                                    <thead>
                                        <tr>
                                            <th class="tb">Date Enreg.</th>
                                            <th class="tb cent">Prestations</th>
                                            <th class="tb cent">Objet</th>
                                            <th class="tb cent">Type req.</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach($detail as $deta)
                                            <tr>
                                                <td class="ts" style="padding:8px" >{{date('d/m/Y H:i:s', strtotime($deta->created_at))}}</td>
                                                <td class="ts" >{{$deta->objet}}</td>
                                                <td class="ts" >{{\App\Http\Controllers\ServiceController::LibelleService($deta->idPrestation)}}</td>
                                                <td class="tb" >
                                                    @if($deta->plainte == 0) Requête
                                                    @elseif($deta->plainte == 1) Plainte
                                                    @elseif($deta->plainte == 2) Demande d'information
                                                    @else -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>        
                                @else
                                    <h5 class="text-center"><i>Aucune requête trouvée</i></h5>                  
                                @endif   
                            @endforeach
                        @else
                            <h3>Aucune information trouvée</h3>                            
                        @endif 
                                
				</div>
			</div>
            <hr/>  
            <h3 class="text-center" style="text-transform: uppercase;"><strong>Suivi du traitement des requêtes (DIR. P.) non traitées du MTFP </strong><br/> du {{$peri}} avec les prestations concernées</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb">STRUCTURES</th>
                                <th class="tb cent">TOTAL REQUETE N.T</th>
                                <th class="tb cent">PRESTATIONS CONCERNEES</th>
							</tr>
						</thead>
                        @if(count($datas) != 0)
						<tbody>
                            @foreach($datas as $dt)
								<tr>
									<td class="ts" style="padding:8px" >{{$dt['strcuture']}}</td>
                                    <td class="tb" >{{$dt['Tplainte']}}</td>
                                    <td class="ts" >{!!$dt['serv']!!}</td>
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
            <h3 class="text-center" style="text-transform: uppercase;"><strong>Suivi du traitement des plaintes (PL.)</strong><br/> du {{$peri}}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb">STRUCTURES</th>
                                <th class="tb cent">TOTAL</th>
								<th class="tb cent">PL. T</th>
								<th class="tb cent">PL. N.T</th>
								<th class="tb cent">% PL. N.T</th>
								<th class="tb cent">OBSERVATIONS</th>
							</tr>
						</thead>
                        @if(count($datas_we) != 0)
						<tbody>
                            @foreach($datas_we as $dt)
								<tr>
									<td class="ts" style="padding:8px" >{{$dt['strcuture']}}</td>
                                    <td class="tb" >{{$dt['Tplainte']}}</td>
									<td class="tb" >{{$dt['plainteTrai']}}</td>
									<td class="tb" >{{$dt['plainteNonTrai']}}</td>
                                    @if(round($dt['Tplainte']) == 0)
                                        <td class="tb">-</td>
                                        <td class="tb">-</td>
                                    @else
                                        <td class="tb" style="background-color : <?php 
                                        $obs = "";
                                        if ($dt['pourcentPNT'] > 50) {
                                            // echo '#ff0000'; //Rouge
                                            $obs = "Critique";
                                        }elseif($dt['pourcentPNT'] > 25) {
                                            // echo '#ffc000'; //Jaune moutarde
                                            $obs = "Moyennement critique";
                                        }elseif($dt['pourcentPNT'] > 0) {
                                            // echo '#ffff00'; //Jaune
                                            $obs = "Acceptable";
                                        }elseif($dt['pourcentPNT'] == 0) {
                                            // echo '#92d050'; //vert
                                            $obs = "Objectif atteint";
                                        } ?>;">
                                        {{round($dt['pourcentPNT'],2)}}</td>
                                        
                                        <td class="tb" >{{$obs}}</td>
                                    @endif
								</tr>
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="6"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
            <hr/>
            <h3 class="text-center" style="text-transform: uppercase;"><strong>Suivi du traitement des demandes d'information et</strong><br/>Requêtes (DIR) du {{$peri}}</h3>
			<div>
				<div>
                    <table class="tb" style="border : 1px solid black; width : 100%;">
						<thead>
                            <tr>
                                <th class="tb">STRUCTURES</th>
                                <th class="tb cent">TOTAL</th>
								<th class="tb cent">DIR. T</th>
								<th class="tb cent">DIR. N.T</th>
								<th class="tb cent">% DIR. N.T</th>
								<th class="tb cent">OBSERVATIONS</th>
							</tr>
						</thead>
                        @if(count($datasreq_we) != 0)
						<tbody>
                            @foreach($datasreq_we as $dtreq)
								<tr>
									<td class="ts" style="padding:8px" >{{$dtreq['strcuture']}}</td>
                                    <td class="tb" >{{$dtreq['Treq']}}</td>
									<td class="tb" >{{$dtreq['reqTrai']}}</td>
									<td class="tb" >{{$dtreq['reqNonTrai']}}</td>
                                    @if(round($dtreq['Treq']) == 0)
                                        <td class="tb">-</td>
                                        <td class="tb">-</td>
                                    @else
                                        <td class="tb" style="background-color : <?php 
                                        $obsreq = "";
                                        if ($dtreq['reqpourcentPNT'] > 50) {
                                            // echo '#ff0000'; //Rouge
                                            $obsreq = "Critique";
                                        }elseif($dtreq['reqpourcentPNT'] > 25) {
                                            // echo '#ffc000'; //Jaune moutarde
                                            $obsreq = "Moyennement critique";
                                        }elseif($dtreq['reqpourcentPNT'] > 0) {
                                            // echo '#ffff00'; //Jaune
                                            $obsreq = "Acceptable";
                                        }elseif($dtreq['reqpourcentPNT'] == 0) {
                                            // echo '#92d050'; //vert
                                            $obsreq = "Objectif atteint";
                                        } ?>;">
                                        {{round($dtreq['reqpourcentPNT'],2)}}</td>
                                        <td class="tb" >{{$obsreq}}</td>
                                    @endif
								</tr>
							@endforeach
						</tbody>
                        @else
                            <tr><th colspan="6"><h3 class="text-center"><strong>Aucune information trouvée</strong></h3></th></tr>                             
                        @endif   
					</table>                    
				</div>
			</div>
               <br>                   
            <i class="text-center"><strong>NB:</strong> Ces statistiques n’intègrent pas les sollicitations par le canal des e-services.</i><br/>

            <h2><strong>Légende : </strong></h2>
            <table>
                <tr>
                    <th class="legendepl"><strong>PL </strong> : Plaintes</th>
                    <th class="legendedir"><strong>DIR </strong> : Demandes d'informations et requêtes </th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>PL. T </strong> : Plaintes traitées</th>
                    <th class="legendedir"><strong>DIR.T </strong> : Demandes d'informations et requêtes traitées </th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>PL.N.T </strong> : Plaintes non traitées</th>
                    <th class="legendedir"><strong>DIR.N.T </strong> : Demandes d'informations et requêtes non traitées </th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>% PL.N.T > 50 </strong> = Critique</th>
                    <th class="legendedir"><strong>% DIR.N.T > 50 </strong> = Critique</th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>50 > % PL.N.T > 25 </strong> = Moyennement critique</th>
                    <th class="legendedir"><strong>50 > % DIR.N.T > 25 </strong> = Moyennement critique</th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>25 > % PL.N.T > 0 </strong> = Acceptable</th>
                    <th class="legendedir"><strong>25 > % DIR.N.T > 0 </strong> = Acceptable</th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>% PL.N.T = 0 </strong> Objectif atteint</th>
                    <th class="legendedir"><strong>% DIR.N.T = 0 </strong> Objectif atteint</th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>S : </strong> Satisfaire</th>
                    <th class="legendedir"><strong></strong> </th>
                </tr>
                <tr>
                    <th class="legendepl"><strong>N.V. </strong> : Nombre de visite </th>
                    <th class="legendedir"></th>
                </tr>
            </table>
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