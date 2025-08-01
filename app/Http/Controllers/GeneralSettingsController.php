<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\SettingsMenu;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response;

class GeneralSettingsController extends Controller
{

    public function settingsMenus(Request $request)
    {
        if (!$request->ajax()) {
            $menus = SettingsMenu::whereNull('parent_id')->get();
            $routes = collect(Route::getRoutes())->filter(function ($route) {
                // return strpos($route->getName(), 'settings.') === 0;
                return strpos($route->uri(), 'settings') === 0 && in_array('GET', $route->methods());
            });

            return view('settings.settings_menu_param', [
                'menus' => $menus,
                'routes' => $routes,
            ]);
        } else {
            $menus = SettingsMenu::all();
            return DataTables::of($menus)
                ->addColumn('parent', function ($data) {
                    if (!empty($data->parent_id)) {
                        return $data->parent->title;
                    } else {
                        return '-';
                    }
                })
                ->addColumn('action', function ($data) {
                    return "<button class='btn btn-outline-primary btn-sm edit' data-data='$data' data-bs-toggle='modal' data-bs-target='#menuModal'>
                                <i class='fa fa-pencil'></i> Edit
                            </button>
                            <button class='btn btn-outline-danger btn-sm delete'  data-data='$data'> <i class='fa fa-trash'></i> Delete</button>";
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function saveSettingsMenus(Request $request)
    {
        $request->validate([
            'title' => 'required',
        ]);

        try {
            $id = (int)SettingsMenu::max('id') + 1;

            $docs = new SettingsMenu();
            $docs->id = $id;
            $docs->title = $request->title;
            $docs->route = $request->route;
            $docs->parent_id = $request->parent_id;
            $docs->created_by = Auth::user()->user_name;
            $docs->updated_by = Auth::user()->user_name;
            $docs->save();

            return redirect()->route('settings.settingsMenus')->with('success', 'Menu added successfully');
        } catch (\Throwable $e) {
            dd($e);
            return redirect()->route('settings.settingsMenus')->with('error', 'Failed to add Menu');
        }
    }

    public function editSettingsMenus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'title' => 'required',
        ]);

        try {
            $SettingsMenus = SettingsMenu::where('id', $request->id)
                ->update([
                    'title' => $request->title,
                    'parent_id' => $request->parent_id,
                    'route' => $request->route,
                ]);

            return redirect()->route('settings.settingsMenus')->with('success', 'Menu edited successfully');
        } catch (\Throwable $e) {
            return redirect()->route('settings.settingsMenus')->with('error', 'Failed to edit Menu');
        }
    }

    public function deleteSettingsMenus(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        try {
            $SettingsMenus = SettingsMenu::where('id', $request->id)->first();
            $SettingsMenus->delete();

            return [
                'status' => Response::HTTP_OK,
                'message' => 'Item deleted successfully'
            ];
        } catch (\Throwable $e) {
            return [
                'status' => $e->getCode(),
                'message' => 'Failed to delete item'
            ];
        }
    }


    public function cDepartmentsEditData(Request $request)
    {
        try {
            $cDepartments = Department::findOrFail($request->ed_department_code);
            $cDepartments->company_id = $request->input('ed_company_id');
            $cDepartments->department_name = $request->input('ed_department_name');
            $cDepartments->save();

            return redirect('/settings/finance/cDepartments')->with('success', 'cDepartments information saved successfully');
        } catch (ModelNotFoundException $exception) {
            return redirect('/settings/finance/cDepartments')->with('error', 'Specified code was not found.');
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
