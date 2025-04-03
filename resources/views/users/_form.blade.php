<div class="row">
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" placeholder="Enter name"
                value="{{ isset($result) ? $result['name'] : old('name') }}" name="name" required>
            @error('name')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @else
                <div class="invalid-feedback">
                    Name is required.
                </div>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Enter email"
                value="{{ isset($result) ? $result['email'] : old('email') }}" name="email" required
                autocomplete="username">
            @error('email')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @else
                <div class="invalid-feedback">
                    Email is required.
                </div>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Password
                @if (isset($result))
                    (Leave blank to keep current)
                @else
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Enter password" value="" name="password" {{ isset($result) ? '' : 'required' }}
                autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @else
                <div class="invalid-feedback">
                    Password is required.
                </div>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Confirm Password
                @if (!isset($result))
                    <span class="text-danger">*</span>
                @endif
            </label>
            <input type="password" class="form-control @error('confirm-password') is-invalid @enderror"
                placeholder="Confirm password" value="" name="confirm-password"
                {{ isset($result) ? '' : 'required' }} autocomplete="new-password">
            @error('confirm-password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @else
                <div class="invalid-feedback">
                    Confirm password is required.
                </div>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-6">
        <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select class="form-select @error('role') is-invalid @enderror" data-choices id="choices-single-default"
                name="role" required>
                <option value="">Select role</option>
                @if (count($roles) > 0)
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}" {{ isset($userRole[$value]) ? 'selected' : '' }}>
                            {{ ucfirst($label) }}</option>
                    @endforeach
                @endif
            </select>
            @error('role')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @else
                <div class="invalid-feedback">
                    Role is required.
                </div>
            @enderror
        </div>
    </div>
</div>

@push('js')
@endpush
