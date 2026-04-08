<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="icon" type="image/png" href="/uitm.png">

  <title inertia>{{ config('app.name', 'E-Tumpang') }}</title>
 
  <!-- Load Vite's entry file that boots the app -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Inertia manages page <head> content -->
  @inertiaHead
</head>
<body>
  <!-- Inertia mounts the app here -->
  @inertia
</body>
</html>
