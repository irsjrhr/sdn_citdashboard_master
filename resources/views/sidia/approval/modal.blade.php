<div class="modal fade" id="modalCreateApproval" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <form id="formCreateApproval" onsubmit="return false;" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">General Approval</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ================= STEP 1 ================= --}}
                    <div id="step-1">
                        <div class="mb-3">
                            <label>Branch</label>
                            <select name="kode_cabang" class="form-control select-branch" style="width:100%">
                                @foreach($branches as $b)
                                    <option value="{{ $b->kode_cabang }}">
                                        {{ $b->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Subject</label>
                            <textarea name="subject" class="form-control"></textarea>
                        </div>

                        <div class="mb-3">
                            <label>Category</label>
                            <select name="category_code"
                                    class="form-control select-category"
                                    style="width:100%">
                                <option value="">-- Pilih Category --</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->category_code }}">
                                        {{ $c->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="ppiTypeWrapper">
                            <label>PPI Type</label>
                            <select name="ppi_code"
                                    class="form-control select-ppi"
                                    style="width:100%">
                                <option value="">-- Pilih PPI Type --</option>
                                @foreach($ppiTypes as $p)
                                    <option value="{{ $p->ppi_code }}">
                                        {{ $p->ppi_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3 d-none" id="amountWrapper">
                            <label>Amount</label>
                            <input type="number"
                                name="amount"
                                class="form-control"
                                min="0"
                                placeholder="Masukkan nominal">
                        </div>

                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                    </div>

                    {{-- ================= STEP 2 ================= --}}
                    <div id="step-2" class="d-none">

                        {{-- APPROVER --}}
                        <h6>Choose Approver(s)</h6>
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <select class="form-control select-user" style="width:100%" id="approverUser">
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input id="approverRole" class="form-control" placeholder="Role">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-success" onclick="addApprover()">+</button>
                            </div>
                        </div>

                        <table class="table table-sm" id="approverTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                        {{-- ATTACHMENT --}}
                        <h6>Attachment</h6>
                        <input type="file" name="attachments[]" multiple class="form-control">
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="btnBack"
                            class="btn btn-secondary d-none"
                            onclick="backStep()">Back</button>

                    <button type="button" id="btnNext"
                            class="btn btn-primary"
                            onclick="nextStep()">Next</button>

                    <button type="submit" id="btnSubmit"
                            class="btn btn-primary d-none">
                        Submit
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
