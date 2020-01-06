<div class="form-body">
  <div class="form-group row {{ $errors->has('title') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="title">Title<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{old('title',@$eblast->title)}}" name="title" placeholder="Title" type="text">
        {!! $errors->first('title', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('start_date') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="start_date">Start Date<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative has-icon-left">
        <?php
        $startDate = '';
        if (!empty($eblast->start_date)) {
          $startDate = date('Y-m-d', strtotime($eblast->start_date));
        }
        ?>
        <input type="text" style="background:#ffffff !important;" id="start_date" class="form-control date" placeholder="Start Date" name="start_date" value="{{old('start_date',@$startDate)}}">
        {!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
        <div class="form-control-position">
          <i class="ft ft-calendar"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('end_date') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="end_date">End Date<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative has-icon-left">
        <?php
        $endDate = '';
        if (!empty($eblast->end_date)) {
          $endDate = date('Y-m-d', strtotime($eblast->end_date));
        }
        ?>
        <input type="text" style="background:#ffffff !important;" class="form-control date" id="end_date" placeholder="End Date" name="end_date" value="{{old('end_date',@$endDate)}}">
        {!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
        <div class="form-control-position">
          <i class="ft ft-calendar"></i>
        </div>
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-md-3 label-control" for="position">Eblast</label>
    <div class="col-md-9">
      <div class="position-relative">
        <select name=@if((old('eblast',@$eblast->eblast_id) != 1) && (@$eblast->eblast_id != ''))"eblast"@else""@endif class="form-control eblast_select" @if(old('eblast',@$eblast->eblast_id) == 1) disabled @endif>
          <option value="">Select Eblast / Company</option>
          <?php
          if (!empty($eblastRole)) {
            foreach ($eblastRole->sortBy('first_name') as $value) {
              $selected = '';
              if (isset($eblast->eblast_id) && $eblast->eblast_id == $value->id) {
                $selected = 'selected';
              }
              ?>
              <option {{$selected}}  value="{{$value->id}}">{{old('eblast',@$value->first_name. ' '. @$value->last_name)}} / {{ @$value->userProfile->company }}</option>
              <?php
            }
          }
          ?>
        </select>
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('eblast') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="slug"></label>
    <div class="col-md-9">
      <div class="position-relative">
        <input name=@if((old('eblast',@$eblast->eblast_id) == 1) || (@$eblast->eblast_id == ''))"eblast"@else""@endif class="adv_as_admin" type="checkbox" @if(old('eblast',@$eblast->eblast_id) == 1) checked @endif value="1" >
        <span for="position"><p class="mandat"><i class="la la-asterisk"></i>Run campaign as admin</p></span>
        {!! $errors->first('eblast', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('slug') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="slug">Slug</label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{old('slug',@$eblast->slug)}}" name="slug" placeholder="Slug" type="text">
        {!! $errors->first('slug', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>

  <div class="form-group row {{ $errors->has('description') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="description">Description<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        <textarea rows="6" autocomplete="off" class="form-control" id="summary-ckeditor" name="banner_description" placeholder="Description">{{old('banner_description',@$eblast->description)}}</textarea>
        <!-- <textarea rows="6" class="form-control" name="banner_description" placeholder="Description">{{old('banner_description',@$eblast->description)}}</textarea> -->
        {!! $errors->first('banner_description', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('form_banner') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="form_banner">Form Banner<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        <div class="custom-file">
          <input type="file"  accept="image/*"  name="form_banner" class="form-control-file select-image" id="image2">
          <div id="image_validate2"></div>
        </div>
        <?php
        if (!empty($eblast->form_banner)) {
          ?>
          <a href="<?php echo url('/uploads/banners/eblast/' . $eblast->form_banner); ?>" title="{{$eblast->form_banner}}" target="_blank"><img style="height:50px" class="img-thumbnail" src="<?php echo url('/uploads/banners/eblast/' . $eblast->form_banner); ?>"></a>
          <?php
        }
        ?>
        {!! $errors->first('form_banner', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-md-3 label-control" for="alt_text">Alt Text</label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{ old('alt_text_banner',@$eblast->alt_text_banner) }}" name="alt_text_banner" placeholder="Alt Text for Form Banner" type="text">
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('aweber_list_id') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="aweber_list_id">Aweber List Id</label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{old('aweber_list_id',@$eblast->aweber_list_id)}}" name="aweber_list_id" placeholder="Aweber List Id" type="text">
        {!! $errors->first('aweber_list_id', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('ad_tracking') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="ad_tracking">Ad Tracking</label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{old('ad_tracking',@$eblast->ad_tracking)}}"  name="ad_tracking" placeholder="Ad Tracking" type="text">
        {!! $errors->first('ad_tracking', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('tag_name') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="tag_name">Infusionsoft Tag ID<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{@$eblast->tag_name}}" name="tag_name" placeholder="Infusionsoft Tag ID" type="text">
        {!! $errors->first('tag_name', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('button_text') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="button_text">Form Button Text<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{old('button_text',@$eblast->button_text)}}" name="button_text" placeholder="Button Text" type="text">
        {!! $errors->first('button_text', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('border_color') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="border_color">Form Border Color<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        @if(!empty(@$eblast->border_color))
        <input type="text" class="form-control minicolors" name="border_color" value="{{old('border_color',@$eblast->border_color)}}">
        @else
        <input type="text" class="form-control minicolors" name="border_color" value="#e31414">
        @endif
        {!! $errors->first('border_color', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row {{ $errors->has('button_color') ? 'has-error' : ''}}">
    <label class="col-md-3 label-control" for="button_color">Form Button Color<i class="la la-asterisk"></i></label>
    <div class="col-md-9">
      <div class="position-relative">
        @if(!empty(@$eblast->button_color))
        <input type="text" class="form-control minicolors" name="button_color" value="{{old('border_color',@$eblast->button_color)}}">
        @else
        <input type="text" class="form-control minicolors" name="button_color" value="#e31414">
        @endif
        {!! $errors->first('button_color', '<p class="help-block">:message</p>') !!}
      </div>
    </div>
  </div>
  <div class="form-group row">
    <label class="col-md-3 label-control" for="alt_text">Thank You URL</label>
    <div class="col-md-9">
      <div class="position-relative">
        <input autocomplete="off" class="form-control" value="{{ old('thank_you_url',@$eblast->thank_you_url) }}" name="thank_you_url" placeholder="Thank You URL" type="url">
      </div>
    </div>
  </div>
  <hr>
  <h5><strong>Form Builder</strong></h5>
  <p><strong class="red">*</strong>Drag and drop form elements from the right to the builder area.</p>
  <p><strong class="red">* Do not</strong> edit the pre-built fields (name, email, phone number), It may effect syncing contacts with infusion soft. You can add other extra fields.</p>
  <div class="card-body" style="border: 2px solid grey;">
    <div id="build-wrap"></div>
  </div>
  <div id="form-builed-message"></div>
  <?php
  if (isset($eblast->id) and ! empty($eblast->id)) {
    ?>
    <input type="hidden" value="{{$eblast->id}}" name="id">
    <?php
  }
  ?>
</div>
<div class="form-actions right">
  <a href="<?php echo url('/') . '/admin/eblast'; ?>" class="btn btn-outline-light">
    <i class="la la-times-circle-o"></i> Cancel
  </a>
  <button type="submit" class="btn btn-outline-info save-btn">
    <i class="la la-check-circle-o"></i> Save
  </button>
</div>
<script>
$(document).ready(function(){
  // select option
  $(document).on('change','.eblast_select',function(){
    var val = $(this).val();
    if(val != '')
    {
      $(this).attr('name','eblast');
      $('.adv_as_admin').attr('name','');
      $('.adv_as_admin').attr('disabled','disabled');
      $('.mandat').html('Run campaign as admin');
    }
    else
    {
      $(this).attr('name','');
      $('.adv_as_admin').attr('name','eblast');
      $('.adv_as_admin').removeAttr('disabled');
      $('.mandat').html('<i class="la la-asterisk"></i>Run campaign as admin');
    }
  });

  // checkbox
  $(document).on('change','.adv_as_admin',function(){
    if($(this). prop("checked") == true)
    {
      $(this).attr('name','eblast');
      $('.eblast_select').attr('name','');
      $('.eblast_select').attr('disabled','disabled');
    }else {
      $(this).attr('name','');
      $('.eblast_select').removeAttr('disabled');
      $('.eblast_select').attr('name','eblast');
    }
  });
});
</script>
