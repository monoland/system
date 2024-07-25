<?php

namespace Module\System\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Process\Exceptions\ProcessFailedException;
use Illuminate\Support\Facades\Gate;
use Module\System\Models\SystemModule;
use Module\System\Http\Resources\ModuleCollection;
use Module\System\Http\Resources\ModuleShowResource;
use Monoland\Platform\Services\PlatformModulesGit;

class SystemModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        Gate::authorize('view', SystemModule::class);

        return new ModuleCollection(
            SystemModule::applyMode($request->trashed)
                ->filter($request->filters)
                ->search($request->findBy)
                ->sortBy($request->sortBy)
                ->paginate($request->itemsPerPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Gate::authorize('create', SystemModule::class);

        $request->validate([]);

        return SystemModule::storeRecord($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Module\System\Models\SystemModule $systemModule
     * @return \Illuminate\Http\Response
     */
    public function show(SystemModule $systemModule)
    {
        Gate::authorize('show', $systemModule);

        return new ModuleShowResource($systemModule);
    }

    /**
     * Undocumented function
     *
     * @param SystemModule $systemModule
     * @return void
     */
    public function checkForUpdate(SystemModule $systemModule, PlatformModulesGit $modulesGit)
    {
        $gitAddress = $systemModule->git_address;
        $slug = $systemModule->slug;

        try {
            $modulesGit->fetchModule($slug);
            $remotes = $modulesGit->getModuleRemotesAndBranch($slug);
            $branch  = $remotes['origin'][0];

            if (env('APP_ENV', 'local') == 'local') {
                // Handling Local Mode
                $current_log = $modulesGit->getModuleCurrentLog($slug);
                $latest_log = $modulesGit->getModuleCurrentLog($slug, 'origin', $branch);

                $latest_version = $modulesGit->getModuleCurrentCommit($slug, 'origin', $branch);
                $current_version = $modulesGit->getModuleCurrentCommit($slug);

                return response()->json([
                    // true = update exists | false = its last update
                    'status' => $current_log->unix_time < $latest_log->unix_time,

                    // jika env == local, maka updated_version = last commit
                    // jika env == production, maka updated_version = last tag
                    'current_version' => $current_version,
                    'updated_version' => $latest_version,

                    // jika env == local, maka updated_notes = commit message
                    // jika env == production, maka updated_notes = release note
                    'updated_notes' => $latest_log->body,
                ], 200);
            } else {
                // Handling Production Mode
                $latest_tag = $modulesGit->getModuleLatestTag($slug);
                $latest_log = !is_null($latest_tag) ? $modulesGit->getModuleGitLogByRef($slug, $latest_tag) : null;

                // Handling Current Tag
                $current_log = $modulesGit->getModuleCurrentLog($slug);
                $current_tag = count($current_log->tags) > 0 ? $current_log->tags[0] : null;

                // Handling Case Dimana latest_tag ada dan current_tag
                $status = false;
                $current_version = !is_null($current_tag) ? $current_tag : 'Not Found';
                $latest_version = !is_null($latest_tag) ? $latest_tag : 'Not Found';
                $body = !is_null($latest_log) ? $latest_log->body : 'No Notes';

                if (!is_null($current_tag) && !is_null($latest_tag)) {
                    $status = $current_tag == $latest_tag;
                } else if (is_null($latest_tag) && !is_null($current_tag)) {
                    throw new Exception('Apabila current_tag ada seharusnya latest_tag ada juga dong..');
                } else if (!is_null($latest_tag) && is_null($current_tag)) {
                    $status = true;
                }

                return response()->json([
                    // true = update exists | false = its last update
                    'status' => $status,

                    // jika env == local, maka updated_version = last commit
                    // jika env == production, maka updated_version = last tag
                    'current_version' => $current_version,
                    'updated_version' => $latest_version,

                    // jika env == local, maka updated_notes = commit message
                    // jika env == production, maka updated_notes = release note
                    'updated_notes' => $body,
                ], 200);
            }
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * processUpdate function
     *
     * @param SystemModule $systemModule
     * @return void
     */
    public function processUpdate(SystemModule $systemModule, PlatformModulesGit $modulesGit)
    {
        $gitAddress = $systemModule->git_address;
        $slug = $systemModule->slug;

        try {
            // fetch and get remotes and branches
            $modulesGit->fetchModule($slug);
            $remotes = $modulesGit->getModuleRemotesAndBranch($slug);
            $branch  = $remotes['origin'][0];

            // get the latest version and checkout to it
            $latest_version = env('APP_ENV', 'local') == 'local' ? $modulesGit->getModuleCurrentCommit($slug, 'origin', $branch) : $modulesGit->getModuleCurrentTag($slug, 'origin', $branch);
            $modulesGit->checkoutModule($latest_version, 'system');

            // get current latest   
            $current_version = env('APP_ENV', 'local') == 'local' ? $modulesGit->getModuleCurrentCommit('system') : $modulesGit->getModuleCurrentTag('system');

            return response()->json([], 200);
        } catch (Exception $error) {
            return response()->json([
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Module\System\Models\SystemModule $systemModule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SystemModule $systemModule)
    {
        Gate::authorize('update', $systemModule);

        $request->validate([]);

        return SystemModule::updateRecord($request, $systemModule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Module\System\Models\SystemModule $systemModule
     * @return \Illuminate\Http\Response
     */
    public function destroy(SystemModule $systemModule)
    {
        Gate::authorize('delete', $systemModule);

        return SystemModule::deleteRecord($systemModule);
    }

    /**
     * Restore the specified resource from soft-delete.
     *
     * @param  \Module\System\Models\SystemModule $systemModule
     * @return \Illuminate\Http\Response
     */
    public function restore(SystemModule $systemModule)
    {
        Gate::authorize('restore', $systemModule);

        return SystemModule::restoreRecord($systemModule);
    }

    /**
     * Force Delete the specified resource from soft-delete.
     *
     * @param  \Module\System\Models\SystemModule $systemModule
     * @return \Illuminate\Http\Response
     */
    public function forceDelete(SystemModule $systemModule)
    {
        Gate::authorize('destroy', $systemModule);

        return SystemModule::destroyRecord($systemModule);
    }
}
