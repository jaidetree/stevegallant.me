<?php
namespace jframe\admin;
use APP, Controller, Video, Department, VideoDepartment, TemplateResponse, RedirectResponse, JSONResponse, SimpleXMLElement;
class DepartmentsController extends Controller
{

    public function index()
    {
        @login_required();

        $departments = Department::find('all', array('order' => 'id DESC'));
        return new TemplateResponse('admin/departments/index', array( 'departments' => $departments ));
    }   

    public function add()
    {
        @login_required();
        $departments = Department::all();
        /**
         * Do we have post data?
         */
        if( $_POST ) 
        {
            /**
             * Let's validate/sanitize the post data.
             * Needs to be streamlined in some way.
             */
            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['slug'] = strip_tags($_POST['slug']) or slugify(strip_tags($_POST['title']));
            $_POST['content'] = strip_tags($_POST['content']);

            $department = new Department();
            $department->title = $_POST['title'];
            $department->slug = $_POST['slug'];
            $department->content = $_POST['content'];

            $department->save();

            send_message('success', "Department was successfully created.");

            return new RedirectResponse('admin\Departments.index');
        }
        else
        {
            /**
             * No post data so show the add form.
             */
            return new TemplateResponse('admin/departments/add');
        }
    }

    public function edit($id)
    {
        @login_required();

        $department = Department::find($id);

        if( $_POST ) 
        {
            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['slug'] = strip_tags($_POST['slug']) or slugify(strip_tags($_POST['title']));
            $_POST['content'] = strip_tags($_POST['content']);

            $department->title = $_POST['title'];
            $department->slug = $_POST['slug'];
            $department->content = $_POST['content'];

            $department->save();

            send_message('success', "Department was successfully updated.");

            return new RedirectResponse('admin\Departments.index');
        }
        else
        { 
            return new TemplateResponse('admin/departments/edit', array('department' => $department));
        }
    }

    public function delete($video_id)
    {
        @login_required();

        $department = Department::find($video_id);
        $department->delete();

        return new JSONResponse(array( 'status' => 'success', 'message' => "Department deleted."));
    }

}
?>