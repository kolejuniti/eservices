@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <section class="bg-light py-3 py-md-4">
            <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                <div class="card border border-light-subtle rounded-3 shadow-sm">
                    <div class="card-body p-3 p-md-4 p-xl-4">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif<div class="text-center mb-1">
                        {{-- <a href="{{ route('login') }}">
                            <picture>
                                <source srcset="https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/logo/logo_edaftar.webp" type="image/webp">
                                <img src="https://ku-storage-object.ap-south-1.linodeobjects.com/urproject/images/logo/logo_edaftar.png" alt="Logo" class="img-fluid" width="120" height="120">
                            </picture>
                        </a> --}}
                    </div>                    
                    <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign in to your account</h2>
                    <form method="POST" action="{{ route('login') }}">
                    @csrf
                        <div class="row gy-2 overflow-hidden">
                        <div class="col-12">
                            <div class="form-floating mb-1">
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" id="email" value="{{ old('email') }}" placeholder="name@example.com" required autocomplete="email">
                            
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="email" class="form-label">Email</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-1">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" value="" placeholder="Password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror

                            <label for="password" class="form-label">Password</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex gap-2 justify-content-between">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" name="rememberMe" id="rememberMe">
                                <label class="form-check-label text-secondary" for="rememberMe">
                                Keep me logged in
                                </label>
                            </div>
                            {{-- <a href="#!" class="link-primary text-decoration-none">Forgot password?</a> --}}
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid my-1">
                            <button class="btn btn-primary btn-lg" type="submit">Log in</button>
                            </div>
                        </div>
                        {{-- <div class="col-12">
                            <p class="m-0 text-secondary text-center">Don't have an account? <a href="#!" class="link-primary text-decoration-none">Sign up</a></p>
                        </div> --}}
                        </div>
                    </form>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </section>
    </div>
</div>
@endsection
