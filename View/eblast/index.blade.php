@extends('layouts.admin')
@section('title', 'Manage Eblast Campaign')
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-6 col-12 mb-2">
            <h3 class="content-header-title">Manage Eblast Campaign</h3>
            <div class="row breadcrumbs-top">
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo url('/admin/dashboard'); ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item active"><a href="javascript:void(0);">Eblast Campaign</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="default">
        <div class="col-12">
            <div class="card" style="padding: 0 15px 15px 15px;">
                <div class="card-header">
                    <h4 class="card-title">Manage Eblast Campaign</h4>
                    <a class="heading-elements-toggle"><i class="la la-ellipsis-v font-medium-3"></i></a>
                    <div class="heading-elements">
                        <ul class="list-inline mb-0">
                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                            <li><a href="{{ url('/admin/eblast') }}"><i class="ft-rotate-cw"></i></a></li>
                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-content collapse show administrator-page">
                    @include('elements.message')
                    <div class="table-actions-header col-12" style="padding:0">
                        <div class="row">
                            <div style="margin-top:13px;" class="col-xl-3 col-md-5 mb-2">
                                <a href="<?php echo url('/admin/eblast/create'); ?>" class="mt-1 btn btn-outline-info actn-btn"><i class="ft-user-plus"></i>Add New Campaign</a>
                            </div>
                            <div class="col-xl-5 col-md-7 mb-2">
                                <form method="post" id="multipleActions" action="" role="search">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                    <div class="input-group">
                                        <label class="w-100"><strong>Apply Actions:</strong></label>
                                        <div class="input-group-append">
                                            <fieldset>
                                                <select class="form-control" name="action" id="basicSelect">
                                                    <option value="">Select Action</option>
                                                    <option value="delete">Delete</option>
                                                    <option value="activate">Activate</option>
                                                    <option value="deactivate">Deactivate</option>
                                                </select>
                                                <input type="hidden" name="getIds" value=""/>
                                                <button id="buttonSubmit" style="display:none;" type="submit" name="submit" value="submit">Submit</button>
                                            </fieldset>
                                        </div>
                                        <button id="btnSubmit" class="btn btn-primary bg-info border-info" type="button" name="submit" value="submit">Submit</button>

                                    </div>
                                </form>
                            </div>
                            <div class="col-xl-4 col-md-6 mb-2">
                                <form id="searchForm" method="get" action="javascript:void(0);" role="search">
                                    <div class="input-group">
                                        <label class="w-100"><strong>Search:</strong></label>
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                                        <input autocomplete="off" name="search" type="text" class="form-control input-md" placeholder="Search.." aria-describedby="button-addon6">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary bg-info border-info searchButton" type="button"> <i class="ft-search"></i></button>
                                            <input type="hidden" name="action" value="/eblast">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="row cus_filters">
                            @include('elements.filter_rows')
                        </div>
                    </div>
                    <div class="bordr-table table-responsive" id="dynamicContent">
                        <table class="table mb-0">
                            <thead>
                            <tr>
                                <th><input class="checked_all" type="checkbox" id="select_all" name="select_all" value=""/></th>
                                <th>Sr. No</th>
                                <th>@sortablelink('title', 'Title',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
                                <th>Eblast Campaign Link</th>
                                <th>@sortablelink('description','Description',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
                                <th>@sortablelink('end_date', 'Expiry Date',['page'=>Request::get('page'),'_token'=>csrf_token()],['class'=>'sortable'])</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $counter = 1;
                            if (count($eblast)) {
                            foreach ($eblast as $key => $value) {
                            ?>
                            <tr id="tr_{{$value->id}}">
                                <td><input class="checkbox" type="checkbox" name="eblast_ids[]" value="{{$value->id}}"/></td>
                                <td>{{$counter}}</td>
                                <td>{{$value->title}}</td>
                                <td>
                                    <a target="_blank" href="{{ url('/eblast/'.$value->slug) }}">{{ wordwrap(url('/eblast/'.$value->slug),25,"<br>\n") }}</a>

                                    <div style="height: 20px;float: right;cursor: pointer;" class="badge badge-warning copy" data-clipboard-text="{{ url('/eblast/'.$value->slug) }}">
                                        <span><i class="ft ft-copy"></i>copy</span>
                                    </div>
                                </td>
                                <td>{{mb_strimwidth($value->description,0,20,'...')}}</td>
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
                                <td id="td_{{$value->id}}" style="width: 100px;">
                                    <?php
                                    $statusText = !empty($value->status) ? 'Deactivate' : 'Activate';
                                    $icon = !empty($value->status) ? 'ft-unlock' : 'ft-lock red';
                                    $encryptId = Helper::encryptDataId($value->id);
                                    $checkEnquiry = Helper::getEblastEnquiry($value->id);
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('admin-assets/js/scripts/clipboard.js-master/dist/clipboard.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            var clipboard = new ClipboardJS('.copy',{
                target: function(e) {
                    $(e).html('copied!');
                }
            });
            $(document).on('click','.copy',function() {
                let link = $(this);
                var clipboard = new ClipboardJS('.copy');
                clipboard.on('success', function(e) {
                    link.html('copied!');
                    clipboard.destroy();
                });
                clipboard.on('error', function(e) {
                    link.html('Not copied!');
//                    console.log(e);
                });
            });
        });
    </script>
@endsection
