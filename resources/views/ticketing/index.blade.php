@extends('layouts.app')
@section('titlepage', 'Ticketing Dashboard')

@section('content')
@section('navigasi')
    <span>Ticketing Dashboard</span>
@endsection

<style>
/* === Chart visibility fix === */
canvas {
    display: block !important;
    width: 100% !important;
    height: 260px !important;
    margin: auto;
}
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="card shadow-sm mb-3">
            <div class="card-body">

                {{-- ====== FILTERS ====== --}}
                <form action="{{ route('ticketing.index') }}" method="GET" class="d-flex flex-wrap align-items-end gap-2 mb-3">

                    {{-- Start Date --}}
                    <div>
                        <label class="small text-muted fw-bold">Start Date</label>
                        <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>

                    {{-- End Date --}}
                    <div>
                        <label class="small text-muted fw-bold">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control form-control-sm">
                    </div>

                    <!-- {{-- Department --}}
                    <div>
                        <label class="small text-muted fw-bold">Department</label>
                        <select name="department" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                    {{ $dept }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Product --}}
                    <div>
                        <label class="small text-muted fw-bold">Product</label>
                        <select name="product" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($products as $prod)
                                <option value="{{ $prod }}" {{ request('product') == $prod ? 'selected' : '' }}>
                                    {{ $prod }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Type --}}
                    <div>
                        <label class="small text-muted fw-bold">Type</label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($types as $t)
                                <option value="{{ $t }}" {{ request('type') == $t ? 'selected' : '' }}>
                                    {{ $t }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="small text-muted fw-bold">Category</label>
                        <select name="category" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                    {{ $cat }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="small text-muted fw-bold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="small text-muted fw-bold">Priority</label>
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach ($priorities as $prio)
                                <option value="{{ $prio }}" {{ request('priority') == $prio ? 'selected' : '' }}>
                                    {{ $prio }}
                                </option>
                            @endforeach
                        </select>
                    </div> -->

                    {{-- Submit --}}
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-filter me-1"></i> Apply
                        </button>
                    </div>

                </form>


                {{-- ====== PERIOD SUMMARY ====== --}}
                <div class="alert alert-primary py-2 mb-3">
                    <strong>Selected Period:</strong> {{ $startDate->format('d M Y') }} – {{ $endDate->format('d M Y') }}
                </div>

                {{-- ========================================= --}}
                {{-- 6️⃣ Baseline Issue --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mt-4 mb-2">#. Baseline Issue</h5>

                {{-- 🔹 Row 1: Baseline Issue Chart + Status Mapping --}}
                <div class="row g-3 mb-4 align-items-stretch">
                    {{-- Left: Baseline Issue Chart --}}
                    <div class="col-md-6 d-flex">
                        <div class="card border-0 shadow-sm p-3 small flex-fill d-flex flex-column">
                            <h6 class="fw-bold text-secondary mb-3">Baseline Issue Tickets</h6>
                            <div class="flex-grow-1">
                                <canvas id="baselineIssueChart" style="height: 100%; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Baseline Status Mapping --}}
                    <div class="col-md-6 d-flex">
                        <div class="card border-0 shadow-sm p-3 small flex-fill d-flex flex-column">
                            <h6 class="fw-bold text-secondary mb-2">Baseline Status Mapping</h6>
                            <div class="flex-grow-1 overflow-auto">
                                <table class="table table-sm table-bordered align-middle small mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Baseline Category</th>
                                            <th>Mapped Statuses</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($chartData['baselineStatusMapping'] as $map)
                                            <tr>
                                                <td><strong>{{ $map->baseline_issue_category }}</strong></td>
                                                <td>{{ $map->statuses }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4 align-items-stretch">
                    <div class="d-flex">
                        <div class="card border-0 shadow-sm p-3 small flex-fill d-flex flex-column">
                            <h6 class="fw-bold text-secondary mb-3">Baseline Development Tickets</h6>

                            @if(empty($chartData['baselineDevTickets']))
                                <p class="text-muted small mb-0">No recurring baseline tickets found for this period.</p>
                            @else
                                <div class="table-responsive flex-grow-1 overflow-auto" style="max-height: 400px;">
                                    <table class="table table-sm table-bordered align-middle small mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Root Code</th>
                                                <th>Root Name</th>
                                                <th>Related Code</th>
                                                <th>Related Name</th>
                                                <th>UAT Date</th>
                                                <th>Updated UAT Date</th>
                                                <th>Updated Counter</th>
                                                <th>Days Passed</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($chartData['baselineDevTickets'] as $item)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $item->root_code }}</td>
                                                    <td>{{ $item->root_name }}</td>
                                                    <td class="fw-bold text-success">{{ $item->related_code }}</td>
                                                    <td>{{ $item->related_name }}</td>
                                                    <td>
                                                        @if($item->uat_date)
                                                            {{ \Carbon\Carbon::parse($item->uat_date)->format('Y-m-d') }}
                                                        @else
                                                            <span class="text-muted"></span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->updated_uat_date }}</td>
                                                    <td>{{ $item->counter }}</td>
                                                    <td>
                                                        @if(!is_null($item->days_since_end))
                                                            @if($item->days_since_end > 7)
                                                                <span class="text-danger fw-bold">{{ $item->days_since_end }} days</span>
                                                            @elseif($item->days_since_end >= 0)
                                                                <span class="text-success fw-bold">{{ $item->days_since_end }} days</span>
                                                            @else
                                                                <span class="text-muted fw-bold">{{ abs($item->days_since_end) }} days ahead</span>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>



                {{-- 🔹 Row 2: Aging Chart + Recurring Baseline Table --}}
                <div class="row g-3 mb-4 align-items-stretch">
                    {{-- Left: Baseline Tickets Aging Chart --}}
                    <div class="col-md-6 d-flex">
                        <div class="card border-0 shadow-sm p-3 small flex-fill d-flex flex-column">
                            <h6 class="fw-bold text-secondary mb-3">Baseline Tickets Aging</h6>
                            <div class="flex-grow-1">
                                <canvas id="baselineAgingChart" style="height: 100%; width: 100%;"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Recurring Baseline Tickets Table --}}
                    <div class="col-md-6 d-flex">
                        <div class="card border-0 shadow-sm p-3 small flex-fill d-flex flex-column">
                            <h6 class="fw-bold text-secondary mb-3">Recurring Baseline Tickets</h6>

                            @if($chartData['recurringBaseline']->isNotEmpty())
                                <!-- ✅ Added scrollable wrapper -->
                                <div class="overflow-auto" style="max-height: 260px;">
                                    <div class="accordion accordion-flush shadow-sm rounded" id="recurringBaselineAccordion">
                                        @foreach($chartData['recurringBaseline']->groupBy('root_code') as $root => $complaintGroup)
                                            @php
                                                $rootTicket = $complaintGroup->first();
                                            @endphp

                                            <div class="accordion-item border mb-1">
                                                <h2 class="accordion-header" id="baselineHeading{{ $loop->index }}">
                                                    <button class="accordion-button collapsed small fw-semibold text-dark d-flex justify-content-between"
                                                            type="button" data-bs-toggle="collapse"
                                                            data-bs-target="#baselineCollapse{{ $loop->index }}"
                                                            aria-expanded="false"
                                                            aria-controls="baselineCollapse{{ $loop->index }}"
                                                            style="align-items: flex-start;">
                                                        <div class="me-auto pe-2">
                                                            <span class="text-primary">{{ $rootTicket->root_code }}</span> - {{ $rootTicket->root_status }}
                                                            <small class="text-muted d-block">{{ $rootTicket->root_name }}</small>
                                                        </div>
                                                        <span class="badge bg-primary rounded-pill mt-1">{{ $complaintGroup->count() }}</span>
                                                    </button>
                                                </h2>

                                                <div id="baselineCollapse{{ $loop->index }}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="baselineHeading{{ $loop->index }}"
                                                    data-bs-parent="#recurringBaselineAccordion">
                                                    <div class="accordion-body small p-2">
                                                        <ul class="list-group list-group-flush">
                                                            @foreach($complaintGroup as $t)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <span class="fw-semibold text-dark">{{ $t->related_code }}</span><br>
                                                                        <small class="text-muted">{{ $t->related_name }}</small>
                                                                    </div>
                                                                    <span class="badge bg-light text-secondary">{{ $t->related_status }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-light border text-muted small" role="alert">
                                    No recurring baseline tickets found for this period.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


                {{-- ========================================= --}}
                {{-- 1️⃣ TICKET VOLUME & TRENDS --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mb-2">1. Ticket Volume & Trends</h5>
                <div class="row text-center mb-3 g-2">
                    @php
                        $kpis = [
                            ['Total Tickets', number_format($chartData['totalTickets']), 'bg-primary'],
                            ['Open', $chartData['openCount'].' | '.$chartData['openRate'].'%', 'bg-danger'],
                            ['In Progress', $chartData['ongoingCount'].' | '.$chartData['ongoingRate'].'%', 'bg-warning text-dark'],
                            ['Resolved', $chartData['resolvedCount'].' | '.$chartData['resolvedRate'].'%', 'bg-success text-dark'],
                            ['Closed', $chartData['closedTickets'].' | '.$chartData['closedTicketRate'].'%', 'bg-dark'],
                        ];
                    @endphp
                    @foreach($kpis as $kpi)
                        <div class="col-6 col-md" style="flex: 0 0 20%; max-width: 20%;">
                            <div class="card border-0 shadow-sm small h-100">
                                <div class="card-header {{ $kpi[2] }} text-white fw-bold py-1">
                                    {{ $kpi[0] }}
                                </div>
                                <div class="card-body py-3 d-flex align-items-center justify-content-center">
                                    <h6 class="fw-bold mb-0">{{ $kpi[1] }}</h6>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Row 1: Trend Chart (Full Width) --}}
                <div class="row g-3 mb-4">
                    <div class="col-12"><canvas id="trendChart"></canvas></div>
                </div>

                {{-- Row 2: Product & Department --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><canvas id="productChart"></canvas></div>
                    <div class="col-md-6"><canvas id="deptChart"></canvas></div>
                </div>

                {{-- Row 3: Category & Type --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><canvas id="categoryChart"></canvas></div>
                    <div class="col-md-6"><canvas id="typeChart"></canvas></div>
                </div>

                

                {{-- ========================================= --}}
                {{-- 2️⃣ SLA & RESPONSE PERFORMANCE --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mt-4 mb-2">2. SLA & Response Performance</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><canvas id="slaChart"></canvas></div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3 small">
                            <h6 class="fw-bold text-secondary mb-2">SLA Metrics</h6>
                            <p>AVG Response Time: <strong>{{ $chartData['firstResponse'] }} hrs</strong></p>
                            <p>AVG Resolution Time: <strong>{{ $chartData['resolution'] }} hrs</strong></p>
                            <p>Within SLA:<strong class="text-success"> {{ $chartData['sla']['withinCount'] }} tickets ({{ $chartData['sla']['withinPct'] }}%)</strong></p>
                            <p>Breached SLA:<strong class="text-danger">{{ $chartData['sla']['breachCount'] }} tickets ({{ $chartData['sla']['breachPct'] }}%)</strong></p>
                            <p class="text-muted mb-0">Total Resolved Tickets:<strong>{{ $chartData['sla']['totalResolved'] }}</strong></p>
                        </div>
                    </div>
                </div>

                {{-- ========================================= --}}
                {{-- 3️⃣ PRIORITY & IMPACT --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mt-4 mb-2">3. Priority & Impact</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <canvas id="priorityYTDChart" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-6"><canvas id="priorityChart"></canvas></div>
                    
                    <!-- Top 3 High Priority Issues -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary mb-3">Top 3 High Priority Open Issues</h6>

                        @if($chartData['highPriorityUnresolved']->isNotEmpty())
                            <ul class="list-group small shadow-sm rounded">
                                @foreach($chartData['highPriorityUnresolved'] as $ticket)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="fw-semibold text-dark">{{ $ticket->code }}</span><br>
                                            <small class="text-muted">{{ Str::limit($ticket->name, 100) }}</small>
                                        </div>
                                        <span class="badge bg-danger rounded-pill">
                                            High
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-light border text-muted small" role="alert">
                                No high priority open issues found for this period.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ========================================= --}}
                {{-- 4️⃣ USER SATISFACTION & ACKNOWLEDGMENT --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mt-4 mb-3">4. User Satisfaction & Acknowledgment</h5>
                <div class="row g-3 mb-4">
                    <!-- Donut Chart -->
                    <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <canvas id="ackChart" width="100%" height="100"></canvas>
                    </div>

                    <!-- Recurring Complaints List -->
                    <div class="col-md-6">
                        <h6 class="fw-bold text-secondary mb-3">Recurring Complaints</h6>

                        @if($chartData['recurringComplaints']->isNotEmpty())
                            <div class="overflow-auto" style="max-height: 260px;">
                                <div class="accordion accordion-flush shadow-sm rounded" id="recurringAccordion">
                                    @foreach($chartData['recurringComplaints']->groupBy('root_code') as $root => $complaintGroup)
                                        @php
                                            $rootTicket = $complaintGroup->first();
                                        @endphp

                                        <div class="accordion-item border mb-1">
                                            <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                                <button class="accordion-button collapsed small fw-semibold text-dark d-flex justify-content-between"
                                                    type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $loop->index }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse{{ $loop->index }}"
                                                    style="align-items: flex-start;">
                                                <div class="me-auto pe-2">
                                                    <span class="text-primary">{{ $rootTicket->root_code }}</span> – {{ $rootTicket->root_status }}
                                                    <small class="text-muted d-block">{{ $rootTicket->root_name }}</small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill mt-1">{{ $complaintGroup->count() }}</span>
                                            </button>

                                            </h2>

                                            <div id="collapse{{ $loop->index }}"
                                                class="accordion-collapse collapse"
                                                aria-labelledby="heading{{ $loop->index }}"
                                                data-bs-parent="#recurringAccordion">
                                                <div class="accordion-body small p-2">
                                                    <ul class="list-group list-group-flush">
                                                        @foreach($complaintGroup as $t)
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <span class="fw-semibold text-dark">{{ $t->related_code }}</span><br>
                                                                    <small class="text-muted">{{ $t->related_name }}</small>
                                                                </div>
                                                                <span class="badge bg-light text-secondary">{{ $t->related_status }}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-light border text-muted small" role="alert">
                                No recurring complaints found for this period.
                            </div>
                        @endif
                    </div>
                </div>


                {{-- ========================================= --}}
                {{-- 5️⃣ OUTSTANDING & AGING TICKETS --}}
                {{-- ========================================= --}}
                <h5 class="fw-bold text-primary mt-4 mb-2">5. Outstanding & Aging Tickets</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm p-3 small">
                            <canvas id="agingChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- ==== Minimal Table ==== --}}
                <!-- Table Header with CSV Download -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-secondary mb-0">Ticket List</h6>
                    <a href="{{ route('ticketing.download', ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d')]) }}" 
                    class="btn btn-success btn-sm">
                        <i class="ti ti-download me-1"></i> Download All Ticket
                    </a>
                </div>
                <div class="table-responsive mt-3">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-dark small">
                            <tr>
                                <th>Project</th>
                                <th>Type</th>
                                <th>Code</th>
                                <th>Ref Code</th>
                                <th>Name</th>
                                <th>Content</th>
                                <th>Product</th>
                                <th>Environment</th>
                                <th>Owner</th>
                                <th>PIC Dev</th>
                                <th>PIC QA</th>
                                <th>PIC SIT</th>
                                <th>PIC UAT</th>
                                <th>Status</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Created at</th>
                                <th>Waiting User Feedback At</th>
                                <th>Resolved At</th>
                                <th>Closed At</th>
                                <th>Related Tickets</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Related User</th>
                                <th>Department</th>
                                <th>Location</th>
                                <th>Root Cause</th>
                                <th>Solution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->project }}</td>
                                    <td>{{ $ticket->epic }}</td>
                                    <td>{{ $ticket->code }}</td>
                                    <td>{{ $ticket->ref_code }}</td>
                                    <td>{{ Str::limit($ticket->name, 100) }}</td>
                                    <td>{{ $ticket->content }}</td>
                                    <td>{{ $ticket->product }}</td>
                                    <td>{{ $ticket->environment }}</td>
                                    <td>{{ $ticket->owner }}</td>
                                    <td>{{ $ticket->pic_dev }}</td>
                                    <td>{{ $ticket->pic_qa }}</td>
                                    <td>{{ $ticket->pic_sit }}</td>
                                    <td>{{ $ticket->pic_uat }}</td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($ticket->status) == 'closed' ? 'success' : 'secondary' }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->type }}</td>
                                    <td>{{ $ticket->priority }}</td>
                                    <td>{{ $ticket->created_at }}</td>
                                    <td>{{ $ticket->waiting_user_feedback_at }}</td>
                                    <td>{{ $ticket->in_progress_at }}</td>
                                    <td>{{ $ticket->resolved_at }}</td>
                                    <td>{{ $ticket->related_tickets }}</td>
                                    <td>{{ $ticket->start_time }}</td>
                                    <td>{{ $ticket->end_time }}</td>
                                    <td>{{ $ticket->related_user }}</td>
                                    <td>{{ $ticket->department }}</td>
                                    <td>{{ $ticket->location }}</td>
                                    <td>{{ $ticket->root_cause }}</td>
                                    <td>{{ $ticket->solution }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                <div class="float-end small">{{ $tickets->links() }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('myscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pass PHP variable to JS as global constant
        window.chartData = @json($chartData);
    </script>
    <script src="{{ asset('assets/js/pages/ticketing/ticketing-dashboard.js') }}"></script>
@endpush
