@if(count($items) > 0 || request()->has('grid.filter'))
    {!! Form::open(['method' => 'get', 'url' => request()->url()]) !!}

    @if(isset($sort['field']) && isset($sort['order']))
        <input type="hidden" name="grid[sort][field]" value="{{ $sort['field'] }}"/>
        <input type="hidden" name="grid[sort][order]" value="{{ $sort['order'] }}"/>
    @endif

    <div class="table-data__tool">
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                <tr>
                    <th>#</th>
                    @foreach($columns as $column)
                        <th>
                            {!! $column['label'] !!}
                        </th>
                    @endforeach

                    @if(count($buttons) > 0)
                        <th>&nbsp;</th>
                    @endif
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    @foreach($columns as $column)
                        @if($column['filterable'] ?? false)
                            <td>
                                @if($column['filterable']['options'] ?? false)
                                    @include('components.grid.select', ['column' => $column])
                                @else
                                    @include('components.grid.input', ['column' => $column])
                                @endif
                            </td>
                        @else
                            <td>&nbsp;</td>
                        @endif
                    @endforeach

                    <td align="right">&nbsp;
                        <button class="btn btn-primary">{{ __("Filter") }}</button>
                    </td>
                </tr>
                </thead>
                <tbody>


                @forelse($items as $item)
                    <tr class="tr-shadow">

                        <td valign="center">{{ ++$pageStart }}</td>

                        @foreach($columns as $column)
                            <td>{!! $item[$column['field']] !!}</td>
                        @endforeach

                        @if(count($buttons) > 0)
                            <td>
                                <div class="table-data-feature">
                                    @foreach($item['_buttons'] as $button)
                                        {!! $button !!}
                                    @endforeach
                                </div>
                            </td>
                        @endif
                    </tr>
                    <tr class="spacer"></tr>
                @empty
                    <tr>
                        <td colspan="50">
                            <div class="alert alert-info">{{ __('No search result found.') }}</div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    </form>
    {!! $links !!}
@else
    <div class="alert alert-info">{{ __('Nothing found here.') }}</div>
@endif

