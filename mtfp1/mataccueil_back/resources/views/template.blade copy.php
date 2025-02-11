<table class="table">
            <thead>
                <tr>
                    <th rowspan="2"></th>
                    <th class="text-center" colspan="2">Semaine écoulé</th>
                    <th class="text-center" colspan="2">Total</th>
                </tr>
                <tr>
                    <th>Plus élevé</th>
                    <th>Plus Faible</th>
                    <th>Plus élevé</th>
                    <th>Plus Faible</th>
                </tr>
            </thead>

            <tbody>
                @foreach(  $data3 as $d)
                <tr >
                    <td>{{$d['name']}}</td>
                    <td>
                        <ol>
                        @forelse( $d['max_last_week'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el->total}}</dt>
                            </dl>
                            </li>
                            @else

                            <p class="text-center">Aucune donnée disponible</p>
                        @forelse
                        </ol>
                    </td>
                    <td>
                    <ol>
                        @forelse( $d['min_last_week'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @else
                            <p class="text-center">Aucune donnée disponible</p>
                        @forelse
                        </ol>
                           
                      
                    </td>
                    <td>
                    <ol>
                        @forelse( $d['max_total'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @else
                            <p class="text-center">Aucune donnée disponible</p>
                        @forelse
                        </ol>
                           
                      
                    </td>
                    <td>
                    <ol>
                        @forelse( $d['min_total'] as $el )

                            <li>
                            <dl class="mb-1">
                                <dt>Commune: {{$el['libellecom']}}</dt>
                                <dt>Valeur:  {{$el['total']}}</dt>
                            </dl>
                            </li>
                            @else
                            <p class="text-center">Aucune donnée disponible</p>
                        @endforelse
                        </ol>
                           
                       
                    </td>
                </tr>
            </tbody>
        </table>