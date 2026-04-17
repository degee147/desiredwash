<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AppContextService;
use App\Traits\TraitsManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AjaxTableController extends Controller
{
    use TraitsManager;

    protected $context;
    // protected $currentUser;

    // Constructor injection
    public function __construct(AppContextService $contextService)
    {
        $this->context = $contextService->getContext();
        // $this->currentUser = Auth::user();
    }

    public function users(Request $request, $userid)
    {
        $cleanQuery = $request->query();
        $refcode = $cleanQuery['refcode'] ?? '';

        $order = $cleanQuery['order'][0]['column'] ?? 'created_at';
        $orderType = strtoupper($cleanQuery['order'][0]['dir'] ?? 'DESC');
        $columnsMap = [
            1 => 'email',
            2 => DB::raw('CAST(last_active_time AS DECIMAL(18,6))'),
            3 => 'firstname',
            5 => 'created_at',
        ];
        $orderBy = $columnsMap[$order] ?? 'id';

        $currentUser = $this->getUser($userid);

        $query = User::orderBy($orderBy, $orderType);

        if (!empty($refcode)) {
            $query->where('referer', $refcode);
        }

        if (!empty($cleanQuery['search']['value'])) {
            $search_term = $this->escapeString($cleanQuery['search']['value']);
            $query->where(function ($q) use ($search_term) {
                foreach ($this->userFullTextQuery($search_term) as $where) {
                    $q->orWhere($where);
                }
            });
        }

        $iTotalRecords = $query->count();

        $limit = intval($cleanQuery['length'] ?? 10);
        $offset = intval($cleanQuery['start'] ?? 0);
        $query->limit($limit)->offset($offset);

        $itemsArray = $query->get();

        $response["data"] = [];
        $index = $offset;

        foreach ($itemsArray as $item) {
            // $membership = $this->getActiveSubscription($item->id);
            $id = ($index + 1);
            $data_array = [];
            $data_array[] = $id;
            $data_array[] = $item->email . "<br>$" . number_format($this->getUserBalance($item->id)['available'], 2) . ($currentUser->sa ? " (" . ($item->is_live ? "Live" : "Test") . ")" : '');
            $data_array[] = lastSeen($item->last_login_at);
            $data_array[] = $item->name;
            $data_array[] = !empty($membership) ? $membership->plan->name : "Free";
            $data_array[] = $this->readableTimestamp(strtotime($item->created_at));
            $data_array[] = $this->showUserRoles($item);
            if ($currentUser->sa) {
                $data_array[] = !empty($item->today) ? "$" . number_format($item->today, 4) : '';
                $data_array[] = !empty($item->month) ? "$" . number_format($item->month, 4) : '';
                $data_array[] =
                    '<a href="' . route('autopilot.users.view', $item->id) . '" class="btn btn-xs btn-primary btn-raised btn-icon mr-1"  title="View "><i class="fa fa-search"></i></a>' .
                    '<a href="' . route('autopilot.users.edit', $item->id) . '" class="btn btn-xs btn-raised btn-warning btn-icon mr-1"  title="Edit User" ><i class="fa fa-edit"></i></a> ' .
                    $this->userStatusAndEmailLink($item) .
                    '<a title="Reset Password" href="' . route('autopilot.users.resetPassword', $item->id) . '" onclick="return confirm(\'Reset Password of ' . $item->name . '?\')" class="btn btn-xs btn btn-raised btn-icon btn-warning"><i class="fa fa-key"></i></a>&nbsp;' .
                    '<a title="Delete" href="' . route('autopilot.users.delete', $item->id) . '" onclick="return confirm(\'Are you sure you want to delete ' . $item->name . '?\')" class="btn btn-xs btn btn-raised btn-icon btn-danger"><i class="fa fa-trash"></i></a>';
            }
            $response["data"][] = $data_array;
            $index++;
        }

        if (isset($cleanQuery["customActionType"]) && $cleanQuery["customActionType"] == "group_action") {
            $response["customActionStatus"] = "OK";
            $response["customActionMessage"] = "Group action successfully has been completed. Well done!";
        }

        $response["draw"] = intval($cleanQuery['draw'] ?? 1);
        $response["recordsTotal"] = $iTotalRecords;
        $response["recordsFiltered"] = $iTotalRecords;
        $response["request"] = $cleanQuery;

        if (!empty($this->context['userBalance'])) {
            $response['available'] = number_format($this->context['userBalance']['available'], 2);
            $response['onorder'] = number_format($this->context['userBalance']['onorder'], 2);
        }
        return response()->json($response);
    }

    public function jobs(Request $request)
    {
        $cleanQuery = $request->query();

        // DataTables ordering
        $orderColumnIndex = $cleanQuery['order'][0]['column'] ?? 0;
        $orderDir = strtoupper($cleanQuery['order'][0]['dir'] ?? 'DESC');

        $columnsMap = [
            0 => 'id',
            1 => 'queue',
            2 => 'attempts',
            3 => 'reserved_at',
            4 => 'available_at',
            5 => 'created_at',
        ];

        $orderBy = $columnsMap[$orderColumnIndex] ?? 'id';

        $query = DB::table('jobs')->orderBy($orderBy, $orderDir);

        /**
         * Search
         */
        if (!empty($cleanQuery['search']['value'])) {
            $search = $cleanQuery['search']['value'];

            $query->where(function ($q) use ($search) {
                $q->where('queue', 'like', "%{$search}%")
                    ->orWhere('payload', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        /**
         * Total count
         */
        $iTotalRecords = $query->count();

        /**
         * Pagination
         */
        $limit = intval($cleanQuery['length'] ?? 10);
        $offset = intval($cleanQuery['start'] ?? 0);

        $jobs = $query
            ->limit($limit)
            ->offset($offset)
            ->get();

        /**
         * Build response rows
         */
        $response['data'] = [];
        $index = $offset;

        foreach ($jobs as $job) {
            $data = [];

            // $data[] = $index + 1;
            $data[] = $job->id;
            $data[] = $job->queue;
            $data[] = $job->attempts;

            // $data[] = $job->reserved_at
            //     ? date('Y-m-d H:i:s', $job->reserved_at)
            //     : '-';

            // $data[] = date('Y-m-d H:i:s', $job->available_at);
            // $data[] = date('Y-m-d H:i:s', $job->created_at);
            $data[] = date('D, M j, g:i A', $job->created_at);
            $data[] = $job->payload;

            // Optional action buttons
            $data[] =
                '<button class="btn btn-xs btn-info" onclick="viewPayload(' . $job->id . ')">
                <i class="fa fa-eye"></i>
             </button>
             <button class="btn btn-xs btn-danger" onclick="deleteJob(' . $job->id . ')">
                <i class="fa fa-trash"></i>
             </button>';

            $response['data'][] = $data;
            $index++;
        }

        /**
         * DataTables meta
         */
        $response['draw'] = intval($cleanQuery['draw'] ?? 1);
        $response['recordsTotal'] = $iTotalRecords;
        $response['recordsFiltered'] = $iTotalRecords;
        $response['request'] = $cleanQuery;

        return response()->json($response);
    }

    private function userStatusAndEmailLink($user)
    {
        $action = $user->active ? 'Suspend' : 'Activate';
        $icon = $user->active ? 'fa-toggle-off' : 'fa-toggle-on';

        return '
        <form action="' . route('autopilot.users.toggleStatus', $user->id) . '" method="POST" style="display:inline;">
            ' . csrf_field() . '
            <button type="submit" onclick="return confirm(\'' . $action . ' ' . $user->name . '?\')"
                class="btn btn-xs btn-raised btn-icon btn-info">
                <i class="fa ' . $icon . '"></i>
            </button>
        </form>
    ';
    }


}
