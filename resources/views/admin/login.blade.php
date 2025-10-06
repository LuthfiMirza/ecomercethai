<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite('resources/css/app.css')
    <style>
        body {
            background: linear-gradient(to right, #000000, #1a202c);
        }
        .login-card {
            background-color: #1a202c;
            border-radius: 1.5rem; /* rounded-xl */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5), 0 10px 10px -5px rgba(0, 0, 0, 0.3);
        }
        .login-button {
            background-color: #00ff99; /* Neon Green */
            transition: all 0.3s ease;
        }
        .login-button:hover {
            box-shadow: 0 0 15px #00ff99, 0 0 25px #00ff99, 0 0 35px #00ff99; /* Glowing effect */
        }
        .input-field {
            background-color: #2d3748; /* Darker gray for input */
            border: 1px solid #4a5568;
            color: #e2e8f0;
        }
        .input-field::placeholder {
            color: #a0aec0;
        }
        .register-link {
            color: #3b82f6; /* Electric Blue */
            transition: all 0.3s ease;
        }
        .register-link:hover {
            color: #60a5fa;
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <div class="login-card p-8 rounded-xl shadow-lg w-full max-w-md">
        <div class="flex justify-center mb-6">
            <!-- Placeholder for shop logo -->
            <img src="/image/monkeyHack.png" alt="Shop Logo" class="h-20 w-20 object-contain">
        </div>
        <h2 class="text-3xl font-bold text-white text-center mb-8">Admin Login</h2>
        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-400 text-sm font-bold mb-2">Email</label>
                <div class="relative">
                    <input type="email" id="email" name="email" class="input-field shadow appearance-none rounded w-full py-3 px-4 pl-10 text-gray-200 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror" placeholder="Enter your email" value="{{ old('email') }}">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.5a2.5 2.5 0 00-5 0V12"></path></svg>
                    </div>
                </div>
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-400 text-sm font-bold mb-2">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" class="input-field shadow appearance-none rounded w-full py-3 px-4 pl-10 text-gray-200 mb-3 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror" placeholder="Enter your password">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-gray-400 text-sm">
                    <input type="checkbox" name="remember" class="form-checkbox mr-2 h-4 w-4 text-green-500 transition duration-150 ease-in-out">
                    Remember Me
                </label>
                <a href="#" class="text-sm text-blue-400 hover:text-blue-300 transition duration-300 ease-in-out">Forgot Password?</a>
            </div>
            <button type="submit" class="login-button text-gray-900 font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline w-full text-lg">
                Login
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-400">
                Don't have an account? 
                <a href="{{ route('register') }}" class="register-link font-medium">Register here</a>
            </p>
        </div>
    </div>
</body>
</html>