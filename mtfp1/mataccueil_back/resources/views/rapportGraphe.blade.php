<html>
  
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"> </script>
    
    <script type="text/javascript">
      
      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
        
        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ["Structures", "Totales Plaintes","Plaintes Non Traitées", {role: "style" }],

            @if(count($datas) != 0 )
              @foreach($datas as $dat)
                ['{{$dat["strcuture"]}}', {{$dat['Tplainte']}}, {{$dat['plainteNonTrai']}}, '#5C3292'],
              @endforeach
            @else
              ["Aucune information trouvée", 0, 0, "#5C3292"],
            @endif
          ]);
          var view = new google.visualization.DataView(data);
          view.setColumns([0, 1,
                          { calc: "stringify",
                            sourceColumn: 1,
                            type: "string",
                            role: "annotation" },
                          2,
                          { calc: "stringify",
                            sourceColumn: 2,
                            type: "string",
                            role: "annotation" }
                          ]);
          var options = {
            title: "GRAPHE : Traitement des Plaintes (PL.) {{$periode}}",
            width: 1400,
            height: 400,
            bar: {groupWidth: "70%"},
            legend: { position: "top" },
          };
          var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
          chart.draw(view, options);
        }
    </script>
    
  <script type="text/javascript">

      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Structures", "Totales R.","R. Non Traitées", {role: "style" }],
          @if(count($datasreq) != 0 )
            @foreach($datasreq as $datr)
              ["{{$datr['strcuture']}}", {{$datr['Treq']}}, {{$datr['reqNonTrai']}}, "#5C3292"],
            @endforeach
          @else
            ["Aucune information trouvée", 0, 0, "#5C3292"],
          @endif
        ]);
        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
                        { calc: "stringify",
                          sourceColumn: 1,
                          type: "string",
                          role: "annotation" },
                        2,
                        { calc: "stringify",
                          sourceColumn: 2,
                          type: "string",
                          role: "annotation" }
                        ]);
        var options = {
          title: "GRAPHE : Traitement des Requêtes (R) {{$periode}}",
          width: 1400,
          height: 400,
          bar: {groupWidth: "70%"},
          legend: { position: "top" },
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart"));
        chart.draw(view, options);
    }
  </script>
  
  
  <script type="text/javascript">

      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Structures", "Totales DI.","DI. Non Traitées", {role: "style" }],

          @if(count($datasDem) != 0 )
            @foreach($datasDem as $datr)
              ["{{$datr['strcuture']}}", {{$datr['Treq']}}, {{$datr['reqNonTrai']}}, "#5C3292"],
            @endforeach
          @else
            ["Aucune information trouvée", 0, 0, "#5C3292"],
          @endif
        ]);
        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
                        { calc: "stringify",
                          sourceColumn: 1,
                          type: "string",
                          role: "annotation" },
                        2,
                        { calc: "stringify",
                          sourceColumn: 2,
                          type: "string",
                          role: "annotation" }
                        ]);
        var options = {
          title: "GRAPHE : Traitement des demandes d'information (DI) {{$periode}}",
          width: 1400,
          height: 400,
          bar: {groupWidth: "70%"},
          legend: { position: "top" },
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchartDI"));
        chart.draw(view, options);
    }
  </script>
  
  <script type="text/javascript">

      google.charts.load("current", {packages:['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ["Thématiques", "Totales Préoccupations","Préoccupations Non Traitées", {role: "style" }],

          @if(count($datasThem) != 0 )
            @foreach($datasThem as $dath)
              ["{{$dath['theme']}}", {{$dath['totalReq']}}, {{$dath['ReqNonTrai']}}, "#5C3292"],
            @endforeach
          @else
            ["Aucune information trouvée", 0, 0, "#5C3292"],
          @endif

        ]);
        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,{ calc: "stringify",
                                sourceColumn: 1,
                                type: "string",
                                role: "annotation" },
                        2,{ calc: "stringify",
                            sourceColumn: 2,
                            type: "string",
                            role: "annotation" }
                          ]);
        var options = {
          title: "GRAPHE : Traitement des préoccupations par thématique {{$periode}}",
          width: 1400,
          height: 400,
          bar: {groupWidth: "70%"},
          legend: { position: "top" },
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchartTheme"));
        chart.draw(view, options);

        alert('{!! $infos !!}');
    }
  </script>
  <script src="{{url('js/jquery.min.js')}}" type="text/javascript"></script>


<h1 style="text-align:center; margin-top:50px;">GRAPHES</h1>

<div title ="{{$infos}}" id="columnchart_values" style="width: 900px; height: 300px;"></div>

<br>
<br>
<br>
<br>
<br>
<br>


  <div title ="{{$infos}}" id="columnchart" style="width: 900px; height: 300px;"></div>

  <br>
  <br>
  <br>
  <br>
  <br>
  <br>

<div title ="{{$infos}}" id="columnchartDI" style="width: 900px; height: 300px;"></div>

<br>
<br>
<br>
<br>
<br>
<br>

<div title ="{{$infos}}" id="columnchartTheme" style="width: 900px; height: 300px;"></div>