<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Package;
use App\Models\Price;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxTableController extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    //  USERS
    //  Columns: #, Name, Email, Phone, Wallet, Joined, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function users(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'name',
            2 => 'email',
            3 => 'phone',
            4 => 'wallet_balance',
            5 => 'created_at',
        ];

        $query = User::orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 5] ?? 'created_at',
            strtoupper($q['order'][0]['dir'] ?? 'DESC')
        );

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $u) {
            $toggleLabel = ($u->admin || $u->sa) ? 'Suspend' : 'Activate';
            $toggleIcon = ($u->admin || $u->sa) ? 'fa-toggle-off' : 'fa-toggle-on';

            $actions = $currentUser->isAdmin()
                ? '<a href="' . route('admin.users.show', $u->id) . '" class="btn btn-xs btn-primary btn-raised btn-icon mr-1" title="View"><i class="fa fa-search"></i></a>'
                . '<a href="' . route('admin.users.edit', $u->id) . '" class="btn btn-xs btn-warning btn-raised btn-icon mr-1" title="Edit"><i class="fa fa-edit"></i></a>'
                . $this->postButton(route('admin.users.toggleStatus', $u->id), 'btn-info', $toggleIcon, "{$toggleLabel} {$u->name}?")
                . '<a href="' . route('admin.users.resetPassword', $u->id) . '" onclick="return confirm(\'Reset password of ' . addslashes($u->name) . '?\')" class="btn btn-xs btn-secondary btn-raised btn-icon mr-1" title="Reset Password"><i class="fa fa-key"></i></a>'
                . $this->deleteButton(route('admin.users.destroy', $u->id), "Delete {$u->name}?")
                : '—';

            $rows[] = [
                $i + 1,
                e($u->name),
                e($u->email),
                e($u->phone ?? '—'),
                '₦' . number_format((float) $u->wallet_balance, 2),
                $u->created_at?->format('M j, Y') ?? '—',
                $actions,
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ORDERS
    //  Columns: #, Order ID, Customer, Zone, Total, Payment, Status, Date, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function orders(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'id',
            2 => 'total',
            3 => 'status',
            4 => 'payment_status',
            5 => 'created_at',
        ];

        $query = Order::with('user')->orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 5] ?? 'created_at',
            strtoupper($q['order'][0]['dir'] ?? 'DESC')
        );

        // Support staff only sees their own user's orders; admins see all
        if (!$currentUser->isAdmin()) {
            $query->where('user_id', $userid);
        }

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('id', 'like', "%{$term}%")
                    ->orWhere('zone_name', 'like', "%{$term}%")
                    ->orWhere('address', 'like', "%{$term}%")
                    ->orWhere('status', 'like', "%{$term}%")
                    ->orWhereHas(
                        'user',
                        fn($uq) =>
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                    );
            });
        }

        if (!empty($q['status'])) {
            $query->where('status', $q['status']);
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $o) {
            $rows[] = [
                $i + 1,
                '<code>' . e(substr($o->id, 0, 8)) . '…</code>',
                e($o->user?->name ?? '—') . '<br><small class="text-muted">' . e($o->user?->email ?? '') . '</small>',
                e($o->zone_name ?? '—'),
                '₦' . number_format((float) $o->total, 2),
                $this->badge($o->payment_status, 'payment'),
                $this->badge($o->status),
                $o->created_at?->format('M j, Y') ?? '—',
                '<a href="' . route('admin.orders.show', $o->id) . '" class="btn btn-xs btn-primary btn-raised btn-icon" title="View"><i class="fa fa-search"></i></a>',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  TRANSACTIONS
    //  Columns: #, Ref, User, Type, Amount, Currency, Status, Date, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function transactions(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'tx_ref',
            2 => 'type',
            3 => 'amount',
            4 => 'status',
            5 => 'created_at',
        ];

        $query = Transaction::with('user')->orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 5] ?? 'created_at',
            strtoupper($q['order'][0]['dir'] ?? 'DESC')
        );

        if (!$currentUser->isAdmin()) {
            $query->where('user_id', $userid);
        }

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('tx_ref', 'like', "%{$term}%")
                    ->orWhere('flw_ref', 'like', "%{$term}%")
                    ->orWhere('type', 'like', "%{$term}%")
                    ->orWhere('status', 'like', "%{$term}%")
                    ->orWhereHas(
                        'user',
                        fn($uq) =>
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                    );
            });
        }

        if (!empty($q['status'])) {
            $query->where('status', $q['status']);
        }

        if (!empty($q['type'])) {
            $query->where('type', $q['type']);
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $t) {
            $rows[] = [
                $i + 1,
                '<code>' . e($t->tx_ref) . '</code>',
                e($t->user?->name ?? '—') . '<br><small class="text-muted">' . e($t->user?->email ?? '') . '</small>',
                '<span class="badge badge-secondary">' . e($t->type) . '</span>',
                '₦' . number_format((float) $t->amount, 2),
                e($t->currency ?? 'NGN'),
                $this->badge($t->status, 'payment'),
                $t->created_at?->format('M j, Y g:i A') ?? '—',
                '<a href="' . route('admin.transactions.show', $t->id) . '" class="btn btn-xs btn-primary btn-raised btn-icon" title="View"><i class="fa fa-search"></i></a>',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  NOTIFICATIONS
    //  Columns: #, Title, Body (truncated), Type, User, Read, Date, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function notifications(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'title',
            2 => 'type',
            3 => 'is_read',
            4 => 'created_at',
        ];

        $query = Notification::with('user')->orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 4] ?? 'created_at',
            strtoupper($q['order'][0]['dir'] ?? 'DESC')
        );

        // Non-admins only see their own notifications
        if (!$currentUser->isAdmin()) {
            $query->where('user_id', $userid);
        }

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('title', 'like', "%{$term}%")
                    ->orWhere('body', 'like', "%{$term}%")
                    ->orWhere('type', 'like', "%{$term}%")
                    ->orWhereHas(
                        'user',
                        fn($uq) =>
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%")
                    );
            });
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $n) {
            $readBadge = $n->is_read
                ? '<span class="badge badge-success">Read</span>'
                : '<span class="badge badge-warning">Unread</span>';

            $rows[] = [
                $i + 1,
                e($n->title),
                '<span title="' . e($n->body) . '">' . e(mb_strimwidth($n->body, 0, 60, '…')) . '</span>',
                '<span class="badge badge-secondary">' . e($n->type) . '</span>',
                e($n->user?->name ?? '—') . '<br><small class="text-muted">' . e($n->user?->email ?? '') . '</small>',
                $readBadge,
                $n->created_at?->format('M j, Y') ?? '—',
                $currentUser->isAdmin()
                ? $this->deleteButton(route('admin.notifications.destroy', $n->id), 'Delete this notification?')
                : '—',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRICES
    //  Columns: #, Item, Category, Service Type, Regular, Express, Active, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function prices(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'item_name',
            2 => 'category',
            3 => 'regular_price',
            4 => 'express_price',
            5 => 'is_active',
        ];

        $query = Price::orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 1] ?? 'item_name',
            strtoupper($q['order'][0]['dir'] ?? 'ASC')
        );

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('item_name', 'like', "%{$term}%")
                    ->orWhere('category', 'like', "%{$term}%")
                    ->orWhere('service_type', 'like', "%{$term}%");
            });
        }

        if (!empty($q['category'])) {
            $query->where('category', $q['category']);
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $p) {
            $activeBadge = $p->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';

            $rows[] = [
                $i + 1,
                e($p->item_name),
                e($p->category),
                e($p->service_type ?? '—'),
                '₦' . number_format((float) $p->regular_price, 2),
                $p->express_price ? '₦' . number_format((float) $p->express_price, 2) : '—',
                $activeBadge,
                $currentUser->isAdmin()
                ? '<a href="' . route('admin.prices.edit', $p->id) . '" class="btn btn-xs btn-warning btn-raised btn-icon mr-1" title="Edit"><i class="fa fa-edit"></i></a>'
                . $this->deleteButton(route('admin.prices.destroy', $p->id), "Delete {$p->item_name}?")
                : '—',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PACKAGES
    //  Columns: #, Name, Subtitle, Price, Featured, Active, Sort, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function packages(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'name',
            2 => 'price',
            3 => 'sort_order',
            4 => 'is_active',
        ];

        $query = Package::orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 3] ?? 'sort_order',
            strtoupper($q['order'][0]['dir'] ?? 'ASC')
        );

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where(function ($sq) use ($term) {
                $sq->where('name', 'like', "%{$term}%")
                    ->orWhere('subtitle', 'like', "%{$term}%");
            });
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $pkg) {
            $featuredBadge = $pkg->is_featured
                ? '<span class="badge badge-primary"><i class="fa fa-star"></i> Yes</span>'
                : '—';
            $activeBadge = $pkg->is_active
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            $priceCell = $pkg->old_price
                ? '<del class="text-muted">₦' . number_format((float) $pkg->old_price, 2) . '</del>&nbsp;₦' . number_format((float) $pkg->price, 2)
                : '₦' . number_format((float) $pkg->price, 2);

            $rows[] = [
                $i + 1,
                ($pkg->icon_class ? '<i class="' . e($pkg->icon_class) . ' mr-1"></i>' : '') . e($pkg->name),
                e($pkg->subtitle ?? '—'),
                $priceCell,
                $featuredBadge,
                $activeBadge,
                $pkg->sort_order,
                $currentUser->isAdmin()
                ? '<a href="' . route('admin.packages.edit', $pkg->id) . '" class="btn btn-xs btn-warning btn-raised btn-icon mr-1" title="Edit"><i class="fa fa-edit"></i></a>'
                . $this->deleteButton(route('admin.packages.destroy', $pkg->id), "Delete package {$pkg->name}?")
                : '—',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  SERVICES
    //  Columns: #, Emoji, Name, Price, Actions
    // ══════════════════════════════════════════════════════════════════════════

    public function services(Request $request, $userid)
    {
        $currentUser = User::findOrFail($userid);
        $q = $request->query();

        $columnsMap = [
            1 => 'name',
            2 => 'price',
        ];

        $query = Service::orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 1] ?? 'name',
            strtoupper($q['order'][0]['dir'] ?? 'ASC')
        );

        if (!empty($q['search']['value'])) {
            $term = $q['search']['value'];
            $query->where('name', 'like', "%{$term}%");
        }

        [$total, $items] = $this->paginate($query, $q);

        $rows = [];
        foreach ($items as $i => $s) {
            $rows[] = [
                $i + 1,
                $s->emoji ? '<span style="font-size:1.4rem;">' . e($s->emoji) . '</span>' : '—',
                e($s->name),
                '₦' . number_format((float) $s->price, 2),
                $currentUser->isAdmin()
                ? '<a href="' . route('admin.services.edit', $s->id) . '" class="btn btn-xs btn-warning btn-raised btn-icon mr-1" title="Edit"><i class="fa fa-edit"></i></a>'
                . $this->deleteButton(route('admin.services.destroy', $s->id), "Delete service {$s->name}?")
                : '—',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  QUEUE JOBS  (no userid — internal tooling)
    // ══════════════════════════════════════════════════════════════════════════

    public function jobs(Request $request)
    {
        $q = $request->query();

        $columnsMap = [0 => 'id', 1 => 'queue', 2 => 'attempts', 3 => 'created_at'];

        $query = DB::table('jobs')->orderBy(
            $columnsMap[$q['order'][0]['column'] ?? 0] ?? 'id',
            strtoupper($q['order'][0]['dir'] ?? 'DESC')
        );

        if (!empty($q['search']['value'])) {
            $search = $q['search']['value'];
            $query->where(function ($sq) use ($search) {
                $sq->where('queue', 'like', "%{$search}%")
                    ->orWhere('payload', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            });
        }

        $total = $query->count();
        $limit = max(1, intval($q['length'] ?? 10));
        $offset = max(0, intval($q['start'] ?? 0));
        $jobs = $query->limit($limit)->offset($offset)->get();

        $rows = [];
        foreach ($jobs as $job) {
            $rows[] = [
                $job->id,
                e($job->queue),
                $job->attempts,
                date('D, M j, g:i A', $job->created_at),
                '<pre style="max-width:300px;overflow:auto;font-size:11px;">'
                . e(mb_strimwidth($job->payload, 0, 200, '…')) . '</pre>',
                '<button class="btn btn-xs btn-info mr-1" onclick="viewPayload(' . $job->id . ')"><i class="fa fa-eye"></i></button>'
                . '<button class="btn btn-xs btn-danger" onclick="deleteJob(' . $job->id . ')"><i class="fa fa-trash"></i></button>',
            ];
        }

        return $this->dtResponse($q, $total, $rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PRIVATE HELPERS
    // ══════════════════════════════════════════════════════════════════════════

    private function paginate($query, array $q): array
    {
        $total = $query->count();
        $limit = max(1, intval($q['length'] ?? 10));
        $offset = max(0, intval($q['start'] ?? 0));

        return [$total, $query->limit($limit)->offset($offset)->get()];
    }

    private function dtResponse(array $q, int $total, array $rows)
    {
        return response()->json([
            'draw' => intval($q['draw'] ?? 1),
            'recordsTotal' => $total,
            'recordsFiltered' => $total,
            'data' => $rows,
        ]);
    }

    private function postButton(string $action, string $btnClass, string $icon, string $confirm = ''): string
    {
        $onConfirm = $confirm ? ' onclick="return confirm(\'' . addslashes($confirm) . '\')"' : '';

        return '<form action="' . $action . '" method="POST" style="display:inline;">'
            . csrf_field()
            . '<button type="submit"' . $onConfirm . ' class="btn btn-xs ' . $btnClass . ' btn-raised btn-icon mr-1">'
            . '<i class="fa ' . $icon . '"></i></button></form>';
    }

    private function deleteButton(string $action, string $confirm = 'Are you sure?'): string
    {
        return '<form action="' . $action . '" method="POST" style="display:inline;">'
            . csrf_field() . method_field('DELETE')
            . '<button type="submit" onclick="return confirm(\'' . addslashes($confirm) . '\')"'
            . ' class="btn btn-xs btn-danger btn-raised btn-icon" title="Delete">'
            . '<i class="fa fa-trash"></i></button></form>';
    }

    private function badge(string $value, string $context = 'order'): string
    {
        $map = $context === 'payment'
            ? [
                'successful' => 'badge-success',
                'pending' => 'badge-warning',
                'failed' => 'badge-danger',
                'cancelled' => 'badge-secondary',
            ]
            : [
                'delivered' => 'badge-success',
                'processing' => 'badge-info',
                'picked_up' => 'badge-info',
                'confirmed' => 'badge-primary',
                'ready' => 'badge-primary',
                'pending' => 'badge-warning',
                'cancelled' => 'badge-danger',
            ];

        return '<span class="badge ' . ($map[$value] ?? 'badge-secondary') . '">'
            . e(ucfirst(str_replace('_', ' ', $value))) . '</span>';
    }
}
