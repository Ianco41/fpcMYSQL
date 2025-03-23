<form id="modalForm">
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="form-label">FY</div>
            <div class="input-group">
                <input type="text" class="form-control" id="fy" name="FY" required readonly>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="form-label">Month</div>
            <div class="input-group">
                <input type="text" class="form-control" id="month" name="MONTH" required readonly>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="form-label">Date</div>
            <input type="date" class="form-control" id="date" name="DATE" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="form-label">NT/NF</div>
            <div class="input-group">
                <input type="text" class="form-control" id="nt_nf" name="NT_NF" required readonly>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Category</label>
            <div class="input-group">
                <input type="text" class="form-control" id="category" name="CATEGORY" placeholder="Type at least 2 letters..." required>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Trigger</label>
            <div class="input-group">
                <input type="text" class="form-control" id="trigger" name="TRIGGER" placeholder="Type Trigger..." required>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Issue</label>
            <div class="input-group">
                <input type="text" class="form-control" id="issue" name="ISSUE" placeholder="Type Issue..." required>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Part No.</label>
            <div class="input-group">
                <input type="text" class="form-control" id="partNumber" name="PART_NO" placeholder="Type Part No..." required>
                <button class="btn btn-secondary dropdown-toggle" type="button"></button>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Part Name</label>
            <input type="text" class="form-control" id="partName" name="PRODUCT" required>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label">Lot/Sublot</label>
            <input type="text" class="form-control" id="lotSublot" name="LOT_SUBLOT" required>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3 mb-3">
            <label class="form-label">Qty-In</label>
            <input type="number" class="form-control" id="inValue" name="IN" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Qty-Out</label>
            <input type="number" class="form-control" id="outValue" name="OUT" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Reject</label>
            <input type="number" class="form-control" id="reject" name="REJECT" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Minutes</label>
            <input type="number" class="form-control" id="minutes" name="MINUTES" required>
        </div>
    </div>
</form>