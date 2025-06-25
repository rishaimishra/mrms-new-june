<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Models\SavedStarRechargeCard;
use App\Models\User;
use App\Models\NewsSubscription;
use App\Models\NationalNews;
use App\Models\StateNewsSubscription;
use App\Models\NationalNewsSubscription;
use App\Models\NoticeSubscription;
use App\Models\AutoSubscription;
use App\Models\RealestateSubscription;
use App\Models\SactonSubscription;
use App\Models\BreakingNews;
use App\Models\PublicNotice;
use App\Models\NoticeImage;
use Illuminate\Support\Facades\Auth;
class UtilityExtendController extends AdminController
{
    public function news_subscription(){
        $users_seller = [];
        $dstv = NewsSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.news.news_subscription',compact('dstv','users_seller'));
    }
    public function news_subscription_store(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'headline' => 'required|string|max:255',
            'story_board' => 'required',
            'headline_image' => 'required',
            // 'headlineimg' => 'required',
            'editor_name' => 'required'
        ]);
         $headline = new NewsSubscription();
        $headline->headline = $request->headline;
        $headline->editor_name = $request->editor_name;
        $headline->headline_description = $request->headline_description;
        if ($request->hasFile('headline_image')) {
            $file = $request->file('headline_image');
            $path = $file->store('form_images', 'public');
            $headline->headline_image = $path;
             }
             $headline->user_id = 105;
             $headline->status = 1;
             $headline->story_board = $request->story_board;
             $headline->save();
             if ($request->hasFile('headlineimg')) {
                foreach ($request->File('headlineimg') as $key => $file) {
                    $path = $file->store('headlineimg', 'public');
                        $headline_image = new HeadlineImages();
                        $headline_image->images = $path;
                        $headline_image->headline_id = $headline->id;
                        $headline_image->save();
                    }
             }
             return back()->with('success', 'headline added successfully.');
    }
    public function news_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NewsSubscription::where('id',$id)->first();
        return view('admin.news.show',compact('hd','users_seller'));
    }
    public function news_subscription_update(Request $request){
        $subscription = NewsSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function news_subscription_delete($id){
        // return $id;
        $subscription = NewsSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }

    public function national_news(){
        $user_id = Auth::user()->id;
        $users_seller = [];
        $dstv = NationalNews::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.nationalNews.national_news',compact('dstv','users_seller','user_id'));
    }
    public function national_news_store(Request $request){

        // return $request;
        $validatedData = $request->validate([
            'headline' => 'required|string|max:255',
            'story_board' => 'required',
            'headline_image' => 'required',
            // 'headlineimg' => 'required',
            'editor_name' => 'required'
        ]);
         $headline = new NationalNews();
        $headline->headline = $request->headline;
        $headline->editor_name = $request->editor_name;
        $headline->headline_description = $request->headline_description;
            if ($request->hasFile('front_image')) {
                $file = $request->file('front_image');
                $path = $file->store('form_images', 'public');
                $headline->front_image = $path;
             }
            if ($request->hasFile('headline_image')) {
                $file = $request->file('headline_image');
                $path = $file->store('form_images', 'public');
                $headline->headline_image = $path;
             }
             $headline->user_id = Auth::user()->id;
             $headline->status = 1;
             $headline->is_image = 1;
             $headline->story_board = $request->story_board;
             $headline->save();
            
             return back()->with('success', 'headline added successfully.');
    }
    public function national_news_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $hd = NationalNews::where('id',$id)->first();
        return view('admin.nationalNews.show',compact('hd','users_seller'));
    }
    public function national_news_update(Request $request){
        $subscription = NationalNews::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function national_news_delete($id){
        // return $id;
        $subscription = NationalNews::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }


    public function news_detail($id){
        $hd = NewsSubscription::where('id',$id)->first();
        
        return view('admin.news.newsDetailMobile',compact('hd'));
    }
    public function national_news_detail($id){
        $hd = NationalNews::where('id',$id)->first();
        
        return view('admin.news.newsDetailMobile',compact('hd'));
    }
    function upload(Request $request)
    {
        $image = $request->upload;
        $imagename = str_replace(' ', '', 'tuorial' . time() . $image->getClientOriginalName());
        $image->move(public_path('uploads/post/'), $imagename);
        $url = asset('uploads/post/' . $imagename);
        $CKEditorFuncNum = $request->input('CKEditorFuncNum');
        $response = "<script>window.parent.CKEDITOR.tools.callFunction( $CKEditorFuncNum  ,  '$url' ,'uploaded')</script>";
        @header('Content-Type:text/html;charset-utf-8');
        echo $response;
    }


    // auto functions


    public function auto_subscription(){
        $users_seller = [];
        $dstv = AutoSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.autos.auto_subscription',compact('dstv','users_seller'));
    }
    public function auto_subscription_store(Request $request){
        // return $request;
        $subscription = new AutoSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }
    public function auto_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = AutoSubscription::where('id',$id)->first();
        return view('admin.autos.edit_auto_subscription',compact('edsa_subscription','users_seller'));
    }
    public function auto_subscription_update(Request $request){
        $subscription = AutoSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function auto_subscription_delete($id){
        // return $id;
        $subscription = AutoSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }


    // realestate functions



    public function real_subscription(){
        $users_seller = [];
        $dstv = RealestateSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.real.real_subscription',compact('dstv','users_seller'));
    }
    public function real_subscription_store(Request $request){
        // return $request;
        $subscription = new RealestateSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }
    public function real_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = RealestateSubscription::where('id',$id)->first();
        return view('admin.real.edit_real_subscription',compact('edsa_subscription','users_seller'));
    }
    public function real_subscription_update(Request $request){
        $subscription = RealestateSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function real_subscription_delete($id){
        // return $id;
        $subscription = RealestateSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }


    // sacton function


    public function sacton_subscription(){
        $users_seller = [];
        $dstv = SactonSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.sacton.sacton_subscription',compact('dstv','users_seller'));
    }
    public function sacton_subscription_store(Request $request){
        // return $request;
        $subscription = new SactonSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }
    public function sacton_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = SactonSubscription::where('id',$id)->first();
        return view('admin.sacton.edit_sacton',compact('edsa_subscription','users_seller'));
    }
    public function sacton_subscription_update(Request $request){
        $subscription = SactonSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function sacton_subscription_delete($id){
        // return $id;
        $subscription = SactonSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }


    // state news subscription
    public function state_subscription(){
        $users_seller = [];
        $dstv = StateNewsSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.state.state_subscription',compact('dstv','users_seller'));
    }
    public function state_subscription_store(Request $request){
        // return $request;
        $subscription = new StateNewsSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }
    public function state_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = StateNewsSubscription::where('id',$id)->first();
        return view('admin.state.edit_state',compact('edsa_subscription','users_seller'));
    }
    public function state_subscription_update(Request $request){
        $subscription = StateNewsSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function state_subscription_delete($id){
        // return $id;
        $subscription = StateNewsSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }


// national news 


public function national_subscription(){
    $users_seller = [];
    $dstv = NationalNewsSubscription::all();
    $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
    
    foreach ($users as $user) {
        $users_seller[$user->id] = $user->name;
    }
    return view('admin.national.national_subscription',compact('dstv','users_seller'));
}
public function national_subscription_store(Request $request){
    // return $request;
    $subscription = new NationalNewsSubscription();
    $subscription->user_id = $request->users;
    $subscription->role = $request->role;
    $subscription->go_plan_monthly = $request->go_plan_monthly;
    $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
    $subscription->individual_plan_monthly = $request->individual_plan_monthly;
    $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
    $subscription->business_plan_monthly = $request->business_plan_monthly;
    $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
    $subscription->save();
    return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
}
public function national_subscription_edit($id){
    // return $id;
    $users_seller = [];
    $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
    
    foreach ($users as $user) {
        $users_seller[$user->id] = $user->name;
    }
    $edsa_subscription = NationalNewsSubscription::where('id',$id)->first();
    return view('admin.national.edit_national',compact('edsa_subscription','users_seller'));
}
public function national_subscription_update(Request $request){
    $subscription = NationalNewsSubscription::where('id',$request->subscription_id)->first();
    $subscription->user_id = $request->users;
    $subscription->role = $request->role;
    $subscription->go_plan_monthly = $request->go_plan_monthly;
    $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
    $subscription->individual_plan_monthly = $request->individual_plan_monthly;
    $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
    $subscription->business_plan_monthly = $request->business_plan_monthly;
    $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
    $subscription->save();
    return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
}
public function national_subscription_delete($id){
    // return $id;
    $subscription = NationalNewsSubscription::where('id',$id)->first();
    $subscription->delete();
    return redirect()->back();
}

public function chat_a_ride(){
    return view('admin.national.chatRide');
}
public function movie_doc(){
    return view('admin.national.movieDoc');
}

public function breaking_national_news(Request $request){
    

    // Assuming you have a BreakingNews model
    $breakingNews = new BreakingNews();
    $breakingNews->seller_id = $request->seller_id;
    $breakingNews->breaking_news = $request->addition_desc;
    $breakingNews->save();

    return response()->json(['success' => true, 'message' => 'Breaking news submitted successfully.']);
}
    public function notice(){
        $user_id = Auth::user()->id;
        $users_seller = [];
        $dstv = PublicNotice::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.online.publicNotice.public_notice',compact('dstv','users_seller','user_id'));
    }

    public function notice_store(Request $request){
        // return $request;
        $validatedData = $request->validate([
            'notice' => 'required|string|max:255',
            'acronym' => 'required|string|max:255',
            'description' => 'required',
          
        ]);
        $notice = new PublicNotice();
        $notice->notice = $request->notice;
        $notice->acronym = $request->acronym;
        $notice->description = $request->description;
        $notice->user_id = Auth::user()->id;
        // $notice->save();
        if ($request->hasFile('one_page')) {
            $file = $request->file('one_page');
            $path = $file->store('notice_one_page', 'public');
            $notice->one_page = $path;
         }
         

        // dd($notice);
        $notice->save();
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generate a unique filename
                $filename = time() . '_' . $image->getClientOriginalName();
                // Store the image in the 'public/notice_images' directory
                $image->storeAs('public/notice_images', $filename);
    
                // Save image data in the NoticeImage model
                $noticeImage = new NoticeImage();
                $noticeImage->notice_id = $notice->id;
                $noticeImage->image = 'notice_images/' . $filename;
                $noticeImage->save();
            }
        }
        return back()->with('success', 'Notice added successfully.');
    }

    public function notice_delete($id){
        $notice = PublicNotice::where('id',$id)->first();
        $notice->delete();
        return back()->with('success', 'Notice deleted successfully.');
    }


    // notice function

    public function notice_subscription(){
        $users_seller = [];
        $dstv = NoticeSubscription::all();
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        return view('admin.noticeSubscription.notice_subscription',compact('dstv','users_seller'));
    }
    public function notice_subscription_store(Request $request){
        // return $request;
        $subscription = new NoticeSubscription();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan save sucessfully.');
    }
    public function notice_subscription_edit($id){
        // return $id;
        $users_seller = [];
        $users = User::where('is_seller', 0)->where('is_edsa_agent', 1)->get();
        
        foreach ($users as $user) {
            $users_seller[$user->id] = $user->name;
        }
        $edsa_subscription = NoticeSubscription::where('id',$id)->first();
        return view('admin.noticeSubscription.edit_notice_subscription',compact('edsa_subscription','users_seller'));
    }
    public function notice_subscription_update(Request $request){
        $subscription = NoticeSubscription::where('id',$request->subscription_id)->first();
        $subscription->user_id = $request->users;
        $subscription->role = $request->role;
        $subscription->go_plan_monthly = $request->go_plan_monthly;
        $subscription->go_plan_referal_fee = $request->go_plan_referal_fee;
        $subscription->individual_plan_monthly = $request->individual_plan_monthly;
        $subscription->individual_plan_referal_fee = $request->individual_plan_referal_fee;
        $subscription->business_plan_monthly = $request->business_plan_monthly;
        $subscription->business_plan_referal_fee = $request->business_plan_referal_fee;
        $subscription->save();
        return redirect()->back()->with('success', 'Subscription plan update sucessfully.');
    }
    public function notice_subscription_delete($id){
        // return $id;
        $subscription = NoticeSubscription::where('id',$id)->first();
        $subscription->delete();
        return redirect()->back();
    }
}