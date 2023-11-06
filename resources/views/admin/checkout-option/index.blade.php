@extends('admin.master')
@section('title', 'Product List')
@section('content')
    @include('sweetalert::alert')
    @include('admin.partial.nav')
    @include('admin.partial.aside')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        {{ breadcrumb('افزودنی های سفارش') }}
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                @if ($errors->any())
                                    <div class="container">
                                        <div class="row alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <form action="{{ route('checkout-options.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="required">عنوان <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $CheckoutOption?->title ?? old('title') }}" placeholder="نام محصول..." required  oninvalid="this.setCustomValidity('لطفا عنوان را وارد نمایید.')"
                                                       oninput="this.setCustomValidity('')">
                                                <input type="hidden" name="id" value="{{ $CheckoutOption?->id ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label class="required">توضیحات <span class="text-danger">*</span></label>
                                                <input type="text" name="details" class="form-control" value="{{ $CheckoutOption?->details ?? old('details') }}" placeholder="توضیحات..." required  oninvalid="this.setCustomValidity('لطفا توضیحات را وارد نمایید.')"
                                                       oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>قیمت اصلی <span class="text-danger">*</span></label>
                                                <input type="text" name="real_price" class="form-control just-numbers" value="{{ $CheckoutOption?->real_price ?? old('real_price') }}" placeholder="قیمت اصلی..." required oninvalid="this.setCustomValidity('لطفا قیمت را وارد  نمایید.')"
                                                       oninput="this.setCustomValidity('')">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>قیمت تخفیف خورده</label>
                                                <input type="text" name="off_price" class="form-control just-numbers" value="{{ $CheckoutOption?->off_price ?? old('off_price') }}" placeholder="قیمت تخفیف خورده...">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>حداقل مبلغ سبد</label>
                                                <input type="text" name="min_price" class="form-control just-numbers" value="{{ $CheckoutOption?->min_price ?? old('min_price') }}" placeholder="حداقل مبلغ...">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label>وضعیت نمایش</label>
                                                <select name="show_status" class="form-control">
                                                    <option {{ ($CheckoutOption?->show_status == '1') ? 'selected' : (old('show_status') == '1' ? 'selected' : '') }} value="1">فعال</option>
                                                    <option {{ ($CheckoutOption?->show_status == '0') ? 'selected' : (old('show_status') == '0' ? 'selected' : '') }} value="0">غیرفعال</option>
                                                </select>
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                @if ($checkoutOptions->count() > 0)
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>عنوان</th>
                                            <th>توضیحات</th>
                                            <th>مبلغ با تخفیف</th>
                                            <th>مبلغ اصلی</th>
                                            <th>حداقل خرید</th>
                                            <th>عملیات</th>
                                        </tr>
                                        @foreach ($checkoutOptions as $checkoutOption)
                                            <tr>
                                                <td>{{ fa_number($checkoutOption->index + 1) }}</td>
                                                <td>{{ $checkoutOption->title }}</td>
                                                <td>{{ $checkoutOption->details }}</td>
                                                <td>{{ fa_number($checkoutOption->off_price) }}</td>
                                                <td>{{ fa_number($checkoutOption->real_price) }}</td>
                                                <td>{{ fa_number($checkoutOption->min_price) }}</td>
                                                <td class="d-flex">
                                                    <a href="{{ route('checkout-options.edit', $checkoutOption) }}"
                                                       class="btn btn-warning m-1">ویرایش</a>
                                                    <form action="{{ route('checkout-options.destroy', $checkoutOption) }}" method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-danger m-1" id="confirmdelete{{ $checkoutOption->id }}">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                @else
                                    <div class="alert alert-danger text-center">
                                        موردی جهت نمایش موجود نیست.
                                    </div>
                                @endif
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


@endsection
