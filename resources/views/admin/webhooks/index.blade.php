<x-app-layout>
    <x-slot name="title">Webhook Logs</x-slot>

    <section class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">Flutterwave Webhook Logs</h4>
                    <div class="d-flex gap-2">
                        <span class="badge badge-success">{{ $counts['processed'] }} processed</span>
                        <span class="badge badge-warning text-dark">{{ $counts['pending'] }} pending</span>
                        <span class="badge badge-danger">{{ $counts['failed'] }} failed</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Event</th>
                                    <th>TX Ref</th>
                                    <th>Status</th>
                                    <th>Attempts</th>
                                    <th>Processed At</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                <tr>
                                    <td class="text-nowrap" style="font-size:13px">
                                        {{ $log->created_at->format('M j, H:i:s') }}
                                    </td>
                                    <td>
                                        <code style="font-size:12px">{{ $log->event }}</code>
                                    </td>
                                    <td>
                                        <span style="font-size:12px;font-family:monospace">
                                            {{ $log->tx_ref }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $badge = match($log->status) {
                                                'processed'  => 'success',
                                                'processing' => 'info',
                                                'pending'    => 'warning',
                                                'failed'     => 'danger',
                                                default      => 'secondary',
                                            };
                                        @endphp
                                        <span class="badge badge-{{ $badge }}">{{ $log->status }}</span>
                                    </td>
                                    <td class="text-center">{{ $log->attempts }}</td>
                                    <td style="font-size:12px">
                                        {{ $log->processed_at?->format('M j, H:i:s') ?? '—' }}
                                    </td>
                                    <td>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            data-toggle="modal"
                                            data-target="#payload-{{ $log->id }}">
                                            Payload
                                        </button>
                                    </td>
                                </tr>

                                {{-- Payload modal --}}
                                <div class="modal fade" id="payload-{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Payload — {{ $log->tx_ref }}
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @if($log->error)
                                                <div class="alert alert-danger" style="font-size:12px">
                                                    <strong>Error:</strong> {{ $log->error }}
                                                </div>
                                                @endif
                                                <pre style="font-size:12px;background:#f8f9fa;padding:16px;border-radius:8px;overflow:auto;max-height:400px">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            </div>
                                            @if($log->status === 'failed')
                                            <div class="modal-footer">
                                                <form method="POST"
                                                    action="{{ route('admin.webhooks.retry', $log->id) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-warning">
                                                        🔄 Retry Processing
                                                    </button>
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No webhook logs yet.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($logs->hasPages())
                <div class="card-footer">
                    {{ $logs->links() }}
                </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
