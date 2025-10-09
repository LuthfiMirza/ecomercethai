@extends('layouts.admin')

@section('header', 'Add New Promo')

@section('content')
    <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
        <div class="flex flex-col gap-7.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-2xl font-bold text-black dark:text-white">Add New Promo</h3>
                <p class="text-sm font-medium">Create a new promotional campaign</p>
            </div>
            <a href="{{ localized_route('admin.promos.index') }}" class="btn-outline rounded-full">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7 7-7m-7 7h18"/></svg>
                Back to Promos
            </a>
        </div>

        <div class="mt-6 form-card">
            <form action="{{ localized_route('admin.promos.store') }}" method="POST">
                @csrf
                <div class="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label>Promo Code <span class="text-rose-500">*</span></label>
                            <div class="input-icon no-icon">
                              <input name="code" value="{{ old('code') }}" required type="text" placeholder="Enter promo code">
                            </div>
                        </div>
                        <div>
                            <label>Discount Type <span class="text-rose-500">*</span></label>
                            <div class="input-icon no-icon">
                              <select name="discount_type" required>
                                <option value="percent" @selected(old('discount_type')==='percent')>Percentage</option>
                                <option value="flat" @selected(old('discount_type')==='flat')>Fixed Amount</option>
                              </select>
                            </div>
                        </div>
                        <div>
                            <label>Discount Value <span class="text-rose-500">*</span></label>
                            <div class="input-icon no-icon">
                              <input name="discount_value" value="{{ old('discount_value') }}" required type="number" step="0.01" placeholder="Enter discount value">
                            </div>
                        </div>
                        <div>
                            <label>Usage Limit</label>
                            <div class="input-icon no-icon">
                              <input name="usage_limit" value="{{ old('usage_limit') }}" type="number" placeholder="Enter usage limit (optional)">
                            </div>
                        </div>
                        <div>
                            <label>Start Date</label>
                            <div class="input-icon no-icon">
                              <input name="starts_at" type="date" value="{{ old('starts_at') }}">
                            </div>
                        </div>
                        <div>
                            <label>End Date</label>
                            <div class="input-icon no-icon">
                              <input name="ends_at" type="date" value="{{ old('ends_at') }}">
                            </div>
                        </div>
                        <div>
                            <label>Status</label>
                            <div class="input-icon no-icon">
                              <select name="status">
                                <option value="active" @selected(old('status')==='active')>Active</option>
                                <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
                              </select>
                            </div>
                            <div class="mt-3 flex items-center gap-2">
                              <input id="remember" type="checkbox" class="check-modern">
                              <label for="remember" class="!mb-0">Mark as featured promo</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ localized_route('admin.promos.index') }}" class="btn-outline">Cancel</a>
                        <button type="submit" class="btn-primary">Save Promo</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
