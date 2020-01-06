<?php

namespace App\Http\Controllers\Admin;

use Helper;
use Illuminate\Http\Request;
use App\Bloglink;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Response;
use Illuminate\Support\Facades\Input;

class BlogLinksController extends Controller
{
    /**
     * @method:      index
     * @params:      request data
     * @createddate: 28-12-2018 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     to show blog links list
     * @return:
     */
     public function index(Request $request) {
         $blog_title = Bloglink::where('blog_title', '=',1)->firstOrFail();
     	if ($request->isMethod('post')) {
         if (Input::get('search')) {
                 $keyword = Input::get('search');
                 $blog_data = Bloglink::where(function ($query) use ($keyword) {
                     $query->orWhere('title', 'LIKE', "%$keyword%")
                         ->orWhere('url', 'LIKE', "%$keyword%");
                 })
                     ->orderByRaw('position = 0')->orderBy('position')->where('blog_title','=',0)->orderBy('title')
                     ->sortable()->paginate(env('PAGINATE_RECORDS'));
             } else {
                 $blog_data = Bloglink::sortable()->where('blog_title','=',0)->paginate(env('PAGINATE_RECORDS'));
             }
             $view = 'admin.blog_links.paginate';
         } else {
             $blog_data = Bloglink::orderByRaw('position = 0')->where('blog_title','=',0)->orderBy('position')->orderBy('title')->paginate(env('PAGINATE_RECORDS'));
             $view = 'admin.blog_links.index';
         }
         return view($view, compact('blog_data','blog_title'));
     }

    /**
     * @method:      create
     * @params:      []
     * @createddate: 27-12-2018 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     create blog link form
     * @return:
     */
    public function showAddBlogLinksForm() {
    	try {
    		$form_html = view('admin.blog_links.blog_link_add_form')->render();
            return new JsonResponse(['status' => '1', 'form_html' => $form_html]);

        } catch (\Exception $e) {
            $data = array(
                'status' => 'success',
                'message' => 'Something went wrong, please try again'
            );
            return response()->json($data);

        }
    }

    /**
     * @method:      showEditBlogLinksForm
     * @params:      id
     * @createddate: 28-12-2018 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     edit blog links form
     */
    public function showEditBlogLinksForm($id) {
    	try {
            $id = Helper::decryptDataId($id);
    		$data = Bloglink::where('id', '=', $id)->firstOrFail();
    		$form_html = view('admin.blog_links.blog_link_edit_form',compact('data'))->render();
            return new JsonResponse(['status' => '0', 'form_html' => $form_html]);

        } catch (\Exception $e) {
            $data = array(
                'status' => 'success',
                'message' => 'Something went wrong, please try again'
            );
            return response()->json($data);

        }
    }

    /**
     * @method:      AddBlogLinks
     * @params:      request data
     * @createddate: 28-12-2018 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     add new blog link
     * @return:      return to blog links list
     */
    public function AddBlogLinks(Request $request) {
        try {
    	Bloglink::create([
                        'title' => $request->title,
                        'url' => $request->url,
                        'position' => $request->position
                    ]);
        $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name . ' added new blog link ' . $request['title'] . '.');
    	$data = array(
                        'status' => 'success',
                        'message' => 'Blog Link Added successfully!'
                    );
    	return response()->json($data);
        } catch (\Exception $e) {
            $data = array(
                'status' => 'success',
                'message' => 'Something went wrong, please try again'
            );
            return response()->json($data);
        }
    }

    /**
     * @method:      EditBlogLinks
     * @params:      id
     * @createddate: 28-12-2018 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     update the blog link record
     * @return :     return to blog links list
     */
    public function EditBlogLinks(Request $request) {
    	try {
    		$data = Bloglink::where('id', '=', $request->id)->firstOrFail();
                    $data->title = $request->title;
                    $data->url = $request->url;
                    $data->position = $request->position;
                    $data->save();
                    $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name . ' update blog link ' . $request['title'] . ' details.');
                    $data = array(
                        'status' => 'success',
                        'message' => 'Blog Link Updated successfully!!'
                    );
    	return response()->json($data);
        } catch (\Exception $e) {
            $data = array(
                'status' => 'success',
                'message' => 'Something went wrong, please try again'
            );
            return response()->json($data);

        }
    }

    /**
     * @method:      status
     * @params:      encrypted
     * @createddate: 28-02-2019 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     update blog_links status [0, 1]
     * @return:      return to blog_links list
     */
    public function status(Request $request, $encrypted)
    {
        try {
            $id = Helper::decryptDataId($encrypted);
            $blog_links = Bloglink::where('id', $id)->firstOrFail();
            $this->trackActivityLog($this->AdminSession->id, $this->AdminSession->first_name . ' ' . $this->AdminSession->last_name . ' chnaged status of ' . $blog_links->title . '.');
            $message = !empty($blog_links->status) ? 'Blog Links status has been changed to deactivated.' : 'Blog Links status has been changed to activated.';
            $title = !empty($blog_links->status) ? 'Activate' : 'Deactivate';
            $current_status = !empty($blog_links->status) ? 0 : 1;
            if ($request->ajax()) {
                return $this->ajaxUpdateStatus('App\Bloglink', $id, '/admin/blog_links', $message, $current_status, $title);
            } else {
                $this->updateStatus('App\Bloglink', $id, 'Admin/blog_links', $message);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return new jsonResponse(['status' => 0, 'message' => 'There is something wrong.Please try again later.']);
            } else {
                return redirect('admin/blog_links')->with('error_message', 'There is something wrong. Please try again later.');
            }
        }
    }

    /**
     * @method:      destroy
     * @params:      encrypted
     * @createddate: 28-02-2019 (dd-mm-yyyy)
     * @developer:   Aditi
     * @purpose:     permanent deletion of the banner record
     * @return:      return to blog_links list
     */
    public function destroy(Request $request, $encrypted)
    {
        try {

            $id = Helper::decryptDataId($encrypted);
            $blog_links = Bloglink::where('id', $id)->firstOrFail();
            $deleted_item = 'Blog Link';
            $message = !empty($blog_links) ? 'Blog Link has been deleted successfully.' : 'Data not found';
            if ($request->ajax()) {
                return $this->ajaxPermanentDeletion('App\Bloglink', $id, '/admin/blog_links', $deleted_item, $message);
            } else {
                $this->permanentDeletion('App\Bloglink', $id, 'admin/blog_links');
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return new jsonResponse(['status' => 0, 'message' => 'There is something wrong.Please try again later.']);
            } else {
                return redirect('admin/blog_links')->with('error_message', 'There is something wrong. Please try again later.');
            }
        }
    }

    public function updateBlogTitle(Request $request){
        $this->ValidationCheck([ 'title' => 'required'
        ]);
        try{
            $id = Helper::decryptDataId($request->id);
            $requestData = $request->all();
            $blogData = [
                'title' => $requestData['title'],
                'url' => $requestData['title'],
                'status' => 1,
                'blog_title' => 1,

            ];
            Bloglink::where('id', $id)->update($blogData);
            return redirect('admin/blog_links')->with('flash_message', 'Blog title has been updated successfully.');
        } catch (\Exception $e){
            return redirect('admin/blog_links')->with('error_message', 'There is something wrong.Please try again.');
        }

    }

}
