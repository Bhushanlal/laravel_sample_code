<table class="table mb-0">
    <thead>
    <tr>
        <th><input class="checked_all" type="checkbox" id="select_all" name="select_all" value=""/></th>
        <th>Sr. No</th>
        <th>@sortablelink('title', 'Title',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
        <th>Eblast Campaign Link</th>
        <th>@sortablelink('description','Description',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
        <th>@sortablelink('end_date', 'End Date',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $counter = 1;
    if (Request::get('page') and ! empty(Request::get('page'))) {
        $page = Request::get('page') - 1;
        $counter = $eblast->perpage() * $page + 1;
    }
    if (count($eblast)) {
    foreach ($eblast as $key => $value) {
    ?>
    <tr id="tr_{{$value->id}}">
        <td><input class="checkbox" type="checkbox" name="eblast_ids[]" value="{{$value->id}}"/></td>
        <td>{{$counter}}</td>
        <td>{{$value->title}}</td>
        <td><a target="_blank" href="{{ url('/eblast/'.$value->slug) }}">{{ url('/eblast/'.$value->slug) }}</a>
            <div style="cursor: pointer;float: right" class="badge badge-warning copy" data-clipboard-text="{{ url('/eblast/'.$value->slug) }}">
                <span><i class="ft ft-copy"></i>copy</span>
            </div>
        </td>
        <td>{{mb_strimwidth($value->description,0,30,'...')}}</td>
        <td>
            <?php
            $today_date = \Carbon\Carbon::now();
            $expire_date = \Carbon\Carbon::createFromFormat('Y-m-d', $value->end_date);
            $data_difference = $today_date->diffInDays($expire_date, false);  //false param
            if($data_difference < 0) {
                echo '<span class="red">'.$value->end_date.'</span>';
            }else{
                echo $value->end_date;
            }
            ?>
        </td>
        <td id="td_{{$value->id}}">
            <?php
            $statusText = !empty($value->status) ? 'Deactivate' : 'Activate';
            $icon = !empty($value->status) ? 'ft-unlock' : 'ft-lock red';
            $checkEnquiry = Helper::getEblastEnquiry($value->id);
            $encryptId = Helper::encryptDataId($value->id);
            ?>
            @if(!empty($checkEnquiry))
              <a title="Download Leads" href="{{ url('/admin/eblast/download-leads/'.$encryptId) }}"><i class="fa fa-download"></i></a>
            @endif
            <a title="Edit" href="{{ url('/admin/eblast/edit/'.$encryptId) }}">
                <i class="ft-edit"></i>
            </a>
            <a href="javascript:void(0);" data-url="{{ url('/admin/eblast/status/'.$encryptId) }}" class="statusLink" title="{{ $statusText }}">
                <i class="{{ $icon }} "></i>
            </a>
            <a href="javascript:void(0);" data-attr-id="{{ $value->id }}" class="deleteRecord" title="Delete"><i data-attr-id="{{ $value->id }}" class="ft-trash" aria-hidden="true"></i></a>
            <form id="deleteRec_{{ $value->id }}"  method="POST" action="{{ url('/admin/eblast/'. $encryptId) }}" accept-charset="UTF-8" style="display:inline">
                {{ method_field('DELETE') }}
                {{ csrf_field() }}
                <button  style="display: none;" type="submit" class="btn btn-danger btn-sm " title="Delete">Delete</button>
            </form>
        </td>
    </tr>
    <?php
    $counter++;
    }
    } else {
    ?>
    <tr>
        <td class="text-center" colspan="6">No records found.</td>
    </tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php
$direction = 'desc';
$sort = 'id';
if (Request::get('direction') and ! empty(Request::get('direction'))) {
    $direction = Request::get('direction');
    $sort = Request::get('sort');
}
if (count($eblast)) {
?>
{!! $eblast->appends(['search' => Request::get('search'),'sort'=>$sort,'direction'=>$direction,'page'=>Request::get('page'),'_token'=>csrf_token()])->render() !!}
<?php } ?>
