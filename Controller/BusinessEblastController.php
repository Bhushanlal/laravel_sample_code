<?php

namespace App\Http\Controllers\Admin;
use App\InfusionSoftToken;
use Helper;
use App\User;
use App\BusinessForm;
use App\LeadBoxEnquiry;
use Carbon\Carbon;
use App\BusinessEblast;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;

class BusinessEblastController extends Controller
{
  /**
  * @method:      index
  * @params:      request data
  * @created_date: 27-02-2019 (dd-mm-yyyy)
  * @developer:   Aditi
  * @purpose:     to show list of eblast campaign
  * @return       \Illuminate\Http\Response
  */
  public function index(Request $request)
  {
    if ($request->isMethod('post')) {
      $requestData = $request->all();
      if (isset($requestData['getIds']) and ! empty($requestData['getIds'])) {
        return $this->multipleActions($requestData);
      }
      if (Input::get('search')) {
        $keyword = Input::get('search');
        $eblast = BusinessEblast::Where('title', 'LIKE', "%$keyword%")
        ->orWhere('start_date', 'LIKE', "%$keyword%")
        ->orWhere('end_date', 'LIKE', "%$keyword%")
        ->sortable()->paginate(env('PAGINATE_RECORDS'));
      } else {
        $eblast = BusinessEblast::sortable()->paginate(env('PAGINATE_RECORDS'));
      }
      $view = 'admin.eblast.paginate';
    } else {
      $eblast = BusinessEblast::latest()->paginate(env('PAGINATE_RECORDS'));
      $view = 'admin.eblast.index';
    }
    return view($view, compact('eblast'));
  }

  /**
  * @method:       create
  * @params:       null
  * @created_date: 27-02-2019 (dd-mm-yyyy)
  * @developer:    Aditi
  * @purpose:      to show eblast form
  * @return        \Illuminate\Http\Response
  */
  public function create()
  {
    $today = Carbon::now()->toDateString();
    $eblastRole = User::whereIn('user_role_id', [ 2, 4, 8, 14, 15])
    ->where('status',1)
    ->where('deleted_at',null)
    //->where('expiry_date','>=',$today)
    ->get();
    $content=BusinessForm::where('id',2)->first();
    return view('admin.eblast.create', compact('eblastRole','content'));
  }

  /**
  * @method:       store
  * @params:       $request
  * @created_date: 27-02-2019 (dd-mm-yyyy)
  * @developer:    Aditi
  * @purpose:      to store eblast campaign
  * @return        \Illuminate\Http\Response
  */
  public function store(Request $request)
  {
    if($request->isMethod('post')){
      $this->ValidationCheck(['title' => 'required',
      'start_date' => 'required',
      'end_date' => 'required',
      'eblast' => 'required',
      'slug' => 'required',
      'banner_description' => 'required',
      'form_banner' => 'required',
      'form_content' => 'required',
      'button_text' => 'required',
      'border_color' => 'required',
      'button_color' => 'required',
      'aweber_list_id' => 'string|nullable',
      'ad_tracking' => 'string|nullable',
      'tag_name' => 'string|nullable',
      'thank_you_url' => 'url|nullable'
    ]);
    # fetching infusion soft access token
    # if access token time expires, then get a new one
    $infusion_soft_cre = InfusionSoftToken::where('id', '=', 1)->firstOrFail();
    $access_token = $infusion_soft_cre->access_token;
    $status_code = app('App\Http\Controllers\InfusionSoftController')->getAccountProfile($access_token);
    if ($status_code == 401) {
      # UnAuthorized, then generate new access code.
      $temp_check = app('App\Http\Controllers\InfusionSoftController')->getAccessToken();
      # if request failed, return
      if ($temp_check == false) {
        return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
      }
      # get updated record
      $infusion_soft_cre = InfusionSoftToken::where('id', '=', 1)->firstOrFail();
    }
    if ($status_code == 0) {
      # Something went wrong
      return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
    }
    try {
      $access_token = $infusion_soft_cre->access_token;
      $client = new \GuzzleHttp\Client();
      $url = 'https://api.infusionsoft.com/crm/rest/v1/tags/'.$request->tag_name.'?access_token='.$access_token;
      $req_tag = $client->get($url);
      $res_tag = $req_tag->getBody()->getContents();
      $res_tag = \GuzzleHttp\json_decode($res_tag, true);
    } catch (\Exception $e) {
      return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
    }
    try{
      $requestData = $request->all();
      $requestData['slug'] = Helper::createPostSlug($requestData['slug'], 'App\BusinessEblast', $request->get('id'));
      $eblastData = [
        'user_id'   =>  $this->AdminSession->id,
        'title'     =>  $requestData['title'],
        'eblast_id'    =>  $requestData['eblast'],
        'slug'      =>  $requestData['slug'],
        'description'   =>  $requestData['banner_description'],
        'alt_text_banner'   =>  $requestData['alt_text_banner'],
        'form_content' => json_encode(\GuzzleHttp\json_decode($request->form_content, true)),
        'status' => 1,
        'button_text' => $request->button_text,
        'border_color' => $request->border_color,
        'button_color' => $request->button_color,
        'aweber_list_id' => $request->aweber_list_id,
        'ad_tracking' => $request->ad_tracking,
        'tag_name' => $request->tag_name,
        'thank_you_url' => $request->thank_you_url
      ];

      if (!empty($requestData['start_date'])) {
        $eblastData['start_date'] = date('Y-m-d', strtotime($requestData['start_date']));
      }
      if (!empty($requestData['end_date'])) {
        $eblastData['end_date'] = date('Y-m-d', strtotime($requestData['end_date']));
      }
      if (!empty($requestData['form_banner'])) {
        $imageName = time() . '.' . request()->form_banner->getClientOriginalExtension();
        request()->form_banner->move(public_path('uploads/banners/eblast'), $imageName);
        $eblastData['form_banner'] = $imageName;
      }
      BusinessEblast::create($eblastData);
      $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name. ' added new eblast campaign. ');
      return redirect('admin/eblast')->with('flash_message', 'Eblast Campaign has been added successfully.');
    }catch(\Exception $e) {
      return redirect()->back()->withInput($request->all)->with('error_message', 'There is something wrong. Please try again.');
    }
  }
  return true;
}

/**
* Display the specified resource.
*
* @param  int  $id
* @return \Illuminate\Http\Response
*/
public function show() {
  try{
    return redirect('/admin/eblast');
  }catch(\Exception $e){
    return redirect('/')->with('error_message', 'Something went wrong, please try again');
  }
}

/**
* @method:       edit
* @params:       $encryptId
* @created_date: 27-02-2019 (dd-mm-yyyy)
* @developer:    Aditi
* @purpose:      to edit eblast
* @return        \Illuminate\Http\Response
*/
public function edit($encryptId)
{
  try{
    $today = Carbon::now()->toDateString();
    $eblastRole = User::whereIn('user_role_id', [ 2, 4, 8, 14, 15])
    ->where('status',1)
    ->where('deleted_at',null)
    //->whereDate('expiry_date','>=',$today)
    ->get();
    $eblast = BusinessEblast::where('id', Helper::decryptDataId($encryptId))->firstOrFail();
  }catch(\Exception $e) {
    return redirect('/admin/eblast')->with('error_message', 'There is something wrong. Please try again.');
  }
  return view('admin.eblast.edit', compact('eblastRole','eblast'));
}

/**
* @method:      update
* @params:      $request
* @created_date: 27-02-2019 (dd-mm-yyyy)
* @developer:   Aditi
* @purpose:     permanent deletion of the banner record
* @return       \Illuminate\Http\Response
*/
public function update(Request $request)
{
  if($request->isMethod('patch')){
    $this->ValidationCheck([
      'title' => 'required',
      'start_date' => 'required',
      'end_date' => 'required',
      'eblast' => 'required',
      'slug' => 'required',
      'banner_description' => 'required',
      'form_content' => 'required',
      'button_text' => 'required',
      'border_color' => 'required',
      'button_color' => 'required',
      'aweber_list_id' => 'string|nullable',
      'ad_tracking' => 'string|nullable',
      'tag_name' => 'string|nullable',
      'thank_you_url' => 'url|nullable'
    ]);
    # fetching infusion soft access token
    # if access token time expires, then get a new one
    $infusion_soft_cre = InfusionSoftToken::where('id', '=', 1)->firstOrFail();
    $access_token = $infusion_soft_cre->access_token;
    $status_code = app('App\Http\Controllers\InfusionSoftController')->getAccountProfile($access_token);
    if ($status_code == 401) {
      # UnAuthorized, then generate new access code.
      $temp_check = app('App\Http\Controllers\InfusionSoftController')->getAccessToken();
      # if request failed, return
      if ($temp_check == false) {
        return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
      }
      # get updated record
      $infusion_soft_cre = InfusionSoftToken::where('id', '=', 1)->firstOrFail();
    }
    if ($status_code == 0) {
      # Something went wrong
      return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
    }
    try {
      $access_token = $infusion_soft_cre->access_token;
      $client = new \GuzzleHttp\Client();
      $url = 'https://api.infusionsoft.com/crm/rest/v1/tags/'.$request->tag_name.'?access_token='.$access_token;
      $req_tag = $client->get($url);
      $res_tag = $req_tag->getBody()->getContents();
      $res_tag = \GuzzleHttp\json_decode($res_tag, true);
    } catch (\Exception $e) {
      return redirect()->back()->withInput($request->all())->with('error_message', 'Could not verify infusion soft tag ID');
    }
    try{
      $requestData = $request->all();
      $requestData['slug'] = Helper::createUniqueSlug($requestData['slug'], 'App\BusinessEblast', $request->get('id'));
      $eblastData = [
        'user_id'   =>  $this->AdminSession->id,
        'title'     =>  $requestData['title'],
        'eblast_id'    =>  $requestData['eblast'],
        'slug'      =>  $requestData['slug'],
        'description'   =>  $requestData['banner_description'],
        'alt_text_banner'   =>  $requestData['alt_text_banner'],
        'form_content' => json_encode(\GuzzleHttp\json_decode($request->form_content, true)),
        'button_text' => $request->button_text,
        'border_color' => $request->border_color,
        'button_color' => $request->button_color,
        'aweber_list_id' => $request->aweber_list_id,
        'ad_tracking' => $request->ad_tracking,
        'tag_name' => $request->tag_name,
        'thank_you_url' => $request->thank_you_url
      ];

      if (!empty($requestData['start_date'])) {
        $eblastData['start_date'] = date('Y-m-d', strtotime($requestData['start_date']));
      }
      if (!empty($requestData['end_date'])) {
        $eblastData['end_date'] = date('Y-m-d', strtotime($requestData['end_date']));
      }
      if (!empty($requestData['form_banner'])) {
        $imageName = time() . '.' . request()->form_banner->getClientOriginalExtension();
        request()->form_banner->move(public_path('uploads/banners/eblast'), $imageName);
        $eblastData['form_banner'] = $imageName;
      }
      BusinessEblast::where('id', $request->get('id'))->update($eblastData);
      $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name. ' added new eblast campaign. ');
      return redirect('admin/eblast')->with('flash_message', 'Eblast Campaign has been updated successfully.');
    }catch(\Exception $e) {
      return redirect()->back()->withInput($request->all)->with('error_message', 'There is something wrong. Please try again.');
    }
  }

}

/**
* @method:      destroy
* @params:      encrypted
* @created_date: 27-02-2019 (dd-mm-yyyy)
* @developer:   Aditi
* @purpose:     permanent deletion of the banner record
* @return       \Illuminate\Http\Response
*/
public function destroy(Request $request, $encrypted)
{
  try{
    $id = Helper::decryptDataId($encrypted);
    $eblast = BusinessEblast::where('id', $id)->firstOrFail();
    $deleted_item = 'eblast campaign';
    $message = !empty($eblast) ? 'Eblast has been deleted successfully.' : 'Data not found';
    if($request->ajax()){
      return $this->ajaxPermanentDeletion('App\BusinessEblast', $id, '/admin/eblast', $deleted_item, $message);
    }else {
      $this->permanentDeletion('App\BusinessEblast', $id, 'admin/eblast');
    }
  } catch(\Exception $e){
    if($request->ajax()){
      return new jsonResponse(['status'=>0, 'message'=>'There is something wrong.Please try again later.']);
    }else {
      return redirect('admin/banners')->with('error_message', 'There is something wrong. Please try again later.');
    }
  }
}

/**
* @method:      status
* @params:      encrypted
* @created_date: 27-02-2019 (dd-mm-yyyy)
* @developer:   Aditi
* @purpose:     update eblast status [0, 1]
* @return:      \Illuminate\Http\JsonResponse
*/
public function status(Request $request, $encrypted)
{
  try{
    $id = Helper::decryptDataId($encrypted);
    $eblast = BusinessEblast::where('id',$id)->firstOrFail();
    $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name.' '.$this->AdminSession->last_name.' chnaged status of '.$eblast->title.'.');
    $message = !empty($eblast->status) ? 'Eblast status has been changed to deactivated.' : 'Eblast status has been changed to activated.';
    $title = !empty($eblast->status) ? 'Activate' : 'Deactivate';
    $current_status = !empty($eblast->status) ? 0 : 1;
    if($request->ajax()){
      return $this->ajaxUpdateStatus('App\BusinessEblast', $id, '/admin/eblast', $message, $current_status, $title);
    }else {
      $this->updateStatus('App\BusinessEblast', $id, 'Admin/eblast', $message);
    }
  } catch(\Exception $e){
    if($request->ajax()){
      return new jsonResponse(['status'=>0, 'message'=>'There is something wrong.Please try again later.']);
    }else {
      return redirect('admin/eblast')->with('error_message', 'There is something wrong. Please try again later.');
    }
  }
  return true;
}

/**
* @method:      multipleActions
* @params:      request data
* @created_date: 27-02-2019 (dd-mm-yyyy)
* @developer:   Aditi
* @purpose:     to apply action on multiple banners
* @return:      return to eblast campaign page
*/
public function multipleActions($requestData) {
  if (!empty($requestData['getIds'])) {
    $eblastList = explode(',', $requestData['getIds']);
    try {
      if ($requestData['action'] === 'delete') {
        BusinessEblast::whereIn('id', $eblastList)->delete();
        $activity = 'deleted the eblast campaign.';
      } elseif ($requestData['action'] === 'activate') {
        BusinessEblast::whereIn('id', $eblastList)->update([
          'status' => 1
        ]);
        $activity = 'activated the eblast campaign.';
      } else {
        BusinessEblast::whereIn('id', $eblastList)->update([
          'status' => 0
        ]);
        $activity = 'deactivated the eblast campaign.';
      }
      $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name . ' ' . $activity);
      return redirect('admin/eblast')->with('flash_message', 'Action has been applied on the selected records.');
    } catch (\Exception $e) {
      return redirect('admin/eblast')->with('error_message', 'There is something wrong.Please try again.');
    }
  }
  return true;
}

/**
* @method:       downloadLeads
* @params:       $encryptId
* @created_date: 06-11-2019 (dd-mm-yyyy)
* @developer:    Sarvjeet
* @purpose:      to download leads
* @return        \Illuminate\Http\Response
*/
public function downloadLeads($encryptId)
{
  try{
    $resultData = [];
    $data = LeadBoxEnquiry::where('advertisement_id', Helper::decryptDataId($encryptId))->get();
    foreach ($data as $key1 => $value) {
      $form_data['Sr'] = $key1+1;
      $form_data['Lead Title'] = $value->enquiry->title;
      $form_data['Advertiser'] = $value->advertiser->full_name;
      if($value->form_data)
      {
        foreach(json_decode($value->form_data) as $key2 => $value1)
        {
          $form_data[$key2] =   $value1;
        }
      }
      $form_data['Date Time'] = $value->created_at->toDateTimeString();
      $resultData[$key1] = $form_data;
    }
    $returnData = collect($resultData);
    return (new FastExcel($returnData))->download(Carbon::now()->format('m-d-Y').'_leads.csv');

  }catch(\Exception $e) {
    // echo $e->getMessage(); die;
    return redirect('/admin/eblast')->with('error_message', 'There is something wrong. Please try again.');
  }
}
}
