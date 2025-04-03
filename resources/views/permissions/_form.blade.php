<div class="row">
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Permission</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Permission"
                value="{{ isset($result) ? $result['name'] : old('name') }}" name="name" required>
            @error('name')
                <div class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
            <div class="invalid-feedback">
                Name is required.
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Group name</label>
            <input type="text" class="form-control @error('group_name') is-invalid @enderror"
                placeholder="Enter Group name" value="{{ isset($result) ? $result['group_name'] : old('group_name') }}"
                name="group_name" required>
            @error('group_name')
                <div class="invalid-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
            <div class="invalid-feedback">
                Group name is required.
            </div>
        </div>
    </div>
</div>
