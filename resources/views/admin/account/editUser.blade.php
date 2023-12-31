@extends('admin.master')
@section('title', 'Account')
@section('content')
    @include('sweetalert::alert')
    @include('admin.partial.nav')
    @include('admin.partial.aside')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->

        {{ breadcrumb('ویرایش حساب کاربری') }}

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4>ویرایش کاربر: {{ $user->username }}</h4><br>
                                @if ($errors->any())
                                    <div class="container">
                                        <div class="row alert alert-danger  justify-content-center mt-4">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <form action="{{ route('users.updateUser', ['accountId' => $account->id, 'userId' => $user->id]) }}"
                                    method="POST" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label">نام <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name"
                                                    value="{{ old('name') ?? $user->name }}" class="form-control persianletters" required
                                                    oninvalid="this.setCustomValidity('.لطفا نام را وارد کنید')"
                                                    oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label">نام خانوادگی <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="family" id="family"
                                                    value="{{ old('family') ?? $user->family }}" class="form-control persianletters" required
                                                    oninvalid="this.setCustomValidity('.لطفا نام خانوادگی را وارد کنید')"
                                                    oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label required"> موبایل <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="mobile" id="mobile"
                                                    value="{{ old('mobile') ?? $user->mobile }}" class="form-control just-numbers" required
                                                    oninvalid="this.setCustomValidity('.لطفا موبایل را وارد کنید')"
                                                    oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>

                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label required"> ایمیل <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email"
                                                    value="{{ old('email') ?? $user->email }}" class="form-control nonPersianletters" required
                                                    oninvalid="this.setCustomValidity('.لطفا ایمیل را وارد کنید')"
                                                    oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label required"> استان </label>
                                                <input type="text" name="state" class="form-control"
                                                    id="state" value="{{ old('state') ?? $user->state }}" />
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label required"> شهر </label>
                                                <input type="text" name="city" class="form-control" id="city"
                                                    value="{{ old('city') ?? $user->city }}" />
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="form-label required"> آدرس </label>
                                                <input type="text" name="address" class="form-control" id="address"
                                                    value="{{ old('address') ?? $user->address }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="form-group">
                                                <label class="form-label required"> کدپستی </label>
                                                <input type="text" name="postalcode" class="form-control just-numbers" id="postalcode"
                                                    value="{{ old('postalcode') ?? $user->postalcode }}" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-success">ذخیره</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#company_fields').hide();

            $('#account_type').change(function() {
                if ($(this).val() === 'حقوقی') {
                    $('#company_fields').show();
                } else {
                    $('#company_fields').hide();
                }
            });
        });
    </script>

<script src="{{ asset('js/validation.js') }}"></script>

@endsection
