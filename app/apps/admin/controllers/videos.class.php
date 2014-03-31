<?php
namespace jframe\admin;
use APP, Controller, Video, Department, VideoDepartment, TemplateResponse, RedirectResponse, JSONResponse, SimpleXMLElement;
class VideosController extends Controller
{

    public function index()
    {
        @login_required();

        $videos = Video::find('all', array('order' => 'id DESC'));
        return new TemplateResponse('admin/videos/index', array( 'videos' => $videos ));
    }   

    public function import()
    {
        @login_required();
        $departments = Department::all();
        /**
         * Do we have post data?
         */
        if( $_POST['title'] ) 
        {
            /**
             * Let's validate/sanitize the post data.
             * Needs to be streamlined in some way.
             */
            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['slug'] = strip_tags($_POST['slug']) or slugify(strip_tags($_POST['title']));
            $_POST['url'] = strip_tags($_POST['url']);
            $_POST['image'] = strip_tags($_POST['image']);
            $_POST['content'] = strip_tags($_POST['content']);

            $video = new Video();
            $video->title = $_POST['title'];
            $video->slug = $_POST['slug'];
            $video->url = $_POST['url'];
            $video->image = $_POST['image'];
            $video->content = $_POST['content'];
            $video->published_at = time();

            $video->save();

            foreach($_POST['departments'] as $department_id)
            {
                $department = new VideoDepartment();
                $department->video_id = $video->id;
                $department->department_id = $department_id;
                $department->save();
            }

            send_message('success', "Video was successfully created.");

            return new RedirectResponse('admin\videos.index');
        }
        elseif( $_POST['vimeo_url'] )
        {
            $url = strip_tags($_POST['vimeo_url']);
            $id = preg_replace('/[^0-9]+/', '', $url);
            $data = file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.xml');
            //echo htmlentities($data) . "<br /><br />";
            $xml = new SimpleXMLElement($data);

            $xvid = $xml->video[0];

            $video = new \stdClass();
            $video->title = $xvid->title;
            $video->url = $url;
            $video->slug = slugify($video->title) . '-' . $id;
            $video->image = $xvid->thumbnail_large;
            $video->content = $xvid->description;

            return new TemplateResponse('admin/videos/add', array('video' => $video, 'departments' => $departments));
        }
        else
        {
            /**
             * No post data so show the add form.
             */
            return new TemplateResponse('admin/videos/add', array( 'departments' => $departments));
        }
    }

    public function image($video_id)
    {
        @login_required();
        $video = Video::find($video_id);
        $id = preg_replace('/[^0-9]+/', '', $video->url);
        $data = file_get_contents('http://vimeo.com/api/v2/video/' . $id . '.xml');
        $xml = new SimpleXMLElement($data);

        $video->image = $xml->video[0]->thumbnail_large;
        $video->save();

        send_message('success', "Video image was successfully updated.");
        return new RedirectResponse('admin\videos.index');
    }

    public function create()
    {
       return new TemplateResponse('admin/videos/create'); 
    }

    public function edit($video_id)
    {
        @login_required();

        $video = Video::find($video_id);
        $departments = Department::all();

        if( $_POST ) 
        {
            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['slug'] = strip_tags($_POST['slug']) or slugify(strip_tags($_POST['title']));
            $_POST['url'] = strip_tags($_POST['url']);
            $_POST['image'] = strip_tags($_POST['image']);
            $_POST['content'] = strip_tags($_POST['content']);

            $video->title = $_POST['title'];
            $video->slug = $_POST['slug'];
            $video->url = $_POST['url'];
            $video->image = $_POST['image'];
            $video->content = $_POST['content'];

            $video->save();

            foreach($_POST['departments'] as $department_id)
            {
                $department = new VideoDepartment();
                $department->video_id = $video->id;
                $department->department_id = $department_id;
                $department->save();
            }

            send_message('success', "Video was successfully updated.");

            return new RedirectResponse('admin\videos.index');
        }
        else
        { 
            return new TemplateResponse('admin/videos/edit', array('video' => $video, 'departments' => $departments));
        }
    }

    public function delete($video_id)
    {
        @login_required();

        $video = Video::find($video_id);
        FeaturedVideo::find($video_id)->delete();
        $video->delete();

        return new JSONResponse(array( 'status' => 'success', 'message' => "Video deleted."));
    }

}
?>